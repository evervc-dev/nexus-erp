<?php

namespace App\Models;

class ProjectBudgetItem
{
    public ?int $id;
    public int $project_id;
    public int $material_id;
    public float $quantity;
    public float $historical_cost; // Precio al momento de agregar
    public ?string $notes;

    // Propiedades extendidas (JOIN con Materiales)
    public ?string $material_name = null;
    public ?string $material_unit = null;

    public function __construct(
        ?int $id,
        int $project_id,
        int $material_id,
        float $quantity,
        float $historical_cost,
        ?string $notes = null
    ) {
        $this->id = $id;
        $this->project_id = $project_id;
        $this->material_id = $material_id;
        $this->quantity = $quantity;
        $this->historical_cost = $historical_cost;
        $this->notes = $notes;
    }

    public static function fromArray(array $data): self
    {
        $item = new self(
            $data['id'] ?? null,
            (int) $data['project_id'],
            (int) $data['material_id'],
            (float) $data['quantity'],
            (float) $data['historical_cost'],
            $data['notes'] ?? null
        );

        if (isset($data['material_name'])) {
            $item->material_name = $data['material_name'];
            $item->material_unit = $data['material_unit'];
        }

        return $item;
    }

    // Calcula el subtotal de esta línea
    public function getSubtotal(): float
    {
        return $this->quantity * $this->historical_cost;
    }
}