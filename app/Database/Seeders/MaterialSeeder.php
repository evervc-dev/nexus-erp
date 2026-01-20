<?php

return function (PDO $pdo) {
    echo "🧱 Sembrando Catálogo de Materiales...\n";

    $materials = [
        ['name' => 'Cemento Portland (Bolsa 42.5kg)', 'unit' => 'Bolsa', 'price' => 9.50, 'sku' => 'CEM-001'],
        ['name' => 'Arena de Río (Metro Cúbico)', 'unit' => 'm3', 'price' => 35.00, 'sku' => 'ARN-001'],
        ['name' => 'Grava 3/4 (Metro Cúbico)', 'unit' => 'm3', 'price' => 38.00, 'sku' => 'GRV-001'],
        ['name' => 'Varilla de Hierro 3/8 (Corrugada)', 'unit' => 'Varilla', 'price' => 4.25, 'sku' => 'HIE-038'],
        ['name' => 'Varilla de Hierro 1/2 (Corrugada)', 'unit' => 'Varilla', 'price' => 7.50, 'sku' => 'HIE-050'],
        ['name' => 'Bloque de Concreto 15x20x40', 'unit' => 'Unidad', 'price' => 0.65, 'sku' => 'BLO-015'],
        ['name' => 'Ladrillo de Obra (Barro cocido)', 'unit' => 'Unidad', 'price' => 0.35, 'sku' => 'LAD-001'],
        ['name' => 'Alambre de Amarre', 'unit' => 'Libra', 'price' => 1.25, 'sku' => 'ALA-001'],
    ];

    $stmt = $pdo->prepare("INSERT INTO materials (name, unit, unit_price, sku) VALUES (:name, :unit, :price, :sku) ON CONFLICT (sku) DO NOTHING");

    foreach ($materials as $m) {
        $stmt->execute([
            'name' => $m['name'], 
            'unit' => $m['unit'], 
            'price' => $m['price'], 
            'sku' => $m['sku']
        ]);
    }
    
    echo "   + Materiales insertados correctamente.\n";
};