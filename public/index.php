<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Request;
use App\Core\Database;
use App\Core\Logger; // Importación necesaria para el registro de errores
use App\Controllers\ErrorController;
use Dotenv\Dotenv;

// Función auxiliar para cargar la vista de error sin dependencias del framework
function renderCriticalError(string $title, string $message): void {
    $errorFile = __DIR__ . '/../views/errors/critical.php';
    
    if (file_exists($errorFile)) {
        require $errorFile;
    } else {
        echo "<h1>Error Crítico del Sistema</h1>";
        echo "<p>{$title}: {$message}</p>";
        echo "<hr><small>Nota: No se encontró el archivo de plantilla de errores.</small>";
    }
    exit;
}

// Verifica la existencia del autoloader de Composer
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    renderCriticalError('Error de Dependencias', 'No se encuentra vendor/autoload.php. Ejecuta "composer install".');
}

require __DIR__ . '/../vendor/autoload.php';

// INICIAR SESIÓN (Necesario para Auth)
session_start();

// Configura el manejador global de excepciones
set_exception_handler(function ($e) {
    try {
        // Registra el error en el log antes de intentar mostrar la vista
        Logger::error("Excepción No Controlada", [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        // Intenta usar el sistema MVC para mostrar el error amigable
        $controller = new ErrorController(null);
        $controller->show(500, $e->getMessage());

    } catch (Throwable $t) {
        // Si falla el Logger o el ErrorController, usa el fallback HTML
        renderCriticalError('Fallo Crítico del Sistema', $e->getMessage());
    }
});

try {
    // Carga las variables de entorno
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();

    // Verifica y carga la configuración de la base de datos
    $configPath = __DIR__ . '/../config/database.php';
    if (!file_exists($configPath)) {
        throw new Exception("El archivo de configuración config/database.php no existe.");
    }
    $dbConfig = require $configPath;

    // Inicializa la conexión a la base de datos
    $database = new Database($dbConfig['pgsql']);

    // Inicializa los componentes del núcleo
    $request = new Request();
    $router = new Router($request, $database);

    // Carga las definiciones de rutas
    require __DIR__ . '/../routes/web.php';

    // Resuelve la petición actual
    $router->resolve();

} catch (Exception $e) {
    
    // Registra el error de inicialización
    Logger::error("Error de Inicialización del Core", ['message' => $e->getMessage()]);

    // Intenta usar ErrorController, si no, usa el crítico
    if (class_exists(ErrorController::class)) {
        try {
            (new ErrorController(null))->show(500, $e->getMessage());
        } catch (Throwable $t) {
            renderCriticalError('Error de Inicialización', $e->getMessage());
        }
    } else {
         renderCriticalError('Error de Inicialización', $e->getMessage());
    }
}