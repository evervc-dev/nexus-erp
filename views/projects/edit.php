<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Editar Proyecto</h2>
            <a href="/projects/view/<?= $project->id ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Cancelar
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="/projects/edit/<?= $project->id ?>" method="POST">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="name" class="form-label fw-bold small">Nombre del Proyecto</label>
                            <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($project->name) ?>">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="location" class="form-label fw-bold small">Ubicación</label>
                            <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($project->location) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="budget" class="form-label fw-bold small">Presupuesto ($)</label>
                            <input type="number" step="0.01" class="form-control" name="budget" required value="<?= $project->budget ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-bold small">Inicio</label>
                            <input type="date" class="form-control" name="start_date" value="<?= $project->start_date ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-bold small">Fin (Estimado)</label>
                            <input type="date" class="form-control" name="end_date" value="<?= $project->end_date ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <?php if($_SESSION['role_name'] === 'SuperAdmin'): ?>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Eliminar Proyecto
                            </button>
                        <?php else: ?>
                            <div></div> <?php endif; ?>

                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i> Actualizar Cambios
                        </button>
                    </div>

                </form>

                <?php if($_SESSION['role_name'] === 'SuperAdmin'): ?>
                    <form id="delete-form" action="/projects/delete/<?= $project->id ?>" method="POST" style="display: none;"></form>
                    <script>
                        function confirmDelete() {
                            if(confirm('¿ESTÁS SEGURO?\n\nEsta acción eliminará el proyecto, todas sus tareas, presupuesto y asignaciones.\n\nNo se puede deshacer.')) {
                                document.getElementById('delete-form').submit();
                            }
                        }
                    </script>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>