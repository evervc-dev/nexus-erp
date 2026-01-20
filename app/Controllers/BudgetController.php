<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\BudgetRepository;

class BudgetController extends Controller
{
    private BudgetRepository $budgetRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->budgetRepo = new BudgetRepository($db);
    }

    /**
     * Agrega un item al presupuesto
     */
    public function store(): void
    {
        $data = $this->request->getBody();
        $projectId = (int) $data['project_id'];

        // Validaciones simples
        if (empty($data['material_id']) || empty($data['quantity'])) {
            // En un caso real, manejaríamos errores con Flash Messages.
            // Por ahora redirigimos.
            $this->redirect("/projects/view/$projectId?tab=budget&error=missing_fields");
            return;
        }

        $this->budgetRepo->addItem(
            $projectId,
            (int) $data['material_id'],
            (float) $data['quantity'],
            $data['notes'] ?? null
        );

        // Redirigir de vuelta a la pestaña de presupuesto
        $this->redirect("/projects/view/$projectId?tab=budget");
    }

    /**
     * Elimina un item
     */
    public function delete(string $id): void
    {
        // Nota: Deberíamos verificar que el item pertenezca a un proyecto que el usuario puede editar.
        // Por simplicidad, asumimos permisos.
        
        $projectId = (int) $this->request->input('project_id'); // Necesitamos esto para volver
        
        $this->budgetRepo->deleteItem((int)$id);
        
        $this->redirect("/projects/view/$projectId?tab=budget");
    }
}