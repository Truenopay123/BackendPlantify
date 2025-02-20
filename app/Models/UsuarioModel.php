<?php
namespace App\Models;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';  // Nombre de la tabla en la BD
    protected $primaryKey = 'id_usuario'; // Llave primaria

    protected $allowedFields = ['nombre', 'apellido', 'correo']; // Campos permitidos

    public function obtenerUsuarios()
    {
        
        return $this->findAll(); // Recupera todos los usuarios
    }
}
