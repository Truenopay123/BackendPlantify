<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class NotificacionesController extends ResourceController
{
    protected $modelName = 'App\Models\NotificacionesModel';
    protected $format = 'json';

    public function historial()
    {
        try {
            $fecha = $this->request->getGet('fecha');
            if ($fecha) {
                $historial = $this->model
                    ->where('DATE(fecha)', $fecha)
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
            } else {
                $historial = $this->model->orderBy('fecha', 'DESC')->findAll();
            }
            return $this->response->setJSON($historial);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function reporte()
    {
        try {
            $fechaInicio = $this->request->getGet('fecha_inicio');
            $fechaFin = $this->request->getGet('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan fechas'])->setStatusCode(400);
            }

            $historial = $this->model
                ->where('fecha >=', $fechaInicio . ' 00:00:00')
                ->where('fecha <=', $fechaFin . ' 23:59:59')
                ->orderBy('fecha', 'ASC')
                ->findAll();

            return $this->response->setJSON($historial);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }
}