<?php

namespace App\Models;

class Project
{
    public ?int $id;
    public int $manager_id;
    public string $name;
    public ?string $location;
    public ?string $start_date;
    public ?string $end_date;
    public float $budget;
    public string $status; // 'borrador', 'activo', 'detenido', 'finalizado'
    public ?string $created_at;

    // Propiedad extra para mostrar el nombre del Ingeniero a cargo
    public ?string $manager_name = null;

    public function __construct(
        ?int $id,
        int $manager_id,
        string $name,
        ?string $location,
        ?string $start_date,
        ?string $end_date,
        float $budget,
        string $status = 'borrador',
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->manager_id = $manager_id;
        $this->name = $name;
        $this->location = $location;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->budget = $budget;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    public static function fromArray(array $data): self
    {
        $project = new self(
            $data['id'] ?? null,
            (int) $data['manager_id'],
            $data['name'],
            $data['location'] ?? null,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            (float) ($data['budget'] ?? 0),
            $data['status'] ?? 'borrador',
            $data['created_at'] ?? null
        );

        if (isset($data['manager_name'])) {
            $project->manager_name = $data['manager_name'];
        }

        return $project;
    }

    // Helper para colores de estado en Bootstrap
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'activo' => 'success',
            'detenido' => 'danger',
            'finalizado' => 'primary',
            default => 'secondary', // borrador
        };
    }
}