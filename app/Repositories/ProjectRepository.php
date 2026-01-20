<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Project;
use PDO;

class ProjectRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    public function getAll(): array
    {
        // Unimos con users para saber quién es el manager
        $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                FROM projects p
                JOIN users u ON p.manager_id = u.id
                ORDER BY p.created_at DESC";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => Project::fromArray($row), $results);
    }

    public function find(int $id): ?Project
    {
        $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                FROM projects p
                JOIN users u ON p.manager_id = u.id
                WHERE p.id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Project::fromArray($row) : null;
    }

    public function save(Project $project): int
    {
        if ($project->id) {
            // Aquí iría el update... lo haremos cuando editemos
            return 0; 
        }
        return $this->create($project);
    }

    private function create(Project $project): int
    {
        $sql = "INSERT INTO projects (manager_id, name, location, start_date, end_date, budget, status) 
                VALUES (:manager, :name, :loc, :start, :end, :budget, :status)
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'manager' => $project->manager_id,
            'name' => $project->name,
            'loc' => $project->location,
            'start' => $project->start_date ?: null, // Manejar strings vacíos
            'end' => $project->end_date ?: null,
            'budget' => $project->budget,
            'status' => $project->status
        ]);

        return (int) $stmt->fetchColumn();
    }
}