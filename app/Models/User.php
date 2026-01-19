<?php

namespace App\Models;

class User
{
    public ?int $id;
    public int $role_id;
    public string $name;
    public string $last_name;
    public string $email;
    public string $password_hash;
    public ?string $phone;
    public bool $is_active;
    public ?string $created_at;

    // Propiedad extra para cuando hacemos JOIN con roles (opcional pero útil)
    public ?string $role_name = null;

    public function __construct(
        ?int $id,
        int $role_id,
        string $name,
        string $last_name,
        string $email,
        string $password_hash,
        ?string $phone = null,
        bool $is_active = true,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->role_id = $role_id;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->phone = $phone;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }

    /**
     * Factory: Crea una instancia desde un array de BD
     */
    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? null,
            (int) $data['role_id'],
            $data['name'],
            $data['last_name'],
            $data['email'],
            $data['password_hash'],
            $data['phone'] ?? null,
            $data['is_active'] ?? true,
            $data['created_at'] ?? null
        );

        // Si la consulta trajo el nombre del rol, lo asignamos
        if (isset($data['role_name'])) {
            $user->role_name = $data['role_name'];
        }

        return $user;
    }
}