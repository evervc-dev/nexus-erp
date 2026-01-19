<?php

namespace App\Models;

class Employee
{
    public ?int $id;
    public string $first_name;
    public string $last_name;
    public ?string $dui;
    public string $position;
    public ?string $phone;
    public float $daily_salary;
    public bool $is_active;
    public ?string $created_at;

    public function __construct(
        ?int $id,
        string $first_name,
        string $last_name,
        ?string $dui,
        string $position,
        ?string $phone,
        float $daily_salary,
        bool $is_active = true,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->dui = $dui;
        $this->position = $position;
        $this->phone = $phone;
        $this->daily_salary = $daily_salary;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['dui'] ?? null,
            $data['position'],
            $data['phone'] ?? null,
            (float) ($data['daily_salary'] ?? 0),
            (bool) ($data['is_active'] ?? true),
            $data['created_at'] ?? null
        );
    }
    
    // Helper para nombre completo
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}