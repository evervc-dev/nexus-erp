<?php

use App\Core\Logger;

return function (PDO $pdo) {
    echo "🌱 Iniciando Seeder de Usuarios y Roles...\n";

    // 1. DEFINICIÓN DE ROLES
    $roles = [
        ['name' => 'SuperAdmin', 'desc' => 'Control total del sistema ERP'],
        ['name' => 'Ingeniero', 'desc' => 'Gestión de proyectos, presupuestos y personal'],
        ['name' => 'MaestroObra', 'desc' => 'Supervisión de campo y actualización de tareas'],
        ['name' => 'Cliente', 'desc' => 'Visualización de avances en sus proyectos asignados'],
    ];

    $stmtCheckRole = $pdo->prepare("SELECT id FROM roles WHERE name = :name");
    $stmtInsertRole = $pdo->prepare("INSERT INTO roles (name, description) VALUES (:name, :desc)");

    foreach ($roles as $role) {
        $stmtCheckRole->execute(['name' => $role['name']]);
        if (!$stmtCheckRole->fetch()) {
            $stmtInsertRole->execute($role);
            echo "   + Rol creado: {$role['name']}\n";
        } else {
            echo "   . Rol existente: {$role['name']}\n";
        }
    }

    // 2. CREAR SUPER ADMIN
    $adminEmail = 'admin@nexus.com';
    $password = 'admin123'; // Contraseña por defecto

    // Obtener ID del rol SuperAdmin
    $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = 'SuperAdmin'");
    $stmtRole->execute();
    $roleId = $stmtRole->fetchColumn();

    if (!$roleId) {
        Logger::error("Fallo al seedear: No se encontró el rol SuperAdmin.");
        echo "❌ Error: Rol SuperAdmin no encontrado.\n";
        return;
    }

    // Verificar si el usuario ya existe
    $stmtCheckUser = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmtCheckUser->execute(['email' => $adminEmail]);

    if (!$stmtCheckUser->fetch()) {
        $stmtInsertUser = $pdo->prepare("
            INSERT INTO users (role_id, name, last_name, email, password_hash, phone, is_active, created_at) 
            VALUES (:role_id, 'EverVC', 'Admin', :email, :hash, '12345678', TRUE, NOW())
        ");
        
        $stmtInsertUser->execute([
            'role_id' => $roleId,
            'email'   => $adminEmail,
            'hash'    => password_hash($password, PASSWORD_BCRYPT)
        ]);
        
        echo "   👤 SuperAdmin creado: {$adminEmail} (Pass: {$password})\n";
    } else {
        echo "   . SuperAdmin ya existe ({$adminEmail})\n";
    }
};