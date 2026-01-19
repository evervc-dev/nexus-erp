<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): void
    {
        // Si no existe la sesión de usuario, redirigir al login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}