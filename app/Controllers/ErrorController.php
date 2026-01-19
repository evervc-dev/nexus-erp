<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function show(int $code = 404, string $message = ''): void
    {
        // Establece el código de respuesta HTTP real
        http_response_code($code);

        // Mensajes por defecto si no se envían
        $defaults = [
            403 => 'Acceso denegado. No tienes permisos para ver esto.',
            404 => 'Página no encontrada. ¿Te has perdido?',
            500 => 'Error interno del servidor. Ya estamos trabajando en ello.',
        ];

        $title = "Error $code";
        $finalMessage = $message ?: ($defaults[$code] ?? 'Ha ocurrido un error inesperado.');

        // Renderiza la vista de error
        $this->view('errors/error', [
            'title' => $title,
            'code' => $code,
            'message' => $finalMessage
        ]);
    }
}