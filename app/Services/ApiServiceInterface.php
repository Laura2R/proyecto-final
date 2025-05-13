<?php

namespace App\Services;

interface ApiServiceInterface
{
    public function syncZonas(): int;
    public function syncMunicipios(): int;
    public function syncNucleos(): int;
    public function syncLineas(): int;
    public function syncParadas(): int;
    public function syncLineaParada(): int;
    public function syncHorarios(): int;
    public function syncPuntosVenta(): int;
    //public function syncTarifas(): int;
}
