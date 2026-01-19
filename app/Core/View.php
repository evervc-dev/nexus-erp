<?php

namespace App\Core;

use Exception;

class View
{
    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $view  Nombre de la vista (ej: 'home', 'projects/index')
     * @param array  $data  Datos a pasar a la vista ['titulo' => 'Hola']
     * @throws Exception Si la vista o el layout no existen.
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../../views/layouts/main.php';

        // Valida Vista
        if (!file_exists($viewFile)) {
            // LANZA la "papa caliente" (error) hacia arriba
            throw new Exception("Error Crítico: La vista '{$view}' no fue encontrada en: {$viewFile}");
        }

        // Captura contenido
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Valida y Renderiza Layout
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            throw new Exception("Error Crítico: El layout principal no existe.");
        }
    }
}