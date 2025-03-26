<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';

    // Campos que son asignables
    protected $allowedFields = ['tipo', 'nombre', 'apellido', 'correo', 'contraseña', 'telefono', 'estatus', 'session_token'];

    // Configuración de la base de datos
    protected $useTimestamps = false; // No hay campos de timestamp en tu esquema

    // Configuración de validación
    protected $validationRules = [
        'tipo'      => 'required|in_list[admin,operador]',
        'nombre'    => 'required|max_length[50]',
        'apellido'  => 'required|max_length[50]',
        'contraseña' => 'required|min_length[8]|max_length[255]',
        'telefono'  => 'permit_empty|max_length[15]',
        'estatus' => 'permit_empty|in_list[0,1]'
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
        $data['contraseña'] = password_hash($data['contraseña'], PASSWORD_DEFAULT);
        return $this->save($data);
    }
}