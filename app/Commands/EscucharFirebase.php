<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Services\SensorAlertService;

class EscucharFirebase extends BaseCommand
{
    protected $group = 'invernadero';
    protected $name = 'invernadero:escuchar';
    protected $description = 'Escucha cambios en Firebase Realtime Database';

    public function run(array $params)
    {
        try {
            $service = new SensorAlertService();
            $service->escucharCambios();
        } catch (\Exception $e) {
            $this->error('Error al escuchar Firebase: ' . $e->getMessage());
            return 1;
        }
    }
}