<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Zona;
use App\Models\Municipio;
use App\Models\Nucleo;
use App\Models\Linea;
use App\Models\Parada;
use App\Models\PuntoVenta;
use App\Models\Horario;
use App\Models\Frecuencia;
use App\Models\TarifaInterurbana;


//use App\Models\Tarifa;
use Illuminate\Support\Facades\DB;

class ApiService implements ApiServiceInterface
{
    protected $baseUrl = 'https://api.ctan.es/v1/Consorcios/9';

    public function syncZonas(): int
    {
        $response = Http::get("{$this->baseUrl}/zonas");

        if (!$response->successful()) {
            Log::error("Error al obtener zonas: " . $response->status());
            return 0;
        }

        $data = $response->json();

        // Logging para depuración
        Log::info("Respuesta de zonas:", ['body' => $data]);

        // Si la respuesta está anidada bajo 'data' o 'zonas', úsala
        $zonas = $data['zonas'] ?? $data['data'] ?? $data;

        $count = 0;
        foreach ($zonas as $zona) {
            if (!isset($zona['idZona'])) {
                Log::warning("Zona sin idZona", ['zona_problematica' => $zona]);
                continue;
            }
            Zona::updateOrCreate(
                ['id_zona' => $zona['idZona']],
                [
                    'nombre' => $zona['nombre'] ?? 'Sin nombre',
                    'color' => $zona['color'] ?? null
                ]
            );
            $count++;
        }
        return $count;
    }

    public function syncMunicipios(): int
    {
        $response = Http::get("{$this->baseUrl}/municipios");

        if (!$response->successful()) {
            Log::error("Error al obtener municipios: " . $response->status());
            return 0;
        }

        $data = $response->json();

        // Extraer correctamente el array de municipios
        $municipios = $data['municipios'] ?? $data['data'] ?? $data;

        $count = 0;
        foreach ($municipios as $municipio) {
            // Ahora $municipio es un municipio individual
            if (!isset($municipio['idMunicipio'])) {
                Log::warning("Municipio individual sin idMunicipio", ['municipio_problematico' => $municipio]);
                continue;
            }
            Municipio::updateOrCreate(
                ['id_municipio' => $municipio['idMunicipio']],
                ['nombre' => $municipio['datos'] ?? 'Sin nombre']
            );
            $count++;
        }
        return $count;
    }

    public function syncNucleos(): int
    {
        $response = Http::get("{$this->baseUrl}/nucleos");

        if (!$response->successful()) {
            Log::error("Error al obtener nucleos: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $nucleos = $data['nucleos'] ?? $data['data'] ?? $data;

        $count = 0;
        foreach ($nucleos as $nucleo) {
            if (!isset($nucleo['idNucleo'])) {
                Log::warning("Nucleo sin idNucleo", ['nucleo_problematico' => $nucleo]);
                continue;
            }
            Nucleo::updateOrCreate(
                ['id_nucleo' => $nucleo['idNucleo']],
                [
                    'id_municipio' => $nucleo['idMunicipio'] ?? null,
                    'id_zona' => $nucleo['idZona'] ?? null,
                    'nombre' => $nucleo['nombre'] ?? 'Sin nombre'
                ]
            );
            $count++;
        }
        return $count;
    }

    public function syncLineas(): int
    {
        $response = Http::get("{$this->baseUrl}/lineas");

        if (!$response->successful()) {
            Log::error("Error al obtener lineas: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $lineas = $data['lineas'] ?? $data['data'] ?? $data;

        $count = 0;
        foreach ($lineas as $linea) {
            if (!isset($linea['idLinea'])) {
                Log::warning("Línea sin idLinea, se omite: ", ['linea_problematica' => $linea]);
                continue;
            }

            $detalleResponse = Http::get("{$this->baseUrl}/lineas/{$linea['idLinea']}");
            if (!$detalleResponse->successful()) {
                Log::error("Error al obtener detalle de línea {$linea['idLinea']}: " . $detalleResponse->status());
                continue;
            }

            $detalle = $detalleResponse->json();
            if (!isset($detalle['idLinea'])) {
                Log::warning("Detalle de línea sin idLinea, se omite: ", ['detalle_problematico' => $detalle]);
                continue;
            }

            Linea::updateOrCreate(
                ['id_linea' => $detalle['idLinea']],
                [
                    'codigo' => $detalle['codigo'] ?? '',
                    'nombre' => $detalle['nombre'] ?? '',
                    'modo' => $detalle['modo'] ?? '',
                    'operadores' => $detalle['operadores'] ?? '',
                    'hay_noticias' => $detalle['hayNoticias'] ?? 0,
                    'termometro_ida' => $detalle['termometroIda'] ?? null,
                    'termometro_vuelta' => $detalle['termometroVuelta'] ?? null,
                    'polilinea' => isset($detalle['polilinea']) ? json_encode($detalle['polilinea']) : null,
                    'color' => $detalle['color'] ?? null,
                    'tiene_ida' => $detalle['tieneIda'] ?? 1,
                    'tiene_vuelta' => $detalle['tieneVuelta'] ?? 1,
                    'pmr' => $detalle['pmr'] ?? null,
                    'concesion' => $detalle['concesion'] ?? null,
                    'observaciones' => $detalle['observaciones'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    public function syncParadas(): int
    {
        $lineas = Linea::all();
        $count = 0;

        // Limpiar tabla de paradas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('paradas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Obtener todas las paradas del endpoint general una sola vez
        $paradasGenerales = $this->obtenerParadasGenerales();

        foreach ($lineas as $linea) {
            Log::info("Procesando paradas de línea: {$linea->id_linea}");

            $responseLinea = Http::get("{$this->baseUrl}/lineas/{$linea->id_linea}/paradas");
            if (!$responseLinea->successful()) {
                Log::warning("Error obteniendo paradas de línea {$linea->id_linea}");
                continue;
            }

            $paradasLinea = $responseLinea->json()['paradas'] ?? [];

            foreach ($paradasLinea as $paradaLinea) {
                $idParada = $paradaLinea['idParada'];

                try {
                    // Verificar si la parada ya existe
                    $paradaExistente = Parada::where('id_parada', $idParada)->first();

                    if (!$paradaExistente) {
                        // Validar si es una parada válida
                        $esValida = $this->validarParada($idParada, $paradasGenerales);

                        if ($esValida) {
                            // Obtener datos completos de la parada
                            $datosParada = $this->obtenerDatosParadaCompletos($idParada, $paradaLinea, $paradasGenerales);

                            Parada::create([
                                'id_parada' => $idParada,
                                'id_nucleo' => $datosParada['id_nucleo'],
                                'id_municipio' => $datosParada['id_municipio'],
                                'id_zona' => $datosParada['id_zona'],
                                'nombre' => $datosParada['nombre'],
                                'latitud' => $datosParada['latitud'],
                                'longitud' => $datosParada['longitud'],
                                'modos' => $datosParada['modos'],
                                'observaciones' => $datosParada['observaciones'],
                            ]);

                            $count++;
                        } else {
                            Log::warning("Parada {$idParada} no es válida, se omite");
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Error procesando parada {$idParada}: " . $e->getMessage());
                }
            }
        }

        Log::info("Sincronización de paradas completada. Paradas procesadas: {$count}");
        return $count;
    }

    private function obtenerParadasGenerales(): array
    {
        $responseParadas = Http::get("{$this->baseUrl}/paradas");
        if ($responseParadas->successful()) {
            return $responseParadas->json()['paradas'] ?? [];
        }
        return [];
    }

    private function validarParada($idParada, $paradasGenerales): bool
    {
        // 1. Verificar si existe en el endpoint de servicios
        $responseServicios = Http::get("{$this->baseUrl}/paradas/{$idParada}/servicios");
        if ($responseServicios->successful()) {
            Log::info("Parada {$idParada} validada por endpoint de servicios");
            return true;
        }

        // 2. Verificar si existe en el endpoint general
        foreach ($paradasGenerales as $parada) {
            if ($parada['idParada'] === $idParada) {
                Log::info("Parada {$idParada} validada por endpoint general");
                return true;
            }
        }

        return false;
    }

    private function obtenerDatosParadaCompletos($idParada, $paradaLinea, $paradasGenerales): array
    {
        $datos = [
            'id_nucleo' => null,
            'id_municipio' => null,
            'id_zona' => null,
            'nombre' => $paradaLinea['nombre'] ?? '',
            'latitud' => $paradaLinea['latitud'] ?? 0,
            'longitud' => $paradaLinea['longitud'] ?? 0,
            'modos' => $paradaLinea['modos'] ?? '',
            'observaciones' => null,
        ];

        // 1. Intentar obtener datos del endpoint general
        foreach ($paradasGenerales as $parada) {
            if ($parada['idParada'] === $idParada) {
                $datos['id_nucleo'] = $parada['idNucleo'] ?? null;
                $datos['id_zona'] = $parada['idZona'] ?? null;
                $datos['observaciones'] = $parada['observaciones'] ?? null;

                // Obtener municipio a través de la relación con núcleo
                if ($datos['id_nucleo']) {
                    $nucleo = Nucleo::where('id_nucleo', $datos['id_nucleo'])->first();
                    if ($nucleo) {
                        $datos['id_municipio'] = $nucleo->id_municipio;
                        $datos['id_zona'] = $datos['id_zona'] ?? $nucleo->id_zona;
                    }
                }

                Log::info("Datos obtenidos del endpoint general para parada {$idParada}");

                // NUEVO: Intentar obtener observaciones del endpoint individual (más detalladas)
                try {
                    $responseParada = Http::timeout(10)->get("{$this->baseUrl}/paradas/{$idParada}");
                    if ($responseParada->successful()) {
                        $paradaData = $responseParada->json();
                        if (!isset($paradaData['error']) && isset($paradaData['observaciones'])) {
                            $datos['observaciones'] = $paradaData['observaciones'];
                            Log::info("Observaciones actualizadas desde endpoint individual para parada {$idParada}");
                        }
                    }
                } catch (\Exception $e) {
                    Log::debug("No se pudieron obtener observaciones del endpoint individual para parada {$idParada}: " . $e->getMessage());
                }

                return $datos;
            }
        }

        // 2. Si no existe en endpoint general, usar datos de línea y relaciones
        $datos['id_nucleo'] = $paradaLinea['idNucleo'] ?? null;

        if ($datos['id_nucleo']) {
            $nucleo = Nucleo::where('id_nucleo', $datos['id_nucleo'])->first();
            if ($nucleo) {
                $datos['id_municipio'] = $nucleo->id_municipio;
                $datos['id_zona'] = $nucleo->id_zona;
            }
        }

        // 3. Intentar obtener observaciones del endpoint individual
        try {
            $responseParada = Http::get("{$this->baseUrl}/paradas/{$idParada}");
            if ($responseParada->successful()) {
                $paradaData = $responseParada->json();
                if (!isset($paradaData['error'])) {
                    $datos['observaciones'] = $paradaData['observaciones'] ?? null;
                    Log::info("Observaciones obtenidas del endpoint individual para parada {$idParada}");
                }
            }
        } catch (\Exception $e) {
            Log::debug("Error consultando endpoint individual para parada {$idParada}: " . $e->getMessage());
        }

        if (!$datos['observaciones']) {
            $datos['observaciones'] = 'Parada solo disponible en líneas específicas';
        }

        Log::info("Datos obtenidos por relaciones para parada {$idParada}");
        return $datos;
    }


    public function syncLineaParada(): int
    {
        $lineas = Linea::all();
        $count = 0;

        // Limpiar tabla pivote
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('linea_parada')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($lineas as $linea) {
            Log::info("Procesando relaciones de línea: {$linea->id_linea}");

            $responseLinea = Http::get("{$this->baseUrl}/lineas/{$linea->id_linea}/paradas");
            if (!$responseLinea->successful()) {
                Log::warning("Error obteniendo paradas de línea {$linea->id_linea}");
                continue;
            }

            $paradasLinea = $responseLinea->json()['paradas'] ?? [];

            foreach ($paradasLinea as $paradaLinea) {
                $idParada = $paradaLinea['idParada'];

                try {
                    // Verificar que la parada existe en nuestra tabla
                    $paradaExiste = Parada::where('id_parada', $idParada)->exists();

                    if ($paradaExiste) {
                        // Determinar sentido
                        $sentido = 'ida';
                        if (isset($paradaLinea['sentido'])) {
                            $sentido = ($paradaLinea['sentido'] == 2 || $paradaLinea['sentido'] == 'vuelta') ? 'vuelta' : 'ida';
                        }

                        DB::table('linea_parada')->insert([
                            'id_linea' => $linea->id_linea,
                            'id_parada' => $idParada,
                            'orden' => $paradaLinea['orden'] ?? null,
                            'sentido' => $sentido,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $count++;
                    } else {
                        Log::warning("Parada {$idParada} no existe en tabla paradas, se omite relación");
                    }

                } catch (\Exception $e) {
                    Log::error("Error procesando relación línea-parada", [
                        'id_linea' => $linea->id_linea,
                        'id_parada' => $idParada,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info("Sincronización de relaciones completada. Relaciones procesadas: {$count}");
        return $count;
    }


    public function syncFrecuencias(): int
    {
        // Obtener frecuencias desde cualquier línea (son las mismas para todas)
        $response = Http::timeout(60)
            ->get("{$this->baseUrl}/frecuencias");

        if (!$response->successful()) {
            Log::error("Error al obtener frecuencias: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $frecuencias = $data['frecuencias'] ?? [];
        $count = 0;

        foreach ($frecuencias as $frecuencia) {
            try {
                Frecuencia::updateOrCreate(
                    ['acronimo' => $frecuencia['codigo']],
                    [
                        'id_frecuencia' => $frecuencia['idFreq'],
                        'nombre' => $frecuencia['nombre']
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                Log::error("Error procesando frecuencia: " . $e->getMessage());
            }
        }

        Log::info("Sincronización de frecuencias completada. Frecuencias procesadas: {$count}");
        return $count;
    }

    public function syncHorarios(): int
    {
        $lineas = Linea::all();
        $count = 0;

        // Limpiar horarios existentes
        DB::table('horarios')->truncate();

        foreach ($lineas as $linea) {
            try {
                Log::info("Procesando horarios de línea: {$linea->id_linea}");

                $response = Http::timeout(60)
                    ->get("{$this->baseUrl}/horarios_lineas?linea={$linea->id_linea}&lang=ES");

                if (!$response->successful()) {
                    Log::warning("Error obteniendo horarios de línea {$linea->id_linea}");
                    continue;
                }

                $data = $response->json();
                $planificadores = $data['planificadores'] ?? [];

                foreach ($planificadores as $planificador) {
                    $idPlanificador = $planificador['idPlani'];
                    $fechaInicio = $planificador['fechaInicio'];
                    $fechaFin = $planificador['fechaFin'];
                    $muestraFechaFin = $planificador['muestraFechaFin'] == '1';

                    // Extraer núcleos de los bloques para IDA
                    $nucleosIda = $this->extraerNucleosDeBloque($planificador['bloquesIda'] ?? []);

                    // Extraer núcleos de los bloques para VUELTA
                    $nucleosVuelta = $this->extraerNucleosDeBloque($planificador['bloquesVuelta'] ?? []);

                    // Procesar horarios de ida
                    $horariosIda = $planificador['horarioIda'] ?? [];
                    foreach ($horariosIda as $horario) {
                        Horario::create([
                            'id_linea' => $linea->id_linea,
                            'id_planificador' => $idPlanificador,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'muestra_fecha_fin' => $muestraFechaFin,
                            'sentido' => 'ida',
                            'horas' => $horario['horas'],
                            'frecuencia_acronimo' => $horario['frecuencia'],
                            'observaciones' => $horario['observaciones'] ?: null,
                            'demanda_horas' => $horario['demandahoras'] ?: null,
                            'nucleos' => $nucleosIda, // Usar núcleos extraídos de bloques
                            'bloques' => $planificador['bloquesIda'] ?? null
                        ]);
                        $count++;
                    }

                    // Procesar horarios de vuelta
                    $horariosVuelta = $planificador['horarioVuelta'] ?? [];
                    foreach ($horariosVuelta as $horario) {
                        Horario::create([
                            'id_linea' => $linea->id_linea,
                            'id_planificador' => $idPlanificador,
                            'fecha_inicio' => $fechaInicio,
                            'fecha_fin' => $fechaFin,
                            'muestra_fecha_fin' => $muestraFechaFin,
                            'sentido' => 'vuelta',
                            'horas' => $horario['horas'],
                            'frecuencia_acronimo' => $horario['frecuencia'],
                            'observaciones' => $horario['observaciones'] ?: null,
                            'demanda_horas' => $horario['demandahoras'] ?: null,
                            'nucleos' => $nucleosVuelta, // Usar núcleos extraídos de bloques
                            'bloques' => $planificador['bloquesVuelta'] ?? null
                        ]);
                        $count++;
                    }
                }

            } catch (\Exception $e) {
                Log::error("Error procesando horarios de línea {$linea->id_linea}: " . $e->getMessage());
            }
        }

        Log::info("Sincronización de horarios completada. Horarios procesados: {$count}");
        return $count;
    }

    private function extraerNucleosDeBloque(array $bloques): array
    {
        $nucleos = [];

        foreach ($bloques as $bloque) {
            // Solo incluir bloques de tipo "0" (paradas), excluir "1" (frecuencia) y "2" (observaciones)
            if (isset($bloque['tipo']) && $bloque['tipo'] === '0') {
                $nucleos[] = [
                    'nombre' => $bloque['nombre'],
                    'color' => $bloque['color'] ?? '#F2F2F2',
                    'colspan' => 1 // Valor por defecto
                ];
            }
        }

        Log::info("Núcleos extraídos de bloques:", ['nucleos' => $nucleos]);

        return $nucleos;
    }


    public function syncPuntosVenta(): int
    {
        $response = Http::get("{$this->baseUrl}/puntos_venta");

        if (!$response->successful()) {
            Log::error("Error al obtener puntos de venta. Código: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $puntos = $data['puntosVenta'] ?? $data['puntos_venta'] ?? $data['data'] ?? [];

        $count = 0;

        foreach ($puntos as $punto) {
            try {
                if (empty($punto['idComercio'])) {
                    Log::warning("Punto de venta sin ID", ['punto' => $punto]);
                    continue;
                }

                // Convertir coordenadas a formato numérico
                $latitud = isset($punto['latitud']) ? (float)$punto['latitud'] : null;
                $longitud = isset($punto['longitud']) ? (float)$punto['longitud'] : null;

                // Verificar existencia del municipio
                $municipio = Municipio::firstOrNew(['id_municipio' => $punto['idMunicipio'] ?? null]);

                // Verificar existencia del núcleo
                $nucleo = null;
                if (!empty($punto['idNucleo'])) {
                    $nucleo = Nucleo::firstOrNew([
                        'id_nucleo' => $punto['idNucleo'],
                        'id_municipio' => $punto['idMunicipio'] ?? null
                    ]);

                    // Si el núcleo es nuevo, guardar información básica
                    if (!$nucleo->exists && !empty($punto['nucleo'])) {
                        $nucleo->nombre = $punto['nucleo'];
                        $nucleo->save();
                    }
                }

                // Asignar "Sin datos" si el tipo está vacío
                $tipo = !empty($punto['tipo']) ? $punto['tipo'] : 'Sin datos';

                PuntoVenta::updateOrCreate(
                    ['id_punto' => $punto['idComercio']],
                    [
                        'id_municipio' => $punto['idMunicipio'] ?? null,
                        'id_nucleo' => $punto['idNucleo'] ?? null,
                        'direccion' => $punto['direccion'] ?? null,
                        'tipo' => $tipo,
                        'latitud' => $latitud,
                        'longitud' => $longitud
                    ]
                );

                $count++;

            } catch (\Exception $e) {
                Log::error("Error procesando punto de venta", [
                    'id_comercio' => $punto['idComercio'] ?? 'N/A',
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Sincronización completada. Puntos procesados: {$count}");
        return $count;
    }

    public function syncTarifasInterurbanas(): int
    {
        try {
            Log::info("Iniciando sincronización de tarifas interurbanas");

            $response = Http::timeout(30)->get("{$this->baseUrl}/tarifas_interurbanas");

            if (!$response->successful()) {
                Log::error("Error al obtener tarifas interurbanas: " . $response->status());
                return 0;
            }

            $data = $response->json();
            $tarifas = $data['tarifasInterurbanas'] ?? [];

            if (empty($tarifas)) {
                Log::warning("No se encontraron tarifas interurbanas en la respuesta");
                return 0;
            }

            $count = 0;

            // Limpiar tabla existente
            TarifaInterurbana::truncate();

            foreach ($tarifas as $tarifa) {
                try {
                    TarifaInterurbana::create([
                        'saltos' => (int)$tarifa['saltos'],
                        'bs' => (float)$tarifa['bs'],
                        'tarjeta' => (float)$tarifa['tarjeta']
                    ]);

                    $count++;
                    Log::debug("Tarifa procesada: {$tarifa['saltos']} saltos");

                } catch (\Exception $e) {
                    Log::error("Error procesando tarifa", [
                        'tarifa' => $tarifa,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("Sincronización de tarifas interurbanas completada. Tarifas procesadas: {$count}");
            return $count;

        } catch (\Exception $e) {
            Log::error("Error general en sincronización de tarifas interurbanas: " . $e->getMessage());
            return 0;
        }
    }

    /* public function syncTarifas(): int
     {
         // Descomenta y adapta este méttodo si tienes el modelo Tarifa definido
         $response = Http::get("{$this->baseUrl}/tarifas_interurbanas");
         if (!$response->successful()) {
             Log::error("Error al obtener tarifas: " . $response->status());
             return 0;
         }

         $data = $response->json();
         $tarifas = $data['tarifas'] ?? $data['data'] ?? $data;

         $count = 0;
         foreach ($tarifas as $tarifa) {
             if (!isset($tarifa['saltos'])) {
                 Log::warning("Tarifa sin saltos", ['tarifa_problematica' => $tarifa]);
                 continue;
             }

             try {
                 // Asegúrate de tener el modelo Tarifa definido y la migración correspondiente
                 Tarifa::updateOrCreate(
                     ['saltos' => $tarifa['saltos']],
                     [
                         'bs' => $tarifa['bs'] ?? null,
                         'tarjeta' => $tarifa['tarjeta'] ?? null
                     ]
                 );
                 $count++;
             } catch (\Exception $e) {
                 Log::error("Error al guardar tarifa: " . $e->getMessage(), ['tarifa' => $tarifa]);
             }
         }
         return $count;
     } */
}
