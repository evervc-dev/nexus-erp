<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Registrar Empleado</h2>
            <a href="/employees" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Volver
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="/employees/create" method="POST">
                    
                    <h5 class="mb-3 text-secondary border-bottom pb-2">Información Personal</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="Ej: Juan Antonio">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-bold small">Apellidos</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ej: Pérez López">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="dui" class="form-label fw-bold small">DUI (Documento Único)</label>
                            <input type="text" class="form-control font-monospace" id="dui" name="dui" placeholder="00000000-0">
                            <div class="form-text">Sin guiones o con guiones, como prefieras.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold small">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="7000-0000">
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 text-secondary border-bottom pb-2">Información Laboral</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="position" class="form-label fw-bold small">Cargo / Puesto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="position" name="position" required list="common-positions" placeholder="Selecciona o escribe...">
                            <datalist id="common-positions">
                                <option value="Maestro de Obra">
                                <option value="Albañil">
                                <option value="Auxiliar">
                                <option value="Carpintero">
                                <option value="Electricista">
                                <option value="Fontanero">
                            </datalist>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="daily_salary" class="form-label fw-bold small">Salario Diario ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="daily_salary" name="daily_salary" required placeholder="0.00">
                            </div>
                            <div class="form-text">Base para cálculo de planillas.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3 border-top">
                        <a href="/employees" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i> Guardar Empleado
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>