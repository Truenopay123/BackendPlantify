<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class SensoresController extends ResourceController
{
    protected $modelName = 'App\Models\SensoresModel';
    protected $format = 'json';

    public function guardar()
    {
        date_default_timezone_set('America/Mexico_City');

        $json = $this->request->getJSON();
        if (!$json || !isset($json->calidad_aire) || !isset($json->humedad)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos'])->setStatusCode(400);
        }

        $data = [
            'calidad_aire' => $json->calidad_aire,
            'humedad' => $json->humedad,
            'humedad_suelo' => $json->humedad_suelo,
            'temperaturaDHT11' => $json->temperaturaDHT11,
            'temperaturaDS18B20' => $json->temperaturaDS18B20,
            'plaga' => $json->plaga ?? false,
            'fecha' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->model->insert($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Datos guardados correctamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function historial()
    {
        try {
            $fecha = $this->request->getGet('fecha'); // Obtener parámetro de fecha
            if ($fecha) {
                // Filtrar por día (ignora hora, solo usa fecha)
                $historial = $this->model
                    ->where('DATE(fecha)', $fecha)
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
            } else {
                // Mostrar todo el historial si no hay filtro
                $historial = $this->model->orderBy('fecha', 'DESC')->findAll();
            }
            return $this->response->setJSON($historial);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }
}