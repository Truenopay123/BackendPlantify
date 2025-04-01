<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacionesModel extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    protected $allowedFields = [
        'notificacion',
        'fecha',
    ];
    protected $useTimestamps = false;
}