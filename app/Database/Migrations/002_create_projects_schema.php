<?php

return function (PDO $pdo) {
    // Proyectos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id SERIAL PRIMARY KEY,
            manager_id INT REFERENCES users(id), -- Ingeniero responsable
            name VARCHAR(150) NOT NULL,
            location VARCHAR(255),
            start_date DATE,
            end_date DATE,
            budget NUMERIC(15, 2) DEFAULT 0.00, -- Presupuesto general estimado
            status VARCHAR(20) DEFAULT 'borrador', -- borrador, activo, detenido, finalizado
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Asignaciones (Quién trabaja en qué proyecto, aparte del manager)
    // Esto es útil para que el 'MaestroObra' o 'Cliente' vean SOLO sus proyectos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_assignments (
            project_id INT REFERENCES projects(id) ON DELETE CASCADE,
            user_id INT REFERENCES users(id) ON DELETE CASCADE,
            assigned_role VARCHAR(50), -- 'maestro', 'cliente_visor'
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (project_id, user_id)
        )
    ");
};