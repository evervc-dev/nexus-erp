<?php

return function (PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            project_id INT REFERENCES projects(id) ON DELETE CASCADE,
            assigned_to INT REFERENCES users(id), -- Opcional: Tarea para un empleado específico
            title VARCHAR(150) NOT NULL,
            description TEXT,
            due_date DATE,
            status VARCHAR(20) DEFAULT 'pending', -- pending, in_progress, completed
            completed_at TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
};