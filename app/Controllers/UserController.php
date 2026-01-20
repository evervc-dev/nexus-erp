<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\UserRepository;
use App\Models\User;

class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->userRepo = new UserRepository($db);
    }

    /**
     * Listado de usuarios (Staff)
     */
    public function index(): void
    {
        // Reutilizamos el método getAll que ya creamos antes
        $users = $this->userRepo->getAll(true); // Solo activos

        $this->view('users/index', [
            'title' => 'Gestión de Usuarios',
            'users' => $users
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(): void
    {
        $allRoles = $this->userRepo->getAllRoles();
        $allowedRoles = [];
        
        $currentUserRole = $_SESSION['role_name'];

        // LÓGICA DE JERARQUÍA
        foreach ($allRoles as $role) {
            
            // Regla 1: Nadie puede crear otro 'SuperAdmin' por sistema (solo por BD directa)
            if ($role['name'] === 'SuperAdmin') continue;

            // Regla 2: El Ingeniero solo puede crear Maestros y Clientes
            if ($currentUserRole === 'Ingeniero') {
                if (in_array($role['name'], ['MaestroObra', 'Cliente'])) {
                    $allowedRoles[] = $role;
                }
            }
            // Regla 3: El SuperAdmin puede crear todo lo demás (Ingenieros, Maestros, Clientes)
            elseif ($currentUserRole === 'SuperAdmin') {
                $allowedRoles[] = $role;
            }
        }

        $this->view('users/create', [
            'title' => 'Registrar Nuevo Usuario',
            'roles' => $allowedRoles
        ]);
    }

    /**
     * Guardar usuario en BD
     */
    public function store(): void
    {
        $data = $this->request->getBody();

        // Validaciones básicas
        if (empty($data['first_name']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
            $this->redirect('/users/create?error=missing_fields');
            return;
        }

        // Verificar si el correo existe
        if ($this->userRepo->findByEmail($data['email'])) {
            $this->redirect('/users/create?error=email_exists');
            return;
        }

        // Crear la Entidad User
        $user = new User(
            null,
            (int) $data['role_id'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['phone'] ?? null,
            true, // is_active
            null
        );

        $this->userRepo->save($user);

        $this->redirect('/users');
    }
}