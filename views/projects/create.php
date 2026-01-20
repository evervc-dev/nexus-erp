<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Nuevo Proyecto</h2>
            <a href="/projects" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Cancelar
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 text-muted small text-uppercase fw-bold">Ficha Técnica de la Obra</h6>
            </div>
            <div class="card-body p-4">
                <form action="/projects/create" method="POST">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="name" class="form-label fw-bold small">Nombre del Proyecto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" required placeholder="Ej: Residencial Los Álamos - Fase 1" autofocus>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="location" class="form-label fw-bold small">Ubicación / Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Ciudad, Municipio o dirección exacta">
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="budget" class="form-label fw-bold small">Presupuesto Estimado ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-success">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget" required placeholder="0.00">
                            </div>
                            <div class="form-text">Monto total aprobado para la obra.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="client_id" class="form-label fw-bold small">Cliente Propietario <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Seleccione un cliente...</option>
                                <?php if (!empty($clients)): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client->id ?>">
                                            <?= htmlspecialchars($client->name . ' ' . $client->last_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($clients)): ?>
                                <div class="form-text text-danger">⚠ No hay clientes registrados.</div>
                            <?php else: ?>
                                <div class="form-text">El proyecto será visible para este usuario.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-bold small">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_date" class="form-label fw-bold small">Fecha de Finalización (Estimada)</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="mb-4">
                        <label class="form-label fw-bold small mb-3">Asignar Maestros de Obra</label>
                        
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <?php if (empty($masters)): ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="bi bi-exclamation-circle me-2"></i>
                                        No hay usuarios con el rol 'Maestro de Obra' registrados en el sistema.
                                    </div>
                                <?php else: ?>
                                    <div class="row g-3">
                                        <?php foreach ($masters as $master): ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check p-3 bg-white rounded border shadow-sm h-100 position-relative">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" 
                                                           name="masters[]" 
                                                           value="<?= $master->id ?>" 
                                                           id="master_<?= $master->id ?>">
                                                    <label class="form-check-label stretched-link" for="master_<?= $master->id ?>">
                                                        <span class="d-block fw-bold text-dark">
                                                            <?= htmlspecialchars($master->name . ' ' . $master->last_name) ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <i class="bi bi-telephone-fill me-1" style="font-size: 0.7rem;"></i>
                                                            <?= htmlspecialchars($master->phone ?? 'Sin teléfono') ?>
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Selecciona los maestros que trabajarán en este proyecto. Podrás asignarles tareas específicas más adelante.
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i> Crear Proyecto
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>