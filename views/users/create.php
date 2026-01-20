<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Registrar Personal</h2>
            <a href="/users" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Volver
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger shadow-sm mb-4">
                <?php if($_GET['error'] == 'email_exists') echo 'El correo electrónico ya está registrado.'; ?>
                <?php if($_GET['error'] == 'missing_fields') echo 'Todos los campos marcados son obligatorios.'; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="/users/create" method="POST">
                    
                    <h5 class="mb-3 text-secondary border-bottom pb-2">Datos de la Cuenta</h5>
                    
                    <div class="mb-4">
                        <label for="role_id" class="form-label fw-bold small">Rol del Usuario <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg bg-light" id="role_id" name="role_id" required autofocus>
                            <option value="">Seleccione un rol...</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>">
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Esto definirá los permisos de acceso al sistema.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-bold small">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold small">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required placeholder="nombre@empresa.com">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold small">Teléfono</label>
                            <input type="text" class="form-control" name="phone" placeholder="7000-0000">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small">Contraseña Temporal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" name="password" required value="Nexus2026!" >
                            <div class="form-text">Contraseña por defecto sugerida.</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-person-plus-fill me-2"></i> Crear Usuario
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>