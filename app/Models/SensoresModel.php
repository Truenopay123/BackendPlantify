<?php

namespace App\Models;

use CodeIgniter\Model;

class SensoresModel extends Model
{
    protected $table = 'sensores';
    protected $primaryKey = 'id_sensores';
    protected $allowedFields = [
        'calidad_aire',
        'humedad',
        'humedad_suelo',
        'temperaturaDHT11',
        'temperaturaDS18B20',
        'plaga',
        'fecha',
    ];
    protected $useTimestamps = false;
}