<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\ProjectRepository;
use App\Models\Project;
use App\Repositories\BudgetRepository;
use App\Repositories\UserRepository;
use App\Repositories\TaskRepository;

class ProjectController extends Controller
{
    private ProjectRepository $projectRepo;
    private BudgetRepository $budgetRepo;
    private UserRepository $userRepo;
    private TaskRepository $taskRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->projectRepo = new ProjectRepository($db);
        $this->budgetRepo = new BudgetRepository($db);
        $this->userRepo = new UserRepository($db);
        $this->taskRepo = new TaskRepository($db);
    }

    /**
     * Muestra todos los proyectos (Grid View)
     */
    public function index(): void
    {
        // Obtenemos contexto del usuario
        $userId = $_SESSION['user_id'];
        $roleName = $_SESSION['role_name'];

        // El repo decide qué mostrarnos
        $projects = $this->projectRepo->getAll($userId, $roleName);

        $this->view('projects/index', [
            'title' => 'Proyectos - Nexus ERP',
            'projects' => $projects
        ]);
    }

    /**
     * Formulario de creación (Cargado con Clientes y Maestros)
     */
    public function create(): void
    {
        // Obtiene la lista de clientes y maestros para los selects
        $clients = $this->userRepo->getUsersByRole('Cliente');
        
        $masters = $this->userRepo->getUsersByRole('MaestroObra'); 

        $this->view('projects/create', [
            'title' => 'Nuevo Proyecto',
            'clients' => $clients,
            'masters' => $masters
        ]);
    }

    /**
     * Guardar proyecto con asignaciones
     */
    public function store(): void
    {
        $data = $this->request->getBody();

        // Validaciones básicas
        if (empty($data['name']) || empty($data['budget']) || empty($data['client_id'])) {
            // Manejar error (redirigir o mostrar mensaje)
            $this->redirect('/projects/create?error=missing_fields');
            return;
        }

        // El manager es el usuario logueado (Ingeniero)
        $managerId = $_SESSION['user_id'];

        $project = new Project(
            null,
            $managerId,
            $data['name'],
            $data['location'],
            $data['start_date'],
            $data['end_date'],
            (float) $data['budget'],
            'borrador'
        );

        // Capturar los IDs del formulario
        $clientId = (int) $data['client_id'];
        
        // masters_ids viene como un array si se usa name="masters[]" en el HTML
        // getBody() a veces rompe los arrays (checkboxes). 
        // Por eso se toma 'masters' directamente de $_POST
        $masterIds = [];
        if (isset($_POST['masters']) && is_array($_POST['masters'])) {
            $masterIds = $_POST['masters'];
        }

        try {
            $this->projectRepo->createWithAssignments($project, $clientId, $masterIds);
            $this->redirect('/projects');
        } catch (\Exception $e) {
            // Loguear error y mostrar mensaje
            // Logger::error(...)
            die("Error al crear proyecto: " . $e->getMessage()); 
        }
    }

    /**
     * Muestra el Dashboard de un Proyecto específico
     */
    public function show(string $id): void
    {
        $userId = $_SESSION['user_id'];
        $roleName = $_SESSION['role_name'];

        // Busca el proyecto sin reglas de acceso para diferenciar 404 de 403
        $project = $this->projectRepo->findById((int)$id);

        if (!$project) {
            (new ErrorController())->show(
                404,
                "El proyecto con ID [$id] no existe"
            );
            exit;
        }

        if (!$this->projectRepo->userHasAccess($project->id, $userId, $roleName)) {
            (new ErrorController())->show(
                403,
                "No tienes permiso para ver el proyecto con ID [$id]"
            );
            exit;
        }

        // --- LÓGICA PARA PRESUPUESTO ---
        // Obtiene los items que ya están en el presupuesto
        $budgetItems = $this->budgetRepo->getItemsByProject($project->id);
        
        // Obtiene el catálogo completo (para el select de "Agregar Material")
        $allMaterials = $this->budgetRepo->getAllMaterials();
        
        // Calcula cuánto se ha gastado (planificado)
        $totalAllocated = $this->budgetRepo->getTotalBudget($project->id);
        
        // Calcula el remanente
        $remainingBudget = $project->budget - $totalAllocated;

        // --- LÓGICA DE TAREAS ---
        $tasksGrouped = $this->taskRepo->getByProjectGrouped($project->id);
        $assignableUsers = $this->userRepo->getUsersByRole('MaestroObra');

        // Obtenemos usuarios para asignar tareas (solo con rol Maestro de Obras)
        $assignableUsers = $this->userRepo->getUsersByRole('MaestroObra'); 

        // Calcular Totales para KPI
        $pendingCount = count($tasksGrouped['pending']);
        $inProgressCount = count($tasksGrouped['in_progress']);
        $completedCount = count($tasksGrouped['completed']);

        $totalTasks = $pendingCount + $inProgressCount + $completedCount;

        // Regla de 3 simple para el porcentaje
        $progressPercent = ($totalTasks > 0) ? round(($completedCount / $totalTasks) * 100) : 0;

        // --- CÁLCULO DE TIEMPO ---
        $daysRemaining = 0;
        $daysLabel = "No definido";
        
        if ($project->end_date) {
            $end = new \DateTime($project->end_date);
            $now = new \DateTime();
            
            if ($now > $end) {
                $daysRemaining = 0;
                $daysLabel = "Vencido";
            } else {
                $diff = $now->diff($end);
                $daysRemaining = $diff->days;
                $daysLabel = $daysRemaining . " días";
            }
        }

        $this->view('projects/show', [
            'title' => $project->name,
            'project' => $project,
            'budgetItems' => $budgetItems,
            'allMaterials' => $allMaterials,
            'totalAllocated' => $totalAllocated,
            'remainingBudget' => $remainingBudget,
            'tasksGrouped' => $tasksGrouped,
            'assignableUsers' => $assignableUsers,
            'kpi' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedCount,
                'progress' => $progressPercent,
                'days_label' => $daysLabel
            ]
        ]);
    }

    /**
     * Acción para cambiar estado del proyecto (POST)
     */
    public function updateStatus(string $id): void
    {
        $newStatus = $this->request->input('status');
        
        // Validar que sea un estado permitido
        $allowed = ['borrador', 'activo', 'detenido', 'finalizado'];
        
        if (in_array($newStatus, $allowed)) {
            $this->projectRepo->updateStatus((int)$id, $newStatus);
        }

        $this->redirect("/projects/view/$id");
    }
}