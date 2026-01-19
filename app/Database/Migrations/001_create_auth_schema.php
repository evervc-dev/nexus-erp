<?php

return function (PDO $pdo) {
    // Roles
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            description TEXT
        )
    ");

    // Permisos (Capabilities)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS permissions (
            id SERIAL PRIMARY KEY,
            slug VARCHAR(50) UNIQUE NOT NULL, -- ej: 'project.create'
            description VARCHAR(100)
        )
    ");

    // Pivote Roles <-> Permisos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INT REFERENCES roles(id) ON DELETE CASCADE,
            permission_id INT REFERENCES permissions(id) ON DELETE CASCADE,
            PRIMARY KEY (role_id, permission_id)
        )
    ");

    // Usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            role_id INT REFERENCES roles(id), -- Rol principal
            name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
};