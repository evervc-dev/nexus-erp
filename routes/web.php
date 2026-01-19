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

// Logout
$router->get('/logout', [AuthController::class, 'logout']);

// --- Rutas Protegidas (Eventualmente) ---

// GRUPO DE RUTAS DE EMPLEADOS (Protegidas)
$router->get('/employees', [EmployeeController::class, 'index'])
       ->middleware('auth');

$router->get('/employees/create', [EmployeeController::class, 'create'])
       ->middleware('auth');

$router->post('/employees/create', [EmployeeController::class, 'store'])
       ->middleware('auth');