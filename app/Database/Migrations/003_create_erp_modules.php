<?php

return function (PDO $pdo) {
    // Catálogo de Materiales
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS materials (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            unit VARCHAR(20) NOT NULL, -- 'bolsa', 'metro', 'unidad'
            unit_price NUMERIC(12, 2) NOT NULL DEFAULT 0, -- Precio actual de mercado
            sku VARCHAR(50) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Presupuestos Detallados (Items del proyecto)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS project_budget_items (
            id SERIAL PRIMARY KEY,
            project_id INT REFERENCES projects(id) ON DELETE CASCADE,
            material_id INT REFERENCES materials(id),
            quantity NUMERIC(10, 2) NOT NULL,
            historical_cost NUMERIC(12, 2) NOT NULL, -- Precio al momento de crear el presupuesto
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Bitácora de Obra (Reportes Diarios)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS daily_reports (
            id SERIAL PRIMARY KEY,
            project_id INT REFERENCES projects(id) ON DELETE CASCADE,
            author_id INT REFERENCES users(id), -- Quién escribió el reporte
            report_date DATE NOT NULL DEFAULT CURRENT_DATE,
            content TEXT NOT NULL,
            incidents_flag BOOLEAN DEFAULT FALSE, -- ¿Hubo accidentes o problemas graves?
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
};