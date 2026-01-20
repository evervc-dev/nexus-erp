<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    /**
     * Obtiene todos los usuarios (idealmente para listas desplegables).
     * @param bool $onlyActive Si es true, filtra solo los usuarios activos.
     * @return User[]
     */
    public function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id";
        
        if ($onlyActive) {
            $sql .= " WHERE u.is_active = TRUE";
        }

        // Ordenamos alfabéticamente para facilitar la búsqueda
        $sql .= " ORDER BY u.name ASC, u.last_name ASC";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => User::fromArray($row), $results);
    }

    /**
     * Obtiene usuarios que pertenezcan a un rol específico (ej: 'Maestro de Obra')
     */
    public function getUsersByRole(string $roleName): array
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE r.name = :role AND u.is_active = TRUE
                ORDER BY u.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role' => $roleName]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => User::fromArray($row), $results);
    }

    public function findByEmail(string $email): ?User
    {
        // Unimos con roles para tener la info completa de una vez
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.email = :email LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? User::fromArray($row) : null;
    }

    public function find(int $id): ?User
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? User::fromArray($row) : null;
    }

    /**
     * Busca el ID de un rol por su nombre.
     */
    public function getRoleIdByName(string $roleName): ?int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM roles WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $roleName]);
        
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    public function save(User $user): int
    {
        // Lógica simple: Si tiene ID es update, sino insert
        if ($user->id) {
            return $this->update($user);
        }

        return $this->create($user);
    }

    private function create(User $user): int
    {
        $sql = "INSERT INTO users (role_id, name, last_name, email, phone, is_active, password_hash, created_at) 
                VALUES (:role_id, :name, :last_name, :email, :phone, :is_active, :password_hash, NOW()) 
                RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'role_id'       => $user->role_id,
            'name'          => $user->name,
            'last_name'     => $user->last_name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'is_active'     => $user->is_active,
            'password_hash' => $user->password_hash
        ]);

        // Actualizamos el ID del objeto original
        $id = (int) $stmt->fetchColumn();
        $user->id = $id;

        return $id;
    }

    private function update(User $user): int
    {
        $sql = "UPDATE users 
                SET role_id = :role_id, 
                    name = :name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    is_active = :is_active, 
                    password_hash = :password_hash 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id'            => $user->id,
            'role_id'       => $user->role_id,
            'name'          => $user->name,
            'last_name'     => $user->last_name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'is_active'     => $user->is_active,
            'password_hash' => $user->password_hash
        ]);

        return $user->id;
    }
}