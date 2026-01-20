<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\ProjectRepository;
use App\Models\Project;

class ProjectController extends Controller
{
    private ProjectRepository $projectRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->projectRepo = new ProjectRepository($db);
    }

    /**
     * Muestra todos los proyectos (Grid View)
     */
    public function index(): void
    {
        $projects = $this->projectRepo->getAll();

        $this->view('projects/index', [
            'title' => 'Proyectos - Nexus ERP',
            'projects' => $projects
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(): void
    {
        $this->view('projects/create', [
            'title' => 'Nuevo Proyecto'
        ]);
    }

    /**
     * Guardar proyecto
     */
    public function store(): void
    {
        $data = $this->request->getBody();

        // Validaciones simples
        if (empty($data['name']) || empty($data['budget'])) {
            $this->view('projects/create', ['error' => 'Nombre y Presupuesto son obligatorios']);
            return;
        }

        // El manager es el usuario logueado (asumiendo que es Ingeniero/Admin)
        $managerId = $_SESSION['user_id'];

        $project = new Project(
            null,
            $managerId,
            $data['name'],
            $data['location'],
            $data['start_date'],
            $data['end_date'],
            (float) $data['budget'],
            'borrador' // Estado inicial
        );

        $this->projectRepo->save($project);

        $this->redirect('/projects');
    }

    /**
     * Muestra el Dashboard de un Proyecto específico
     */
    public function show(string $id): void
    {
        // 1. Convertir ID y buscar
        $project = $this->projectRepo->find((int)$id);

        // 2. Si no existe, error 404
        if (!$project) {
            // Podrías lanzar una excepción o llamar al ErrorController manual
            // Por simplicidad, redirigimos con error por ahora
            header('Location: /projects'); 
            exit;
        }

        // 3. (AQUÍ VA LA LÓGICA DE PERMISOS FUTURA)
        // Ejemplo: Si soy 'Cliente', verificar si $project->id está en mis asignaciones.

        // 4. Cargar vista
        $this->view('projects/show', [
            'title' => $project->name,
            'project' => $project
        ]);
    }
}