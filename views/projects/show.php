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
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calculator fs-1 text-muted mb-3"></i>
                <h5>Módulo de Materiales y Costos</h5>
                <p class="text-muted">Aquí gestionaremos la lista de materiales y control de gastos.</p>
                <button class="btn btn-outline-dark btn-sm">Configurar Presupuesto (Próximamente)</button>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tasks" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-kanban fs-1 text-muted mb-3"></i>
                <h5>Gestión de Tareas</h5>
                <p class="text-muted">Asignación de actividades a Maestros y Empleados.</p>
                <button class="btn btn-outline-dark btn-sm">Crear Tarea (Próximamente)</button>
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