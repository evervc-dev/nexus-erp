<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    /**
     * @param array $config El array de configuración (host, port, dbname, etc.)
     */
    public function __construct(array $config)
    {
        // Construimos el DSN dinámicamente según el driver (por defecto pgsql)
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;',
            $config['host'],
            $config['port'],
            $config['dbname']
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Arrays asociativos por defecto
                PDO::ATTR_EMULATE_PREPARES => false, // Seguridad real de PostgreSQL
                PDO::ATTR_STRINGIFY_FETCHES => false, // Mantener tipos de datos (int como int, no string)
            ]);
        } catch (PDOException $e) {
            // En producción no debe mostrar el mensaje exacto al usuario
            die("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}