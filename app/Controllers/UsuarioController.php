<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    public function index()
    {
        $model = new UsuarioModel();
        $usuarios = $model->obtenerUsuarios();

        return $this->respond($usuarios); // Devuelve los datos en formato JSON
    }
}
