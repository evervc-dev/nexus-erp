<div class="mb-4">
    <a href="/projects" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Volver a Proyectos
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h2 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($project->name) ?></h2>
                    <span class="badge rounded-pill bg-<?= $project->getStatusColor() ?> px-3">
                        <?= ucfirst($project->status) ?>
                    </span>
                </div>
                <p class="text-muted mb-0">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> 
                    <?= htmlspecialchars($project->location ?? 'Ubicación no definida') ?>
                    <span class="mx-2">|</span>
                    <i class="bi bi-person-badge-fill text-primary me-1"></i>
                    Manager: <strong><?= htmlspecialchars($project->manager_name) ?></strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="text-muted small text-uppercase fw-bold">Presupuesto Total</div>
                <div class="fs-3 fw-bold text-dark">$<?= number_format($project->budget, 2) ?></div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">0% Ejecutado (Pendiente)</small>
            </div>
        </div>
    </div>
    
    <div class="card-footer bg-white border-top-0 pb-0 pt-0">
        <ul class="nav nav-tabs nav-fill card-header-tabs" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-3 fw-bold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="bi bi-speedometer2 me-2"></i>Resumen
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="budget-tab" data-bs-toggle="tab" data-bs-target="#budget" type="button" role="tab">
                    <i class="bi bi-cash-coin me-2"></i>Presupuesto
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                    <i class="bi bi-list-check me-2"></i>Tareas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 fw-bold" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                    <i class="bi bi-journal-richtext me-2"></i>Bitácora
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="projectTabsContent">
    
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Días Restantes</h6>
                        <h3 class="fw-bold text-primary">--</h3>
                        <small>Fecha fin: <?= $project->end_date ? date('d/m/Y', strtotime($project->end_date)) : 'N/A' ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Gastos vs Presupuesto</h6>
                        <h3 class="fw-bold text-success">$0.00</h3>
                        <small>Disponible: $<?= number_format($project->budget, 2) ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Tareas Completadas</h6>
                        <h3 class="fw-bold text-warning">0 / 0</h3>
                        <small>0% Avance</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="budget" role="tabpanel">
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border border-success-subtle">
                    <small class="text-uppercase text-muted fw-bold">Presupuesto Global</small>
                    <div class="fs-4 fw-bold text-dark">$<?= number_format($project->budget, 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border border-warning-subtle">
                    <small class="text-uppercase text-muted fw-bold">Planificado (Materiales)</small>
                    <div class="fs-4 fw-bold text-warning">$<?= number_format($totalAllocated, 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border border-primary-subtle">
                    <small class="text-uppercase text-muted fw-bold">Disponible</small>
                    <div class="fs-4 fw-bold <?= $remainingBudget < 0 ? 'text-danger' : 'text-success' ?>">
                        $<?= number_format($remainingBudget, 2) ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero'])): ?>
            <div class="card mb-4 shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-cart-plus me-2"></i>Agregar Material al Presupuesto</h6>
                    <form action="/budget/add" method="POST" class="row g-2 align-items-end">
                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                        
                        <div class="col-md-5">
                            <label class="small text-muted mb-1">Material</label>
                            <select class="form-select" name="material_id" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($allMaterials as $mat): ?>
                                    <option value="<?= $mat->id ?>">
                                        <?= htmlspecialchars($mat->name) ?> ($<?= $mat->unit_price ?> / <?= $mat->unit ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Cantidad</label>
                            <input type="number" step="0.01" class="form-control" name="quantity" placeholder="0.00" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Notas (Opcional)</label>
                            <input type="text" class="form-control" name="notes" placeholder="Ej: Para cimientos">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Material</th>
                            <th>Unidad</th>
                            <th>Precio (Hist.)</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Notas</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($budgetItems)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No hay materiales asignados a este presupuesto.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($budgetItems as $item): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($item->material_name) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($item->material_unit) ?></span></td>
                                    <td>$<?= number_format($item->historical_cost, 2) ?></td>
                                    <td><?= $item->quantity ?></td>
                                    <td class="fw-bold text-success">$<?= number_format($item->getSubtotal(), 2) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($item->notes ?? '-') ?></td>
                                    <td class="text-end pe-4">
                                        <?php if (in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero'])): ?>
                                            <form action="/budget/delete/<?= $item->id ?>" method="POST" onsubmit="return confirm('¿Eliminar este item?');">
                                                <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tasks" role="tabpanel">
        
        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card-body">
                <form action="/tasks/create" method="POST" class="row g-2 align-items-end">
                    <input type="hidden" name="project_id" value="<?= $project->id ?>">
                    
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Título de la Tarea <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control fw-bold" placeholder="Ej: Revisión de Planos" required>
                    </div>

                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Descripción (Opcional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Detalles adicionales...">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Maestro Encargado</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">-- Sin asignar --</option>
                            <?php foreach ($assignableUsers as $usr): ?>
                                <option value="<?= $usr->id ?>">
                                    <?= htmlspecialchars($usr->name . ' ' . $usr->last_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Fecha Límite</label>
                        <div class="input-group">
                            <input type="date" name="due_date" class="form-control">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3">
            
            <div class="col-md-4">
                <div class="p-3 bg-secondary bg-opacity-10 rounded h-100">
                    <h6 class="text-uppercase text-secondary fw-bold mb-3 border-bottom pb-2">Por Hacer</h6>
                    <?php foreach ($tasksGrouped['pending'] as $task): ?>
                        <div class="card mb-2 border-0 shadow-sm border-start border-4 border-secondary">
                            <div class="card-body p-3">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($task->title) ?></h6>
                                <?php if($task->description): ?>
                                    <p class="small text-muted mb-2 lh-sm"><?= htmlspecialchars($task->description) ?></p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <small class="text-secondary fw-bold" style="font-size: 0.7rem;">
                                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($task->assigned_user_name ?? 'Nadie') ?>
                                    </small>
                                    
                                    <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-sm btn-outline-primary py-0 lh-1">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-primary bg-opacity-10 rounded h-100">
                    <h6 class="text-uppercase text-primary fw-bold mb-3 border-bottom pb-2">En Proceso</h6>
                    <?php foreach ($tasksGrouped['in_progress'] as $task): ?>
                        <div class="card mb-2 border-0 shadow-sm border-start border-4 border-primary">
                            <div class="card-body p-3">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($task->title) ?></h6>
                                <?php if($task->description): ?>
                                    <p class="small text-muted mb-2 lh-sm"><?= htmlspecialchars($task->description) ?></p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($task->assigned_user_name ?? 'Nadie') ?>
                                    </small>
                                    
                                    <div class="btn-group">
                                        <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                            <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0 me-1"><i class="bi bi-arrow-left"></i></button>
                                        </form>
                                        <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                            <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-success bg-opacity-10 rounded h-100">
                    <h6 class="text-uppercase text-success fw-bold mb-3 border-bottom pb-2">Finalizado</h6>
                    <?php foreach ($tasksGrouped['completed'] as $task): ?>
                        <div class="card mb-2 border-0 shadow-sm border-start border-4 border-success bg-white opacity-75">
                            <div class="card-body p-3">
                                <h6 class="mb-1 fw-bold text-decoration-line-through text-muted"><?= htmlspecialchars($task->title) ?></h6>
                                
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-calendar-check"></i> 
                                        <?= $task->completed_at ? date('d/m H:i', strtotime($task->completed_at)) : '-' ?>
                                    </small>
                                    
                                    <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary py-0"><i class="bi bi-arrow-left"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="tab-pane fade" id="reports" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-camera fs-1 text-muted mb-3"></i>
                <h5>Bitácora de Obra</h5>
                <p class="text-muted">Reportes diarios y evidencia fotográfica.</p>
                <button class="btn btn-outline-dark btn-sm">Nuevo Reporte (Próximamente)</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Verificar si hay un parametro ?tab=...
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        if (activeTab) {
            // Buscar el botón que abre esa pestaña y hacerle clic
            const triggerEl = document.querySelector(`#projectTabs button[data-bs-target="#${activeTab}"]`);
            if (triggerEl) {
                const tabInstance = new bootstrap.Tab(triggerEl);
                tabInstance.show();
            }
        }
    });
</script>