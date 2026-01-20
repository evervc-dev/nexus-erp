<?php

use App\Controllers\AuthController;
use App\Controllers\EmployeeController;
use App\Controllers\HomeController;

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