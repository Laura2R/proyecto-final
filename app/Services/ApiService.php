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

        // Si la respuesta está anidada bajo 'data', úsala
        $zonas = $data['data'] ?? $data;

        $count = 0;
        foreach ($zonas as $zona) {
            if (!isset($zona['idZona'])) {
                Log::warning("Zona sin idZona", $zona);
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
        $municipios = $data['data'] ?? $data;

        $count = 0;
        foreach ($municipios as $municipio) {
            if (!isset($municipio['idMunicipio'])) {
                Log::warning("Municipio sin idMunicipio", $municipio);
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
        $nucleos = $data['data'] ?? $data;

        $count = 0;
        foreach ($nucleos as $nucleo) {
            if (!isset($nucleo['idNucleo'])) {
                Log::warning("Nucleo sin idNucleo", $nucleo);
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
        $lineas = $data['data'] ?? $data;

        $count = 0;
        foreach ($lineas as $linea) {
            if (!isset($linea['idLinea'])) {
                Log::warning("Línea sin idLinea, se omite: ", $linea);
                continue; // ¡Aquí está el fix!
            }
            $detalle = Http::get("{$this->baseUrl}/lineas/{$linea['idLinea']}")->json();
            if (!isset($detalle['idLinea'])) {
                Log::warning("Detalle de línea sin idLinea, se omite: ", $detalle);
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
        $response = Http::get("{$this->baseUrl}/paradas");

        if (!$response->successful()) {
            Log::error("Error al obtener paradas: " . $response->status());
            return 0;
        }

        $data = $response->json();
        $paradas = $data['data'] ?? $data;

        $count = 0;
        foreach ($paradas as $parada) {
            // 1. Validar existencia de idParada en la lista
            if (!isset($parada['idParada'])) {
                Log::warning("Parada sin idParada en lista", $parada);
                continue;
            }

            // 2. Obtener detalles
            $detalleResponse = Http::get("{$this->baseUrl}/paradas/{$parada['idParada']}");

            if (!$detalleResponse->successful()) {
                Log::error("Error al obtener detalle de parada {$parada['idParada']}");
                continue;
            }

            $detalle = $detalleResponse->json();

            // 3. Validar respuesta del detalle
            if (!isset($detalle['idParada'])) {
                Log::warning("Detalle de parada inválido", $detalle);
                continue;
            }

            // 4. Crear/Actualizar registro
            Parada::updateOrCreate(
                ['id_parada' => $detalle['idParada']],
                [
                    'id_nucleo' => $detalle['idNucleo'] ?? null,
                    'id_municipio' => $detalle['idMunicipio'] ?? null,
                    'id_zona' => $detalle['idZona'] ?? null,
                    'nombre' => $detalle['nombre'] ?? '',
                    'latitud' => $detalle['latitud'] ?? null,
                    'longitud' => $detalle['longitud'] ?? null,
                    'descripcion' => $detalle['descripcion'] ?? null,
                    'observaciones' => $detalle['observaciones'] ?? null,
                    'principal' => isset($detalle['principal']) ? $detalle['principal'] == '1' : false,
                    'inactiva' => isset($detalle['inactiva']) ? $detalle['inactiva'] == '1' : false,
                    'correspondencias' => $detalle['correspondecias'] ?? null
                ]
            );
            $count++;
        }
        return $count;
    }

    public function syncLineaParada(): int
    {
        DB::table('linea_parada')->truncate();
        $response = Http::get("{$this->baseUrl}/lineas");
        if (!$response->successful()) {
            Log::error("Error al obtener lineas para pivote: " . $response->status());
            return 0;
        }
        $lineas = $response->json();
        $lineas = $lineas['data'] ?? $lineas;
        $count = 0;
        foreach ($lineas as $linea) {
            if (!isset($linea['idLinea'])) {
                Log::warning("Línea sin idLinea en lista para linea_parada", $linea);
                continue;
            }
            $paradasLinea = Http::get("{$this->baseUrl}/lineas/{$linea['idLinea']}/paradas")->json();
            foreach ($paradasLinea as $p) {
                if (!isset($p['idParada'])) continue;
                DB::table('linea_parada')->insert([
                    'id_linea' => $linea['idLinea'],
                    'id_parada' => $p['idParada'],
                    'orden' => $p['orden'] ?? null,
                    'sentido' => isset($p['sentido']) && $p['sentido'] == 1 ? 'ida' : 'vuelta',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $count++;
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
        $lineas = $response->json();
        $lineas = $lineas['data'] ?? $lineas;

        $count = 0;
        foreach ($lineas as $linea) {
            if (!isset($linea['idLinea'])) {
                Log::warning("Linea sin idLinea en syncHorarios", $linea);
                continue;
            }
            $horarios = Http::get("{$this->baseUrl}/horarios_lineas?linea={$linea['idLinea']}&lang=ES")->json();
            if (!isset($horarios['planificadores'])) continue;

            // Sincronizar frecuencias
            if (isset($horarios['frecuencias'])) {
                foreach ($horarios['frecuencias'] as $f) {
                    Frecuencia::updateOrCreate(
                        ['id_frecuencia' => $f['idfrecuencia']],
                        [
                            'acronimo' => $f['acronimo'],
                            'nombre' => $f['nombre']
                        ]
                    );
                }
            }

            foreach ($horarios['planificadores'] as $planificador) {
                // Horarios de ida
                if (isset($planificador['horarioIda'])) {
                    foreach ($planificador['horarioIda'] as $h) {
                        Horario::create([
                            'id_linea' => $linea['idLinea'],
                            'sentido' => 'ida',
                            'tipo_dia' => $h['frecuencia'] ?? 'LVL',
                            'horas' => isset($h['horas']) ? json_encode($h['horas']) : null,
                            'id_frecuencia' => null // Puedes mapearlo si lo necesitas
                        ]);
                        $count++;
                    }
                }
                // Horarios de vuelta
                if (isset($planificador['horarioVuelta'])) {
                    foreach ($planificador['horarioVuelta'] as $h) {
                        Horario::create([
                            'id_linea' => $linea['idLinea'],
                            'sentido' => 'vuelta',
                            'tipo_dia' => $h['frecuencia'] ?? 'LVL',
                            'horas' => isset($h['horas']) ? json_encode($h['horas']) : null,
                            'id_frecuencia' => null // Puedes mapearlo si lo necesitas
                        ]);
                        $count++;
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
        $puntos = $data['data'] ?? $data;
        $count = 0;
        foreach ($puntos as $punto) {
            if (!isset($punto['idComercio'])) continue;
            PuntoVenta::updateOrCreate(
                ['id_punto' => $punto['idComercio']],
                [
                    'id_municipio' => $punto['idMunicipio'] ?? null,
                    'nombre' => $punto['nombre'] ?? $punto['tipo'] ?? '',
                    'direccion' => $punto['direccion'] ?? '',
                    'tipo' => $punto['tipo'] ?? '',
                    'latitud' => $punto['latitud'] ?? null,
                    'longitud' => $punto['longitud'] ?? null,
                    'horario' => $punto['horario'] ?? null,
                    'servicios' => isset($punto['servicios']) ? json_encode($punto['servicios']) : null
                ]
            );
            $count++;
        }
        return $count;
    }

    /* public function syncTarifas(): int
    {
        $response = Http::get("{$this->baseUrl}/tarifas_interurbanas");
        if (!$response->successful()) {
            Log::error("Error al obtener tarifas: " . $response->status());
            return 0;
        }
        $data = $response->json();
        $tarifas = $data['data'] ?? $data;
        $count = 0;
        foreach ($tarifas as $tarifa) {
            if (!isset($tarifa['saltos'])) continue;
            Tarifa::updateOrCreate(
                ['saltos' => $tarifa['saltos']],
                [
                    'bs' => $tarifa['bs'] ?? null,
                    'tarjeta' => $tarifa['tarjeta'] ?? null
                ]
            );
            $count++;
        }
        return $count;
    }*/
}
