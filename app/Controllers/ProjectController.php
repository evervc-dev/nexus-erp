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
        
        // masters_ids vendrá como un array si usamos name="masters[]" en el HTML
        // Si no se selecciona ninguno, asignamos array vacío
        $masterIds = $data['masters'] ?? []; 

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

        // Busca por ID con contexto de usuario
        $project = $this->projectRepo->find((int)$id, $userId, $roleName);

        // Si no existe, error 404
        if (!$project) {
            (new ErrorController())->show(
                404,
                "El proyecto con ID ['$id'] no existe o no tienes permiso para verlo"
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

        // Obtenemos usuarios para asignar (podrías filtrar por rol si quisieras)
        $assignableUsers = $this->userRepo->getUsersByRole('MaestroObra'); 

        $this->view('projects/show', [
            'title' => $project->name,
            'project' => $project,
            'budgetItems' => $budgetItems,
            'allMaterials' => $allMaterials,
            'totalAllocated' => $totalAllocated,
            'remainingBudget' => $remainingBudget,
            'tasksGrouped' => $tasksGrouped,
            'assignableUsers' => $assignableUsers
        ]);
    }
}