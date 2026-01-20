<?php

namespace App\Models;

class Material
{
    public ?int $id;
    public string $name;
    public string $unit; // bolsa, metro, unidad
    public float $unit_price;
    public ?string $sku;

    public function __construct(?int $id, string $name, string $unit, float $unit_price, ?string $sku = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->unit = $unit;
        $this->unit_price = $unit_price;
        $this->sku = $sku;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['unit'],
            (float) $data['unit_price'],
            $data['sku'] ?? null
        );
    }
}