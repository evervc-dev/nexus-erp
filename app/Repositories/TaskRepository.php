<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Task;
use PDO;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    public function create(Task $task): void
    {
        $sql = "INSERT INTO tasks (project_id, assigned_to, title, description, due_date, status) 
                VALUES (:pid, :uid, :title, :desc, :due, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'pid' => $task->project_id,
            'uid' => $task->assigned_to, // Puede ser null
            'title' => $task->title,
            'desc' => $task->description,
            'due' => $task->due_date ?: null,
            'status' => $task->status
        ]);
    }

    public function updateStatus(int $taskId, string $newStatus): void
    {
        // Si el estado es 'completed', guardamos la fecha actual. Si no, null.
        $completedAt = ($newStatus === 'completed') ? date('Y-m-d H:i:s') : null;

        $sql = "UPDATE tasks SET status = :status, completed_at = :completed WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $newStatus, 
            'completed' => $completedAt,
            'id' => $taskId
        ]);
    }

    public function getByProjectGrouped(int $projectId): array
    {
        // JOIN con users para obtener nombre y apellido del asignado
        $sql = "SELECT t.*, CONCAT(u.name, ' ', u.last_name) as assigned_user_name
                FROM tasks t
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.project_id = :pid
                ORDER BY t.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $projectId]);
        $allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [
            'pending' => [],
            'in_progress' => [],
            'completed' => []
        ];

        foreach ($allTasks as $row) {
            $task = Task::fromArray($row);
            if (array_key_exists($task->status, $grouped)) {
                $grouped[$task->status][] = $task;
            }
        }

        return $grouped;
    }
}