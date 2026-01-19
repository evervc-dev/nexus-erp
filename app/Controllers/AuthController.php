<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    private UserRepository $userRepo;

    /**
     * Constructor: Recibe DB y Request del Router.\
     */
    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->userRepo = new UserRepository($db);
    }

    /**
     * Muestra el formulario de Login (GET)
     */
    public function loginForm(): void
    {
        // Si ya está logueado, al Dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        $this->view('auth/login', [
            'title' => 'Iniciar Sesión - Nexus ERP'
        ]);
    }

    /**
     * Procesa el Login (POST)
     */
    public function login(): void
    {
        // Obtiene datos limpios usando la clase Request
        $email = $this->request->input('email');
        $password = $this->request->input('password');

        // Validación básica
        if (!$email || !$password) {
            $this->view('auth/login', [
                'title' => 'Iniciar Sesión',
                'error' => 'Por favor completa todos los campos.'
            ]);
            return;
        }

        // Busca usuario en el Repositorio
        $user = $this->userRepo->findByEmail($email);

        // Verifica existencia y contraseña
        if ($user && password_verify($password, $user->password_hash)) {
            
            // Verificar si el usuario está activo
            if (!$user->is_active) {
                $this->view('auth/login', [
                    'title' => 'Iniciar Sesión',
                    'error' => 'Tu cuenta está desactivada. Contacta al administrador.',
                    'old_email' => $email
                ]);
                return;
            }

            // --- LOGIN EXITOSO ---
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            // Guardar datos en sesión
            $_SESSION['user_id'] = $user->id;
            // Concatenamos Nombre y Apellido 
            $_SESSION['user_name'] = $user->name . ' ' . ($user->last_name ?? ''); 
            $_SESSION['user_email'] = $user->email;
            $_SESSION['role_id'] = $user->role_id;
            $_SESSION['role_name'] = $user->role_name; // Viene del JOIN en el Repo

            // Redireccionar al Dashboard
            $this->redirect('/');

        } else {
            // --- LOGIN FALLIDO ---
            $this->view('auth/login', [
                'title' => 'Iniciar Sesión',
                'error' => 'Credenciales incorrectas.',
                'old_email' => $email // Para no borrar lo que el usuario escribió
            ]);
        }
    }

    /**
     * Cerrar Sesión (GET)
     */
    public function logout(): void
    {
        // Limpiar array de sesión
        $_SESSION = [];

        // Borrar la cookie de sesión del navegador
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesión en el servidor
        session_destroy();

        $this->redirect('/login');
    }
}