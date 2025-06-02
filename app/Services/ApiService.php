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
                            // Obtener datos completos de la parada con datos del endpoint general
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

        // NUEVA FUNCIONALIDAD: Buscar datos en endpoint general por nombre o coordenadas
        $datosEndpointGeneral = $this->buscarDatosEnEndpointGeneral($paradaLinea, $paradasGenerales);

        if ($datosEndpointGeneral) {
            $datos['id_nucleo'] = $datosEndpointGeneral['idNucleo'];
            $datos['id_municipio'] = $datosEndpointGeneral['idMunicipio'];
            $datos['id_zona'] = $datosEndpointGeneral['idZona'];

            Log::info("Datos encontrados en endpoint general para parada {$idParada}: Núcleo={$datos['id_nucleo']}, Municipio={$datos['id_municipio']}, Zona={$datos['id_zona']}");
        } else {
            Log::warning("No se encontraron datos en endpoint general para parada {$idParada}");
        }

        // Intentar obtener observaciones del endpoint individual
        try {
            $responseParada = Http::timeout(10)->get("{$this->baseUrl}/paradas/{$idParada}");
            if ($responseParada->successful()) {
                $paradaData = $responseParada->json();
                if (!isset($paradaData['error']) && isset($paradaData['observaciones'])) {
                    $datos['observaciones'] = $paradaData['observaciones'];
                }
            }
        } catch (\Exception $e) {
            Log::debug("No se pudieron obtener observaciones del endpoint individual para parada {$idParada}: " . $e->getMessage());
        }

        if (!$datos['observaciones']) {
            $datos['observaciones'] = 'Parada sincronizada desde líneas';
        }

        return $datos;
    }

    /**
     * NUEVA FUNCIÓN: Buscar datos en endpoint general por nombre o coordenadas cercanas
     */
    private function buscarDatosEnEndpointGeneral($paradaLinea, $paradasGenerales): ?array
    {
        $nombreParadaLinea = trim($paradaLinea['nombre'] ?? '');
        $latitudLinea = (float)($paradaLinea['latitud'] ?? 0);
        $longitudLinea = (float)($paradaLinea['longitud'] ?? 0);

        // 1. PRIMERA BÚSQUEDA: Por nombre exacto
        foreach ($paradasGenerales as $paradaGeneral) {
            $nombreParadaGeneral = trim($paradaGeneral['nombre'] ?? '');

            if (!empty($nombreParadaLinea) && !empty($nombreParadaGeneral) &&
                $nombreParadaLinea === $nombreParadaGeneral) {

                Log::info("Coincidencia por nombre exacto: '{$nombreParadaLinea}'");
                return [
                    'idNucleo' => $paradaGeneral['idNucleo'] ?? null,
                    'idMunicipio' => $paradaGeneral['idMunicipio'] ?? null,
                    'idZona' => $paradaGeneral['idZona'] ?? null,
                    'metodo' => 'nombre_exacto'
                ];
            }
        }

        // 2. SEGUNDA BÚSQUEDA: Por nombre similar (sin espacios, minúsculas)
        $nombreNormalizado = $this->normalizarNombre($nombreParadaLinea);

        if (!empty($nombreNormalizado)) {
            foreach ($paradasGenerales as $paradaGeneral) {
                $nombreGeneralNormalizado = $this->normalizarNombre($paradaGeneral['nombre'] ?? '');

                if (!empty($nombreGeneralNormalizado) && $nombreNormalizado === $nombreGeneralNormalizado) {
                    Log::info("Coincidencia por nombre normalizado: '{$nombreParadaLinea}' -> '{$paradaGeneral['nombre']}'");
                    return [
                        'idNucleo' => $paradaGeneral['idNucleo'] ?? null,
                        'idMunicipio' => $paradaGeneral['idMunicipio'] ?? null,
                        'idZona' => $paradaGeneral['idZona'] ?? null,
                        'metodo' => 'nombre_normalizado'
                    ];
                }
            }
        }

        // 3. TERCERA BÚSQUEDA: Por coordenadas cercanas (si tiene coordenadas válidas)
        if ($latitudLinea && $longitudLinea) {
            $paradaMasCercana = null;
            $menorDistancia = PHP_FLOAT_MAX;

            foreach ($paradasGenerales as $paradaGeneral) {
                $latitudGeneral = (float)($paradaGeneral['latitud'] ?? 0);
                $longitudGeneral = (float)($paradaGeneral['longitud'] ?? 0);

                if (!$latitudGeneral || !$longitudGeneral) {
                    continue;
                }

                $distancia = $this->calcularDistanciaHaversine(
                    $latitudLinea,
                    $longitudLinea,
                    $latitudGeneral,
                    $longitudGeneral
                );

                if ($distancia < $menorDistancia) {
                    $menorDistancia = $distancia;
                    $paradaMasCercana = $paradaGeneral;
                }
            }

            // Solo usar si está dentro de 500 metros (0.5 km)
            if ($paradaMasCercana && $menorDistancia <= 0.5) {
                Log::info("Coincidencia por coordenadas cercanas: distancia {$menorDistancia}km - '{$nombreParadaLinea}' -> '{$paradaMasCercana['nombre']}'");
                return [
                    'idNucleo' => $paradaMasCercana['idNucleo'] ?? null,
                    'idMunicipio' => $paradaMasCercana['idMunicipio'] ?? null,
                    'idZona' => $paradaMasCercana['idZona'] ?? null,
                    'metodo' => 'coordenadas',
                    'distancia' => $menorDistancia
                ];
            }
        }

        return null;
    }

    /**
     * NUEVA FUNCIÓN: Normalizar nombres para comparación
     */
    private function normalizarNombre($nombre): string
    {
        if (empty($nombre)) {
            return '';
        }

        // Convertir a minúsculas, quitar espacios, acentos y caracteres especiales
        $nombre = strtolower(trim($nombre));
        $nombre = str_replace([' ', '-', '_', '.', ','], '', $nombre);

        // Quitar acentos
        $acentos = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ā' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'ē' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'ī' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'ō' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'ū' => 'u',
            'ñ' => 'n', 'ç' => 'c'
        ];

        return strtr($nombre, $acentos);
    }

    /**
     * FUNCIÓN: Calcular distancia entre dos puntos usando fórmula de Haversine
     */
    private function calcularDistanciaHaversine($lat1, $lon1, $lat2, $lon2): float
    {
        $radioTierra = 6371; // Radio de la Tierra en kilómetros

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c; // Distancia en kilómetros
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

}
