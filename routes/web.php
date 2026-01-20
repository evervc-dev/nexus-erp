<?php

use App\Controllers\AuthController;
use App\Controllers\EmployeeController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\BudgetController;
use App\Controllers\TaskController;
use App\Controllers\UserController;

/** @var App\Core\Router $router */

// --- Rutas Públicas ---

// Ruta principal
$router->get('/', [HomeController::class, 'index']);


// Login
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);

// Registro
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);

// Logout
$router->get('/logout', [AuthController::class, 'logout']);

// --- Rutas Protegidas (Eventualmente) ---

// GRUPO DE RUTAS DE EMPLEADOS
// Orden de ejecución: 
// Auth (¿Está logueado?) -> SI -> 
// Role (¿Es Ingeniero o Maestro?) -> SI -> Controlador

$router->get('/employees', [EmployeeController::class, 'index'])
       ->middleware('auth')
       ->middleware('role:Ingeniero,MaestroObra');

$router->get('/employees/create', [EmployeeController::class, 'create'])
       ->middleware('auth')
       ->middleware('role:Ingeniero,MaestroObra');

$router->post('/employees/create', [EmployeeController::class, 'store'])
       ->middleware('auth')
       ->middleware('role:Ingeniero,MaestroObra');


// MÓDULO DE PROYECTOS

// Ver lista (Todos los logueados)
$router->get('/projects', [ProjectController::class, 'index'])
       ->middleware('auth');

// Crear (Solo Admin e Ingeniero)
$router->get('/projects/create', [ProjectController::class, 'create'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->post('/projects/create', [ProjectController::class, 'store'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->get('/projects/view/{id}', [ProjectController::class, 'show'])
       ->middleware('auth');

$router->get('/projects/edit/{id}', [ProjectController::class, 'edit'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->post('/projects/edit/{id}', [ProjectController::class, 'update'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

// ELIMINAR PROYECTO (Solo SuperAdmin)
$router->post('/projects/delete/{id}', [ProjectController::class, 'delete'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin');

// Cambiar Estado del Proyecto (Solo Admin e Ingeniero)
$router->post('/projects/update-status/{id}', [ProjectController::class, 'updateStatus'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

// Acciones de Presupuesto (Solo Ingenieros/Admin)
$router->post('/budget/add', [BudgetController::class, 'store'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->post('/budget/delete/{id}', [BudgetController::class, 'delete'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

// --- GESTIÓN DE TAREAS (KANBAN) ---

// 1. Crear Tarea (POST)
// Solo el Ingeniero o Admin pueden crear y asignar tareas.
$router->post('/tasks/create', [TaskController::class, 'store'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

// 2. Actualizar Estado (POST)
// Mover la tarjeta de columna. El Maestro de Obra NECESITA permiso aquí para trabajar.
$router->post('/tasks/update-status/{id}', [TaskController::class, 'updateStatus'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero,MaestroObra');

// GESTIÓN DE USUARIOS (STAFF)
$router->get('/users', [UserController::class, 'index'])       
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->get('/users/create', [UserController::class, 'create'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');

$router->post('/users/create', [UserController::class, 'store'])
       ->middleware('auth')
       ->middleware('role:SuperAdmin,Ingeniero');