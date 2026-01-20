<?php

namespace App\Models;

class Task
{
    public ?int $id;
    public int $project_id;
    public ?int $assigned_to; // Referencia a users(id)
    public string $title;
    public ?string $description;
    public ?string $due_date;
    public string $status;
    public ?string $completed_at;
    
    // Propiedad extendida (JOIN con Users)
    public ?string $assigned_user_name = null;

    public function __construct(
        ?int $id, 
        int $project_id, 
        ?int $assigned_to, 
        string $title, 
        ?string $description, 
        ?string $due_date, 
        string $status = 'pending',
        ?string $completed_at = null
    ) {
        $this->id = $id;
        $this->project_id = $project_id;
        $this->assigned_to = $assigned_to;
        $this->title = $title;
        $this->description = $description;
        $this->due_date = $due_date;
        $this->status = $status;
        $this->completed_at = $completed_at;
    }

    public static function fromArray(array $data): self
    {
        $task = new self(
            $data['id'] ?? null,
            (int) $data['project_id'],
            !empty($data['assigned_to']) ? (int) $data['assigned_to'] : null,
            $data['title'],
            $data['description'] ?? null,
            $data['due_date'] ?? null,
            $data['status'] ?? 'pending',
            $data['completed_at'] ?? null
        );

        // Mapea el nombre del usuario asignado si viene del JOIN
        if (isset($data['assigned_user_name'])) {
            $task->assigned_user_name = $data['assigned_user_name'];
        }

        return $task;
    }
}