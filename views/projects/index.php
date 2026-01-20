<?php
// Verifica permisos de creación en la vista
$canCreate = in_array($_SESSION['role_name'] ?? '', ['SuperAdmin', 'Ingeniero']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Proyectos</h2>
        <p class="text-muted mb-0">Gestión y seguimiento de obras activas.</p>
    </div>
    
    <?php if ($canCreate): ?>
        <a href="/projects/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Proyecto
        </a>
    <?php endif; ?>
</div>

<div class="row g-4">
    
    <?php if (empty($projects)): ?>
        <div class="col-12">
            <div class="text-center py-5 bg-light rounded-3 border border-dashed">
                <i class="bi bi-cone-striped fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No hay proyectos registrados.</h5>
                <?php if ($canCreate): ?>
                    <p class="mb-3">Comienza creando la primera obra del sistema.</p>
                    <a href="/projects/create" class="btn btn-outline-primary btn-sm">Crear Proyecto</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        
        <?php foreach ($projects as $project): ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 project-card hover-shadow transition-all">
                    
                    <div class="card-img-top bg-secondary bg-opacity-10 d-flex justify-content-center align-items-center position-relative" style="height: 160px;">
                        <i class="bi bi-building text-secondary fs-1 opacity-50"></i>
                        
                        <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-<?= $project->getStatusColor() ?> shadow-sm">
                            <?= ucfirst($project->status) ?>
                        </span>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-truncate" title="<?= htmlspecialchars($project->name) ?>">
                            <?= htmlspecialchars($project->name) ?>
                        </h5>
                        
                        <p class="card-text small text-muted mb-3">
                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> 
                            <?= htmlspecialchars($project->location ?? 'Sin ubicación') ?>
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between small mb-2 border-bottom pb-2">
                                <span class="text-muted">Presupuesto:</span>
                                <span class="fw-bold text-dark">$<?= number_format($project->budget, 2) ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between small mb-3">
                                <span class="text-muted">Manager:</span>
                                <span class="text-truncate ms-2" style="max-width: 120px;" title="<?= htmlspecialchars($project->manager_name) ?>">
                                    <?= htmlspecialchars($project->manager_name) ?>
                                </span>
                            </div>

                            <a href="/projects/view/<?= $project->id ?>" class="btn btn-outline-primary w-100 btn-sm">
                                Ver Detalles <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-calendar-event me-1"></i> 
                            Inicio: <?= $project->start_date ? date('d/m/Y', strtotime($project->start_date)) : 'Pendiente' ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>