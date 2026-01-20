<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\ProjectBudgetItem;
use App\Models\Material;
use PDO;

class BudgetRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    /**
     * Obtiene todos los items del presupuesto de un proyecto
     */
    public function getItemsByProject(int $projectId): array
    {
        // JOIN vital para mostrar nombre y unidad del material
        $sql = "SELECT pbi.*, m.name as material_name, m.unit as material_unit
                FROM project_budget_items pbi
                JOIN materials m ON pbi.material_id = m.id
                WHERE pbi.project_id = :pid
                ORDER BY pbi.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $projectId]);

        return array_map(fn($row) => ProjectBudgetItem::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Obtiene todo el catálogo de materiales (para el select del formulario)
     */
    public function getAllMaterials(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM materials ORDER BY name ASC");
        return array_map(fn($row) => Material::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Agrega un material al presupuesto
     */
    public function addItem(int $projectId, int $materialId, float $quantity, ?string $notes): void
    {
        // 1. Buscar precio actual del material para congelarlo
        $stmtPrice = $this->pdo->prepare("SELECT unit_price FROM materials WHERE id = :id");
        $stmtPrice->execute(['id' => $materialId]);
        $currentPrice = (float) $stmtPrice->fetchColumn();

        // 2. Insertar con el precio histórico
        $sql = "INSERT INTO project_budget_items (project_id, material_id, quantity, historical_cost, notes)
                VALUES (:pid, :mid, :qty, :cost, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'pid' => $projectId,
            'mid' => $materialId,
            'qty' => $quantity,
            'cost' => $currentPrice, // ¡Aquí guardamos el precio congelado!
            'notes' => $notes
        ]);
    }
    
    /**
     * Eliminar un item del presupuesto
     */
    public function deleteItem(int $itemId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM project_budget_items WHERE id = :id");
        $stmt->execute(['id' => $itemId]);
    }
    
    /**
     * Calcular total gastado en presupuesto (Suma de subtotales)
     */
    public function getTotalBudget(int $projectId): float
    {
        $sql = "SELECT SUM(quantity * historical_cost) FROM project_budget_items WHERE project_id = :pid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $projectId]);
        
        return (float) $stmt->fetchColumn();
    }
}