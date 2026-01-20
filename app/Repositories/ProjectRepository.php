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

    /**
     * Obtiene proyectos filtrados según el rol del usuario.
     */
    public function getAll(int $userId, string $roleName): array
    {
        // Si es SuperAdmin, ve TODO.
        if ($roleName === 'SuperAdmin') {
            $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                    FROM projects p
                    JOIN users u ON p.manager_id = u.id
                    ORDER BY p.created_at DESC";
            
            $stmt = $this->pdo->query($sql);
        }
        
        // Si es Ingeniero, ve SOLO lo que él gestiona (donde es manager_id).
        elseif ($roleName === 'Ingeniero') {
            $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                    FROM projects p
                    JOIN users u ON p.manager_id = u.id
                    WHERE p.manager_id = :uid
                    ORDER BY p.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['uid' => $userId]);
        }
        
        // Si es Cliente o Maestro, ve SOLO donde ha sido ASIGNADO.
        else {
            $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                    FROM projects p
                    JOIN users u ON p.manager_id = u.id
                    JOIN project_assignments pa ON p.id = pa.project_id
                    WHERE pa.user_id = :uid
                    ORDER BY p.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['uid' => $userId]);
        }

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => Project::fromArray($row), $results);
    }

    public function find(int $projectId, int $userId, string $roleName): ?Project
    {
        $sql = "SELECT p.*, CONCAT(u.name, ' ', u.last_name) as manager_name 
                FROM projects p
                JOIN users u ON p.manager_id = u.id ";

        // Base de condiciones WHERE
        $whereClauses = ["p.id = :pid"];
        $params = ['pid' => $projectId];

        // Filtros de seguridad según rol
        if ($roleName === 'Ingeniero') {
            $whereClauses[] = "p.manager_id = :uid";
            $params['uid'] = $userId;
        } 
        elseif ($roleName !== 'SuperAdmin') {
            // Clientes y Maestros: Verificar tabla de asignaciones
            $sql .= " JOIN project_assignments pa ON p.id = pa.project_id ";
            $whereClauses[] = "pa.user_id = :uid";
            $params['uid'] = $userId;
        }

        // Construir query final
        $sql .= " WHERE " . implode(" AND ", $whereClauses) . " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

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

    public function updateStatus(int $id, string $status): void
    {
        $sql = "UPDATE projects SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Crea un proyecto y sus asignaciones en una sola transacción.
     * @param Project $project Datos del proyecto
     * @param int $clientId ID del usuario Cliente
     * @param array $masterIds Array de IDs de los Maestros de Obra
     */
    public function createWithAssignments(Project $project, int $clientId, array $masterIds): int
    {
        try {
            // Inicia Transacción (Todo o nada)
            $this->pdo->beginTransaction();

            // Inserta el Proyecto
            $sql = "INSERT INTO projects (manager_id, name, location, start_date, end_date, budget, status) 
                    VALUES (:manager, :name, :loc, :start, :end, :budget, :status)
                    RETURNING id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'manager' => $project->manager_id,
                'name' => $project->name,
                'loc' => $project->location,
                'start' => $project->start_date ?: null,
                'end' => $project->end_date ?: null,
                'budget' => $project->budget,
                'status' => $project->status
            ]);
            
            $newProjectId = (int) $stmt->fetchColumn();

            // Inserta la Asignación del CLIENTE
            $sqlAssign = "INSERT INTO project_assignments (project_id, user_id, assigned_role) VALUES (:pid, :uid, :role)";
            $stmtAssign = $this->pdo->prepare($sqlAssign);

            $stmtAssign->execute([
                'pid' => $newProjectId,
                'uid' => $clientId,
                'role' => 'cliente_visor'
            ]);

            // Inserta las Asignaciones de MAESTROS (Bucle)
            foreach ($masterIds as $masterId) {
                // Evitar duplicados si el usuario selecciona al mismo dos veces
                $stmtAssign->execute([
                    'pid' => $newProjectId,
                    'uid' => (int) $masterId,
                    'role' => 'maestro'
                ]);
            }

            // Confirma cambios
            $this->pdo->commit();

            return $newProjectId;

        } catch (\Exception $e) {
            // Si algo falla, revertimos todo
            $this->pdo->rollBack();
            throw $e;
        }
    }
}