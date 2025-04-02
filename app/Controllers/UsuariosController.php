<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\RESTful\ResourceController;

class UsuariosController extends ResourceController
{
    protected $modelName = 'App\Models\UsuariosModel';
    protected $format = 'json';

    // Habilitar CORS
    public function options()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(200);
    }

    // Obtener todos los usuarios (GET /usuarios)
    public function index()
    {
        try {
            $usuariosModel = new UsuariosModel();
            $usuarios = $usuariosModel->select('id_usuario, tipo, nombre, apellido, correo, telefono, estatus')
                ->findAll();

            if (empty($usuarios)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'No hay usuarios registrados',
                    'data' => []
                ], 200);
            }

            $usuariosFormateados = array_map(function ($usuario) {
                return [
                    'id_usuario' => $usuario['id_usuario'],
                    'tipo' => $usuario['tipo'] === 'admin' ? 1 : 0,
                    'nombre' => $usuario['nombre'],
                    'apellido' => $usuario['apellido'],
                    'correo' => $usuario['correo'],
                    'telefono' => $usuario['telefono'],
                    'estatus' => (int) $usuario['estatus']
                ];
            }, $usuarios);

            return $this->respond([
                'status' => 'success',
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $usuariosFormateados
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    // Agregar un nuevo usuario (POST /usuarios)
    public function create()
    {
        try {
            $json = $this->request->getJSON();
            if (
                !$json ||
                !isset($json->nombre) ||
                !isset($json->apellido) ||
                !isset($json->correo) ||
                !isset($json->telefono) ||
                !isset($json->contraseña)
            ) {
                return $this->respond(['status' => 'error', 'message' => 'Faltan datos requeridos'], 400);
            }

            // Depuración: Loguear los datos recibidos
            log_message('debug', 'Datos recibidos en create: ' . json_encode($json));

            // Encriptar la contraseña
            $hashedPassword = password_hash($json->contraseña, PASSWORD_DEFAULT);

            $data = [
                'tipo' => ($json->tipo === 1 || $json->tipo === "1") ? 'admin' : 'operador',
                'nombre' => $json->nombre,
                'apellido' => $json->apellido,
                'correo' => $json->correo,
                'telefono' => $json->telefono,
                'contraseña' => $hashedPassword,
                'estatus' => isset($json->estatus) ? (int) $json->estatus : 1,
                'session_token' => null // Aseguramos que sea NULL inicialmente
            ];

            $usuariosModel = new UsuariosModel();

            // Depuración: Verificar datos antes de insertar
            log_message('debug', 'Datos a insertar: ' . json_encode($data));

            $id = $usuariosModel->insert($data);

            if ($id === false) {
                $errors = $usuariosModel->errors();
                log_message('error', 'Errores del modelo al insertar: ' . json_encode($errors));
                return $this->respond(['status' => 'error', 'message' => 'Error al agregar el usuario: ' . implode(', ', $errors)], 400);
            }

            $nuevoUsuario = $usuariosModel->find($id);
            $usuarioFormateado = [
                'id_usuario' => $nuevoUsuario['id_usuario'],
                'tipo' => $nuevoUsuario['tipo'] === 'admin' ? 1 : 0,
                'nombre' => $nuevoUsuario['nombre'],
                'apellido' => $nuevoUsuario['apellido'],
                'correo' => $nuevoUsuario['correo'],
                'telefono' => $nuevoUsuario['telefono'],
                'estatus' => (int) $nuevoUsuario['estatus']
            ];

            return $this->respond(['status' => 'success', 'message' => 'Usuario agregado exitosamente.', 'data' => $usuarioFormateado], 201);
        } catch (\Exception $e) {
            log_message('error', 'Excepción en create: ' . $e->getMessage() . ' en línea ' . $e->getLine());
            return $this->respond(['status' => 'error', 'message' => 'Error interno al agregar el usuario: ' . $e->getMessage()], 500);
        }
    }

    public function updatePost()
    {
        try {
            $json = $this->request->getJSON();
            if (!$json || !isset($json->id_usuario)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'ID de usuario no proporcionado'
                ], 400);
            }

            $id = $json->id_usuario;

            if (!$json || !isset($json->nombre) || !isset($json->apellido) || !isset($json->correo) || !isset($json->telefono)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Faltan datos requeridos'
                ], 400);
            }

            $usuariosModel = new UsuariosModel();
            $usuario = $usuariosModel->find($id);

            if (!$usuario) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $data = [
                'tipo' => isset($json->tipo) && $json->tipo === 1 ? 'admin' : 'operador',
                'nombre' => $json->nombre,
                'apellido' => $json->apellido,
                'correo' => $json->correo,
                'telefono' => $json->telefono,
                'estatus' => isset($json->estatus) ? (int) $json->estatus : (int) $usuario['estatus']
            ];

            $updated = $usuariosModel->update($id, $data);

            if ($updated === false) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Error al actualizar el usuario: ' . implode(', ', $usuariosModel->errors())
                ], 400);
            }

            $usuarioActualizado = $usuariosModel->find($id);
            $usuarioFormateado = [
                'id_usuario' => $usuarioActualizado['id_usuario'],
                'tipo' => $usuarioActualizado['tipo'] === 'admin' ? 1 : 0,
                'nombre' => $usuarioActualizado['nombre'],
                'apellido' => $usuarioActualizado['apellido'],
                'correo' => $usuarioActualizado['correo'],
                'telefono' => $usuarioActualizado['telefono'],
                'estatus' => (int) $usuarioActualizado['estatus']
            ];

            return $this->respond([
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente',
                'data' => $usuarioFormateado
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Procesa el login del usuario (POST /login)
    public function procesarLogin()
    {
        $json = $this->request->getJSON();
        if (!$json || !isset($json->correo) || !isset($json->password)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos'])->setStatusCode(400);
        }

        $correo = filter_var($json->correo, FILTER_VALIDATE_EMAIL);
        if ($correo === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Correo inválido'])->setStatusCode(400);
        }

        $password = $json->password;
        if (strlen($password) < 8) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Contraseña demasiado corta'])->setStatusCode(400);
        }

        $usuariosModel = new UsuariosModel();
        $usuario = $usuariosModel->where('correo', $correo)->first();

        // Si no se encuentra el usuario o la contraseña es incorrecta
        if (!$usuario || !password_verify($password, $usuario['contraseña'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Credenciales incorrectas'
            ])->setStatusCode(401);
        }

        // Verificar si ya hay una sesión activa
        if (isset($usuario['session_token']) && $usuario['session_token'] !== null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ya existe una sesión activa para este usuario en otro dispositivo.'
            ])->setStatusCode(403);
        }

        // Generar token de sesión
        $sessionToken = bin2hex(random_bytes(16));
        $usuariosModel->update($usuario['id_usuario'], ['session_token' => $sessionToken]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso',
            'usuario' => [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'tipo' => $usuario['tipo'] === 'admin' ? 1 : 0,
                'session_token' => $sessionToken
            ]
        ])->setStatusCode(200);
    }

    // Cerrar sesión (POST /logout)
    public function logout()
    {
        $json = $this->request->getJSON();
        if (!$json || !isset($json->id_usuario) || !isset($json->session_token)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos'])->setStatusCode(400);
        }

        $id_usuario = $json->id_usuario;
        $session_token = $json->session_token;

        $usuariosModel = new UsuariosModel();
        $usuario = $usuariosModel->where('id_usuario', $id_usuario)
            ->where('session_token', $session_token)
            ->first();

        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesión inválida'])->setStatusCode(401);
        }

        // Limpiar el session_token
        $usuariosModel->update($id_usuario, ['session_token' => null]);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Sesión cerrada exitosamente']);
    }

    //

    public function operadores()
    {
        $operadores = $this->model->where('tipo', 'operador')
            ->where('estatus', 1)
            ->findAll();
        return $this->respond($operadores);
    }

    public function notificaciones()
    {
        date_default_timezone_set('America/Mexico_City');

        $json = $this->request->getJSON();
        if (!$json || !isset($json->notificacion)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos'])->setStatusCode(400);
        }

        $dataNotificacion = [
            'notificacion' => $json->notificacion,
            'fecha' => isset($json->fecha) ? date('Y-m-d H:i:s', strtotime($json->fecha)) : date('Y-m-d H:i:s'),
        ];

        try {
            $notificacionesModel = new \App\Models\NotificacionesModel();
            $notificacionesModel->insert($dataNotificacion);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Notificación guardada correctamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

}