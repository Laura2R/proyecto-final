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
use App\Models\Tarifa;
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
        // Primero obtenemos todas las paradas con sus núcleos del endpoint general
        $responseParadas = Http::get("{$this->baseUrl}/paradas");

        if (!$responseParadas->successful()) {
            Log::error("Error al obtener paradas generales: " . $responseParadas->status());
            return 0;
        }

        $paradasGenerales = $responseParadas->json()['paradas'] ?? [];

        // Creamos un mapa indexado por idParada para acceso rápido
        $paradasPorId = [];
        // También creamos un mapa de núcleos por nombre de parada
        $nucleosPorNombre = [];
        // Detectamos nombres duplicados para tener especial cuidado
        $nombresDuplicados = [];
        $contadorNombres = [];

        // Primero identificamos nombres duplicados
        foreach ($paradasGenerales as $parada) {
            $nombre = strtoupper(trim($parada['nombre']));
            if (!isset($contadorNombres[$nombre])) {
                $contadorNombres[$nombre] = 0;
            }
            $contadorNombres[$nombre]++;
        }

        foreach ($contadorNombres as $nombre => $contador) {
            if ($contador > 1) {
                $nombresDuplicados[$nombre] = true;
                Log::info("Nombre de parada duplicado en API general: {$nombre} ({$contador} veces)");
            }
        }

        // Ahora procesamos todas las paradas generales
        foreach ($paradasGenerales as $parada) {
            $nombre = strtoupper(trim($parada['nombre']));
            $paradasPorId[$parada['idParada']] = $parada;

            // Si el nombre no está duplicado, lo podemos usar como índice confiable
            if (!isset($nombresDuplicados[$nombre])) {
                $nucleosPorNombre[$nombre] = [
                    'idNucleo' => $parada['idNucleo'],
                    'idParada' => $parada['idParada']
                ];
            }
        }

        $count = 0;
        $lineas = Linea::all();

        foreach ($lineas as $linea) {
            $response = Http::get("{$this->baseUrl}/lineas/{$linea->id_linea}/paradas");

            if (!$response->successful()) {
                Log::error("Error obteniendo paradas para línea {$linea->id_linea}: " . $response->status());
                continue;
            }

            $paradasLinea = $response->json()['paradas'] ?? [];

            foreach ($paradasLinea as $paradaLinea) {
                try {
                    $idParadaLinea = $paradaLinea['idParada'];
                    $nombreParadaLinea = strtoupper(trim($paradaLinea['nombre']));

                    // Estrategia 1: Intentar coincidir por ID de parada
                    if (isset($paradasPorId[$idParadaLinea])) {
                        $paradaGeneral = $paradasPorId[$idParadaLinea];
                        Log::info("Parada encontrada por ID: {$idParadaLinea}");
                    }
                    // Estrategia 2: Intentar coincidir por nombre si no es un nombre duplicado
                    elseif (isset($nucleosPorNombre[$nombreParadaLinea])) {
                        $idParadaGeneral = $nucleosPorNombre[$nombreParadaLinea]['idParada'];
                        $paradaGeneral = $paradasPorId[$idParadaGeneral];
                        Log::info("Parada encontrada por nombre: {$nombreParadaLinea}");
                    }
                    // Si no encontramos coincidencia, lo registramos y continuamos
                    else {
                        Log::warning("No se encontró coincidencia para la parada: {$nombreParadaLinea} (ID: {$idParadaLinea})");
                        continue;
                    }

                    // Buscamos el núcleo
                    $nucleo = Nucleo::where('id_nucleo', $paradaGeneral['idNucleo'])->first();

                    if (!$nucleo) {
                        // Si no existe el núcleo, intentamos crear uno temporal básico
                        Log::warning("Núcleo no encontrado: {$paradaGeneral['idNucleo']} para parada {$nombreParadaLinea}. Intentando crear núcleo temporal.");

                        // Podríamos tener un núcleo "desconocido" para estos casos
                        // O intentar obtener más información del API
                        $nucleoResponse = Http::get("{$this->baseUrl}/nucleos/{$paradaGeneral['idNucleo']}");

                        if ($nucleoResponse->successful()) {
                            $nucleoData = $nucleoResponse->json();
                            $nucleo = Nucleo::updateOrCreate(
                                ['id_nucleo' => $paradaGeneral['idNucleo']],
                                [
                                    'id_municipio' => $nucleoData['idMunicipio'] ?? null,
                                    'id_zona' => $nucleoData['idZona'] ?? null,
                                    'nombre' => $nucleoData['nombre'] ?? 'Desconocido'
                                ]
                            );
                        } else {
                            Log::error("No se pudo obtener información del núcleo {$paradaGeneral['idNucleo']}");
                            continue;
                        }
                    }

                    // Convertir modos (array a string)
                    $modos = is_array($paradaLinea['modos'] ?? null)
                        ? implode(',', $paradaLinea['modos'])
                        : ($paradaLinea['modos'] ?? '');

                    // NUEVO: Obtener detalles específicos de la parada, incluyendo observaciones
                    $detalleParadaResponse = Http::get("{$this->baseUrl}/paradas/{$idParadaLinea}");


                    if ($detalleParadaResponse->successful()) {
                        $detalleParada = $detalleParadaResponse->json();

                        Log::info("Obtenidas observaciones para parada {$idParadaLinea}");
                    } else {
                        Log::warning("No se pudieron obtener detalles para la parada {$idParadaLinea}");
                    }

                    // Crear/actualizar parada
                    // Usamos los datos del endpoint general para asegurar consistencia
                    Parada::updateOrCreate(
                        ['id_parada' => $idParadaLinea],
                        [
                            'id_nucleo' => $paradaGeneral['idNucleo'],
                            'id_municipio' => $nucleo->id_municipio,
                            'id_zona' => $nucleo->id_zona,
                            'nombre' => $paradaGeneral['nombre'], // Nombre normalizado del API general
                            'latitud' => $paradaLinea['latitud'], // Mantener coordenadas de la línea
                            'longitud' => $paradaLinea['longitud'],
                            'observaciones' => $detalleParada['observaciones'] ?? null,
                            'modos' => $modos
                        ]
                    );

                    $count++;

                } catch (\Exception $e) {
                    Log::error("Error sincronizando parada {$paradaLinea['idParada']}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                }
            }
        }

        return $count;
    }

    public function syncLineaParada(): int
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('linea_parada')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = 0;
        $lineas = Linea::all();

        foreach ($lineas as $linea) {
            $response = Http::get("{$this->baseUrl}/lineas/{$linea->id_linea}/paradas");

            if (!$response->successful()) {
                Log::error("Error obteniendo paradas para línea {$linea->id_linea}: " . $response->status());
                continue;
            }

            $paradasLinea = $response->json()['paradas'] ?? [];

            foreach ($paradasLinea as $p) {
                try {
                    DB::table('linea_parada')->insert([
                        'id_linea' => $p['idLinea'],
                        'id_parada' => $p['idParada'],
                        'orden' => $p['orden'] ?? null,
                        'sentido' => $p['sentido'] == 1 ? 'ida' : 'vuelta',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Error insertando relación {$linea->id_linea}-{$p['idParada']}: " . $e->getMessage());
                }
            }
        }

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
            Log::error("Error al obtener puntos de venta: " . $response->status());
            return 0;
        }

        $data = $response->json();
        // Extrae el array correcto de puntos de venta
        $puntos = $data['puntosVenta'] ?? $data['puntos_venta'] ?? $data['data'] ?? [];

        $count = 0;
        foreach ($puntos as $punto) {
            if (!isset($punto['idComercio'])) {
                Log::warning("Punto de venta sin idComercio", ['punto_problematico' => $punto]);
                continue;
            }

            // Guarda todos los campos relevantes
            PuntoVenta::updateOrCreate(
                ['id_punto' => $punto['idComercio']],
                [
                    'id_municipio' => $punto['idMunicipio'] ?? null,
                    'id_nucleo'    => $punto['idNucleo'] ?? null,
                    'tipo'         => $punto['tipo'] ?? null,
                    'municipio'    => $punto['municipio'] ?? null,
                    'nucleo'       => $punto['nucleo'] ?? null,
                    'direccion'    => $punto['direccion'] ?? null,
                    'latitud'      => $punto['latitud'] ?? null,
                    'longitud'     => $punto['longitud'] ?? null,
                ]
            );
            $count++;
        }
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
