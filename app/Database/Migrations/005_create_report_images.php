<?php

return function (PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS report_images (
            id SERIAL PRIMARY KEY,
            report_id INT REFERENCES daily_reports(id) ON DELETE CASCADE,
            image_url VARCHAR(255) NOT NULL, -- La URL para verla
            public_id VARCHAR(100), -- ID interno de Cloudinary (para poder borrarla después)
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
};