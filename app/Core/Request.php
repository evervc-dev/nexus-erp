<?php

namespace App\Core;

class Request
{
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Quita query params (ej: ?id=1)
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    /**
     * Obtiene todos los datos limpios de la petición
     */
    public function getBody(): array
    {
        $body = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        // EXTRA: Soporte para JSON (Para cuando hagas APIs con Fetch/Axios)
        $inputJSON = file_get_contents('php://input');
        if (!empty($inputJSON)) {
            $data = json_decode($inputJSON, true);
            if (is_array($data)) {
                $body = array_merge($body, $data);
            }
        }

        return $body;
    }

    /**
     * Helper para obtener un solo dato o un valor por defecto
     */
    public function input(string $key, $default = null)
    {
        $body = $this->getBody();
        return $body[$key] ?? $default;
    }
}