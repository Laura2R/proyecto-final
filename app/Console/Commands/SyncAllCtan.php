<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiServiceInterface;

class SyncAllCtan extends Command
{
    protected $signature = 'ctan:sync-todo';
    protected $description = 'Sincroniza todas las tablas principales desde la API CTAN';

    protected $apiService;

    public function __construct(ApiServiceInterface $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    public function handle()
    {
        $this->info('Sincronizando zonas...');
        $this->apiService->syncZonas();

        $this->info('Sincronizando municipios...');
        $this->apiService->syncMunicipios();

        $this->info('Sincronizando núcleos...');
        $this->apiService->syncNucleos();

        $this->info('Sincronizando líneas...');
        $this->apiService->syncLineas();

        $this->info('Sincronizando paradas...');
        $this->apiService->syncParadas();

        $this->info('Sincronizando pivote línea-parada...');
        $this->apiService->syncLineaParada();

        $this->info('Sincronizando horarios...');
        $this->apiService->syncHorarios();

        $this->info('Sincronizando puntos de venta...');
        $this->apiService->syncPuntosVenta();

        //$this->info('Sincronizando tarifas...');
        //$this->apiService->syncTarifas();

        $this->info('Sincronización completa.');
    }
}
