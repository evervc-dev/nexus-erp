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
                    
                    <?php if (in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero'])): ?>
                        <a href="/projects/edit/<?= $project->id ?>" class="btn btn-light border btn-sm rounded-circle text-muted" title="Configuración">
                            <i class="bi bi-gear-fill"></i>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-<?= $project->getStatusColor() ?> btn-sm rounded-pill px-3 dropdown-toggle fw-bold text-uppercase" type="button" data-bs-toggle="dropdown">
                                <?= ucfirst($project->status) ?>
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li>
                                    <h6 class="dropdown-header">Cambiar Estado</h6>
                                </li>
                                <li>
                                    <form action="/projects/update-status/<?= $project->id ?>" method="POST">
                                        <input type="hidden" name="status" value="borrador">
                                        <button class="dropdown-item" type="submit"><i class="bi bi-circle text-secondary me-2"></i>Borrador</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="/projects/update-status/<?= $project->id ?>" method="POST">
                                        <input type="hidden" name="status" value="activo">
                                        <button class="dropdown-item" type="submit"><i class="bi bi-play-circle text-success me-2"></i>Activo</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="/projects/update-status/<?= $project->id ?>" method="POST">
                                        <input type="hidden" name="status" value="detenido">
                                        <button class="dropdown-item" type="submit"><i class="bi bi-pause-circle text-danger me-2"></i>Detenido</button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="/projects/update-status/<?= $project->id ?>" method="POST">
                                        <input type="hidden" name="status" value="finalizado">
                                        <button class="dropdown-item fw-bold text-primary" type="submit"><i class="bi bi-check-circle-fill me-2"></i>Finalizado</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <span class="badge rounded-pill bg-<?= $project->getStatusColor() ?> px-3">
                            <?= ucfirst($project->status) ?>
                        </span>
                    <?php endif; ?>
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
                
                <div class="d-flex justify-content-between small text-muted mt-2">
                    <span>Progreso General</span>
                    <span class="fw-bold text-dark"><?= $kpi['progress'] ?>%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $kpi['progress'] ?>%;" 
                         aria-valuenow="<?= $kpi['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
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
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-calendar-event fs-4"></i>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold">Tiempo Restante</h6>
                        <h3 class="fw-bold text-dark"><?= $kpi['days_label'] ?></h3>
                        <small class="text-muted">
                            Fin: <?= $project->end_date ? date('d/m/Y', strtotime($project->end_date)) : 'N/A' ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                         <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold">Gastos Planificados</h6>
                        <h3 class="fw-bold text-dark">$<?= number_format($totalAllocated, 2) ?></h3>
                        <small class="text-success fw-bold">
                            <?= number_format(($totalAllocated / ($project->budget > 0 ? $project->budget : 1)) * 100, 2) ?>% del Presupuesto
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                         <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-list-check fs-4"></i>
                        </div>
                        <h6 class="text-muted text-uppercase small fw-bold">Tareas Completadas</h6>
                        <h3 class="fw-bold text-dark">
                            <?= $kpi['completed_tasks'] ?> <span class="text-muted fs-6 fw-normal">/ <?= $kpi['total_tasks'] ?></span>
                        </h3>
                        <small class="text-warning fw-bold"><?= $kpi['progress'] ?>% Avance Físico</small>
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
        
        <?php 
        // Lógica de permisos para esta vista
        $role = $_SESSION['role_name'] ?? '';
        $canCreateTask = in_array($role, ['SuperAdmin', 'Ingeniero']);
        $canMoveTask = in_array($role, ['SuperAdmin', 'Ingeniero', 'MaestroObra']);
        ?>

        <?php if ($canCreateTask): ?>
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
        <?php endif; ?>

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
                                    
                                    <?php if ($canMoveTask): ?>
                                        <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                            <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="btn btn-sm btn-outline-primary py-0 lh-1" title="Iniciar Tarea">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
                                    
                                    <?php if ($canMoveTask): ?>
                                        <div class="btn-group">
                                            <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                                <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary py-0 me-1" title="Devolver a Pendiente">
                                                    <i class="bi bi-arrow-left"></i>
                                                </button>
                                            </form>
                                            <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                                <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-outline-success py-0" title="Marcar Finalizado">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
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
                                    
                                    <?php if ($canMoveTask): ?>
                                        <form action="/tasks/update-status/<?= $task->id ?>" method="POST">
                                            <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0" title="Reabrir Tarea">
                                                <i class="bi bi-arrow-left"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="tab-pane fade" id="reports" role="tabpanel">
        
        <?php if (in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero', 'MaestroObra'])): ?>
            <div class="card mb-4 shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Nuevo Reporte Diario</h6>
                    
                    <form action="/reports/create" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="2" placeholder="Describe el avance del día, materiales recibidos, etc..." required></textarea>
                        </div>
                        
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="input-group w-auto">
                                <input type="file" class="form-control form-control-sm" name="images[]" multiple accept="image/*">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="incidents_flag" id="incidentsFlag">
                                <label class="form-check-label text-danger fw-bold small" for="incidentsFlag">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Reportar Incidente/Retraso
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="bi bi-send-fill me-2"></i>Publicar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="logs-container">
            <?php if (empty($logs)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-album fs-1 d-block mb-3 opacity-50"></i>
                    <p>No hay reportes registrados en la bitácora aún.</p>
                </div>
            <?php else: ?>
                <?php foreach ($logs as $report): ?>
                    <div class="card mb-3 shadow-sm <?= $report->incidents_flag ? 'border-danger border-start border-4 bg-danger bg-opacity-10' : 'border-0' ?>">
                        <div class="card-body">
                            
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                
                                <div>
                                    <span class="fw-bold text-dark me-2"><?= htmlspecialchars($report->author_name) ?></span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size: 0.7rem;">
                                        <?= htmlspecialchars($report->author_role) ?>
                                    </span>
                                    <?php if($report->incidents_flag): ?>
                                        <span class="badge bg-danger ms-2">INCIDENTE</span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-end d-flex align-items-center gap-3">
                                    <div>
                                        <small class="text-dark fw-bold d-block">
                                            <?= date('d/m/Y', strtotime($report->report_date)) ?>
                                        </small>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <?= date('h:i A', strtotime($report->created_at)) ?>
                                        </small>
                                    </div>

                                    <?php 
                                        $isAuthor = (isset($_SESSION['user_id']) && $report->author_id === $_SESSION['user_id']);
                                        $isAdminOrEng = (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero']));
                                        
                                        if ($isAdminOrEng || $isAuthor): 
                                    ?>
                                        <form action="/reports/delete/<?= $report->id ?>" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este reporte?\nSe borrarán las fotos asociadas permanentemente.');">
                                            <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Eliminar reporte">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <p class="card-text mb-3 <?= $report->incidents_flag ? 'text-dark' : 'text-secondary' ?>">
                                <?= nl2br(htmlspecialchars($report->content)) ?>
                            </p>

                            <?php if (!empty($report->images)): ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($report->images as $img): ?>
                                        <div class="rounded-3 overflow-hidden border bg-white" style="width: 100px; height: 100px;">
                                            <a href="<?= $img->image_url ?>" target="_blank">
                                                <img src="<?= $img->image_url ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Evidencia">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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