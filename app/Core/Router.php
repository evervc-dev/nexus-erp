<?php

namespace App\Core;

use App\Controllers\ErrorController;

class Router
{
    private Request $request;
    private Database $database;
    
    // Las rutas guardan más info: ['callback' => ..., 'middlewares' => []]
    private array $routes = [];
    
    // Guarda la última ruta registrada para poder encadenarle middlewares
    private ?string $lastRoutePath = null;
    private ?string $lastRouteMethod = null;

    public function __construct(Request $request, Database $database)
    {
        $this->request = $request;
        $this->database = $database;
    }

    public function get(string $path, array $callback): self
    {
        return $this->addRoute('get', $path, $callback);
    }

    public function post(string $path, array $callback): self
    {
        return $this->addRoute('post', $path, $callback);
    }

    /**
     * Método interno para registrar la ruta y permitir encadenamiento
     */
    private function addRoute(string $method, string $path, array $callback): self
    {
        $this->routes[$method][$path] = [
            'callback' => $callback,
            'middlewares' => [] // Array vacío por defecto
        ];

        // Guardamos referencia de cuál fue la última ruta agregada
        $this->lastRoutePath = $path;
        $this->lastRouteMethod = $method;

        return $this; // Retornamos $this para permitir ->middleware(...)
    }

    /**
     * Asigna un middleware a la última ruta registrada.
     * Uso: $router->get(...)->middleware('auth');
     */
    public function middleware(string $key): self
    {
        if ($this->lastRoutePath && $this->lastRouteMethod) {
            $this->routes[$this->lastRouteMethod][$this->lastRoutePath]['middlewares'][] = $key;
        }
        return $this;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        // Busca coincidencia exacta
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            return $this->runMiddlewaresAndDispatch($route, []);
        }

        // Busca coincidencias dinámicas (Regex)
        foreach ($this->routes[$method] as $routePath => $routeConfig) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routePath);
            $pattern = "@^" . $pattern . "$@";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return $this->runMiddlewaresAndDispatch($routeConfig, $matches);
            }
        }

        // 3. 404 Not Found
        $errorController = new ErrorController();
        $errorController->show(404, "Ruta no encontrada: $path");
        exit;
    }

    /**
     * Ejecuta los middlewares y luego el controlador
     */
    private function runMiddlewaresAndDispatch(array $routeConfig, array $params)
    {
        // Ejecuta Middlewares
        foreach ($routeConfig['middlewares'] as $middlewareKey) {
            $this->executeMiddleware($middlewareKey);
        }

        // Ejecuta Controlador
        $callback = $routeConfig['callback'];
        $controllerClass = $callback[0];
        $action = $callback[1];

        $controller = new $controllerClass($this->database, $this->request);

        if (!method_exists($controller, $action)) {
             (new ErrorController())->show(500, "Método '$action' no encontrado en el controlador.");
             return;
        }

        return call_user_func_array([$controller, $action], $params);
    }

    /**
     * Mapa de alias de middlewares a clases reales
     */
    private function executeMiddleware(string $key): void
    {
        // Separa el alias de los parámetros (ej: "role:Admin,Editor")
        $parts = explode(':', $key, 2); 
        $alias = $parts[0]; // "role"
        $params = [];

        if (isset($parts[1])) {
            // Convertir "Admin,Editor" en array ['Admin', 'Editor']
            $params = explode(',', $parts[1]); 
        }

        // Define el mapa con las clases de middleware
        $map = [
            'auth' => \App\Middlewares\AuthMiddleware::class,
            'role' => \App\Middlewares\RoleMiddleware::class,
        ];

        if (isset($map[$alias])) {
            $middlewareClass = $map[$alias];
            $middleware = new $middlewareClass();
            
            // Ejecuta pasando los parámetros usando el operador 'spread' (...)
            // Esto permite que el método handle reciba (string ...$roles)
            $middleware->handle(...$params); 
        } else {
            throw new \Exception("Middleware '$alias' no está registrado en el Router.");
        }
    }
}