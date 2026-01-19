<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Employee;
use PDO;

class EmployeeRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    /**
     * Obtener todos los empleados activos
     * @return Employee[]
     */
    public function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM employees";
        if ($onlyActive) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY last_name ASC";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Employee::fromArray($row), $results);
    }

    public function save(Employee $employee): int
    {
        if ($employee->id) {
            return $this->update($employee);
        }
        return $this->create($employee);
    }

    private function create(Employee $employee): int
    {
        $sql = "INSERT INTO employees (first_name, last_name, dui, position, phone, daily_salary, is_active) 
                VALUES (:first, :last, :dui, :pos, :phone, :salary, :active) 
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'first' => $employee->first_name,
            'last' => $employee->last_name,
            'dui' => $employee->dui,
            'pos' => $employee->position,
            'phone' => $employee->phone,
            'salary' => $employee->daily_salary,
            'active' => $employee->is_active ? 'true' : 'false'
        ]);

        return (int) $stmt->fetchColumn();
    }

    private function update(Employee $employee): int
    {
        $sql = "UPDATE employees SET 
                first_name = :first, last_name = :last, dui = :dui, 
                position = :pos, phone = :phone, daily_salary = :salary, is_active = :active
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $employee->id,
            'first' => $employee->first_name,
            'last' => $employee->last_name,
            'dui' => $employee->dui,
            'pos' => $employee->position,
            'phone' => $employee->phone,
            'salary' => $employee->daily_salary,
            'active' => $employee->is_active ? 'true' : 'false'
        ]);

        return $employee->id;
    }
}