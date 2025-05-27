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
        $responseParada = Http::get("{$this->baseUrl}/paradas/{$idParada}");
        if ($responseParada->successful()) {
            $paradaData = $responseParada->json();
            $datos['observaciones'] = $paradaData['observaciones'] ?? null;
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


    public function syncHorarios(): int
    {
        // Desactivar las restricciones de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('horarios')->truncate();
        DB::table('frecuencias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $response = Http::get("{$this->baseUrl}/lineas");
        if (!$response->successful()) {
            Log::error("Error al obtener lineas para horarios: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $lineas = $data['lineas'] ?? $data['data'] ?? $data;

        $count = 0;
        foreach ($lineas as $linea) {
            if (!isset($linea['idLinea'])) {
                Log::warning("Linea sin idLinea en syncHorarios", ['linea_problematica' => $linea]);
                continue;
            }

            $horariosResponse = Http::get("{$this->baseUrl}/horarios_lineas?linea={$linea['idLinea']}&lang=ES");
            if (!$horariosResponse->successful()) {
                Log::error("Error al obtener horarios de línea {$linea['idLinea']}: " . $horariosResponse->status());
                continue;
            }

            $horarios = $horariosResponse->json();
            if (!isset($horarios['planificadores'])) {
                Log::info("La línea {$linea['idLinea']} no tiene planificadores de horarios");
                continue;
            }

            // Sincronizar frecuencias
            if (isset($horarios['frecuencias'])) {
                foreach ($horarios['frecuencias'] as $f) {
                    if (!isset($f['idfrecuencia'])) {
                        Log::warning("Frecuencia sin idfrecuencia", ['frecuencia_problematica' => $f]);
                        continue;
                    }

                    try {
                        Frecuencia::updateOrCreate(
                            ['id_frecuencia' => $f['idfrecuencia']],
                            [
                                'acronimo' => $f['acronimo'] ?? '',
                                'nombre' => $f['nombre'] ?? ''
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error("Error al guardar frecuencia: " . $e->getMessage(), ['frecuencia' => $f]);
                    }
                }
            }

            foreach ($horarios['planificadores'] as $planificador) {
                // Horarios de ida
                if (isset($planificador['horarioIda'])) {
                    foreach ($planificador['horarioIda'] as $h) {
                        try {
                            Horario::create([
                                'id_linea' => $linea['idLinea'],
                                'sentido' => 'ida',
                                'tipo_dia' => $h['frecuencia'] ?? 'LVL',
                                'horas' => isset($h['horas']) ? json_encode($h['horas']) : null,
                                'id_frecuencia' => null // Puedes mapearlo si lo necesitas
                            ]);
                            $count++;
                        } catch (\Exception $e) {
                            Log::error("Error al guardar horario ida: " . $e->getMessage(), [
                                'linea' => $linea['idLinea'],
                                'horario' => $h
                            ]);
                        }
                    }
                }

                // Horarios de vuelta
                if (isset($planificador['horarioVuelta'])) {
                    foreach ($planificador['horarioVuelta'] as $h) {
                        try {
                            Horario::create([
                                'id_linea' => $linea['idLinea'],
                                'sentido' => 'vuelta',
                                'tipo_dia' => $h['frecuencia'] ?? 'LVL',
                                'horas' => isset($h['horas']) ? json_encode($h['horas']) : null,
                                'id_frecuencia' => null // Puedes mapearlo si lo necesitas
                            ]);
                            $count++;
                        } catch (\Exception $e) {
                            Log::error("Error al guardar horario vuelta: " . $e->getMessage(), [
                                'linea' => $linea['idLinea'],
                                'horario' => $h
                            ]);
                        }
                    }
                }
            }
        }
        return $count;
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
                        'id_nucleo'    => $punto['idNucleo'] ?? null,
                        'direccion'    => $punto['direccion'] ?? null,
                        'tipo'         => $tipo,
                        'latitud'      => $latitud,
                        'longitud'     => $longitud
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
