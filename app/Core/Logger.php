<?php

namespace App\Core;

class Logger
{
    /**
     * Registra un error en el archivo de logs diario.
     * * @param string $message Mensaje descriptivo del error
     * @param array $context Datos adicionales (ej: ID de usuario, array del error)
     */
    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    /**
     * Registra información general (debugging).
     */
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        // Definir archivo de log (uno por día para no hacer un archivo gigante)
        // Ejemplo: logs/app-2026-01-19.log
        $date = date('Y-m-d');
        $logFile = __DIR__ . "/../../logs/app-{$date}.log";

        // Formato: [FECHA HORA] [NIVEL] MENSAJE {CONTEXTO JSON}
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        
        $logEntry = "[{$timestamp}] [{$level}] {$message} {$contextString}" . PHP_EOL;

        // Escribir al final del archivo (flag 3 de error_log permite append a archivo)
        // Si el archivo no existe, error_log intenta crearlo.
        error_log($logEntry, 3, $logFile);
    }
}