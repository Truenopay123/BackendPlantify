<?php

namespace App\Filters;

error_reporting(E_ALL);
ini_set('display_errors', 1);

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CORS implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Permitir solicitudes desde cualquier origen
        return Services::response()->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Para las solicitudes OPTIONS (pre-flight), no necesitamos hacer nada
        if ($request->getMethod() === 'options') {
            return $response->setStatusCode(200);
        }
    }
}
