<div class="row justify-content-center mt-4">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-dark text-white text-center py-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill text-warning me-2"></i>Únete a Nexus
                </h3>
                <small class="text-white-50">Crea tu cuenta para gestionar tus proyectos</small>
            </div>
            
            <div class="card-body p-4">
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form action="/register" method="POST">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="first_name" required 
                                   value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" autofocus>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="last_name" required
                                   value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" required
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" name="confirm_password" required minlength="6">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark">
                            Crear Cuenta
                        </button>
                        <a href="/login" class="btn btn-outline-secondary">
                            ¿Ya tienes cuenta? Inicia Sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>