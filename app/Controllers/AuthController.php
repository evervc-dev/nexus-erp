<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;

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

    /**
     * Muestra el formulario de registro (GET)
     */
    public function registerForm(): void
    {
        // Si ya está logueado, redirigir al Dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        $this->view('auth/register', [
            'title' => 'Crear Cuenta - Nexus ERP'
        ]);
    }

    /**
     * Procesa el registro de un nuevo Cliente (POST)
     */
    public function register(): void
    {
        // Obtiene datos del Request
        $data = $this->request->getBody();

        // Validaciones Básicas
        if ($data['password'] !== $data['confirm_password']) {
            $this->view('auth/register', [
                'error' => 'Las contraseñas no coinciden.',
                'old' => $data
            ]);
            return;
        }

        // Verifica si el correo ya existe
        if ($this->userRepo->findByEmail($data['email'])) {
            $this->view('auth/register', [
                'error' => 'Este correo electrónico ya está registrado.',
                'old' => $data
            ]);
            return;
        }

        // Obtiene el ID del rol "Cliente" para forzar el rol
        $clientRoleId = $this->userRepo->getRoleIdByName('Cliente');

        if (!$clientRoleId) {
            throw new Exception("Error Crítico: El rol 'Cliente' no existe en la base de datos.");
        }

        // Crea la Entidad Usuario        
        $newUser = new User(
            null,
            $clientRoleId, // role_id (Forzado a Cliente)
            $data['first_name'], // name (Viene del form como first_name)
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['phone'] ?? null, // phone (Opcional, null si no viene)
            true, // is_active (Default true)
            null // created_at (BD lo genera)
        );

        // Guarda en Base de Datos
        try {
            $newId = $this->userRepo->save($newUser);

            // Auto-Login (Iniciamos sesión automáticamente)
            session_regenerate_id(true);
            $_SESSION['user_id'] = $newId;
            $_SESSION['user_name'] = $newUser->name . ' ' . $newUser->last_name;
            $_SESSION['user_email'] = $newUser->email;
            $_SESSION['role_id'] = $clientRoleId;
            $_SESSION['role_name'] = 'Cliente';

            // Redirige al Dashboard
            $this->redirect('/');

        } catch (Exception $e) {
            // Manejo de error por si falla la base de datos
            $this->view('auth/register', [
                'error' => 'Ocurrió un error al crear la cuenta. Intenta nuevamente.',
                'old' => $data
            ]);
        }
    }
}