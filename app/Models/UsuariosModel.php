<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    // Especificamos la tabla de la base de datos
    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';

    // Campos que son asignables
    protected $allowedFields = ['tipo', 'nombre', 'apellido', 'correo', 'contraseña', 'telefono'];

    // Configuración de la base de datos
    protected $useTimestamps = true;

    // Configuración de validación
    protected $validationRules = [
        'correo'    => 'required|valid_email|is_unique[usuarios.correo]',
        'contraseña' => 'required|min_length[6]',
    ];
    
    // Método para obtener un usuario por su correo
    public function obtenerPorEmail($correo)
    {
        return $this->where('correo', $correo)->first();
    }

    // Método para verificar si el usuario existe
    public function verificarUsuario($correo, $contraseña)
    {
        $usuario = $this->obtenerPorEmail($correo);
        if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
            return $usuario;
        }
        return null;
    }

    // Método para registrar un nuevo usuario
    public function registrarUsuario($data)
    {
        // Encriptar la contraseña antes de guardarla
        $data['contraseña'] = password_hash($data['contraseña'], PASSWORD_DEFAULT);
        return $this->save($data);
    }
}
