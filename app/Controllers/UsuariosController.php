<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\RESTful\ResourceController;

class UsuariosController extends ResourceController
{
    // Habilitar CORS correctamente
    public function options()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(200);
    }

    // Procesa el login del usuario (POST)
    public function procesarLogin()
    {        
        // Obtener datos desde JSON (POST)
        $json = $this->request->getJSON();
        if (!$json || !isset($json->correo) || !isset($json->password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Faltan datos'
            ])->setStatusCode(400);
        }

        $correo = $json->correo;
        $password = $json->password;

        $UsuariosModel = new UsuariosModel();
        $usuario = $UsuariosModel->where('correo', $correo)->first();

        if ($usuario && $password === $usuario['contraseña']) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Inicio de sesión exitoso',
                'usuario' => [
                    'id' => $usuario['id_usuario'],
                    'nombre' => $usuario['nombre'],
                    'correo' => $usuario['correo'],
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Correo o contraseña incorrectos'
            ])->setStatusCode(401);
        }
    }

    // Método para cerrar sesión
    public function logout()
    {
        session()->destroy();
        return $this->respond(['message' => 'Sesión cerrada exitosamente']);
    }
}
