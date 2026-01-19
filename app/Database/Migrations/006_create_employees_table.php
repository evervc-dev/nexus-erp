<?php

return function (PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS employees (
            id SERIAL PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            dui VARCHAR(20) UNIQUE, -- Documento de Identidad
            position VARCHAR(50) NOT NULL, -- 'Albañil', 'Peón', 'Carpintero'
            phone VARCHAR(20),
            daily_salary NUMERIC(10, 2) NOT NULL DEFAULT 0.00, -- Base para futuras planillas
            is_active BOOLEAN DEFAULT TRUE, -- Para 'despedir' sin borrar historial
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
};