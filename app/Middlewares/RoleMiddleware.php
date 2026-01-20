<?php

namespace App\Middlewares;

use App\Controllers\ErrorController;

class RoleMiddleware
{
    /**
     * Maneja la petición.
     * Recibe una lista variable de roles permitidos.
     * Ej: handle('Ingeniero', 'MaestroObra')
     */
    public function handle(string ...$allowedRoles): void
    {
        // Asegura que haya sesión (por si olvidaste poner el middleware 'auth' antes)
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_name'])) {
            header('Location: /login');
            exit;
        }

        $userRole = $_SESSION['role_name'];

        // El SuperAdmin siempre tiene acceso total (God Mode)
        if ($userRole === 'SuperAdmin') {
            return;
        }

        // Verifica si el rol del usuario está en la lista de permitidos
        // Usa in_array estricto, es importante asegurarse que los nombres en BD coincidan (Mayúsculas importan)
        if (in_array($userRole, $allowedRoles, true)) {
            return; // Acceso concedido
        }

        // ACCESO DENEGADO
        // Muestra una vista de error 403 Forbidden
        $errorController = new ErrorController();
        $errorController->show(403, "No tienes permisos para acceder a esta sección. (Rol actual: $userRole)");
        exit;
    }
}