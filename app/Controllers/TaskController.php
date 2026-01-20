<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\TaskRepository;
use App\Models\Task;

class TaskController extends Controller
{
    private TaskRepository $taskRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->taskRepo = new TaskRepository($db);
    }

    public function store(): void
    {
        $data = $this->request->getBody();
        $projectId = (int) $data['project_id'];

        // Validación: Título es obligatorio
        if (empty($data['title'])) {
             // Redirigir con error (idealmente usarías flash messages)
             $this->redirect("/projects/view/$projectId?tab=tasks");
             return;
        }

        $task = new Task(
            null,
            $projectId,
            !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            $data['title'],
            $data['description'] ?? null,
            $data['due_date'] ?? null,
            'pending'
        );

        $this->taskRepo->create($task);
        $this->redirect("/projects/view/$projectId?tab=tasks");
    }

    public function updateStatus(string $id): void
    {
        $newStatus = $this->request->input('status');
        $projectId = $this->request->input('project_id');

        if ($newStatus) {
            $this->taskRepo->updateStatus((int)$id, $newStatus);
        }

        $this->redirect("/projects/view/$projectId?tab=tasks");
    }
}