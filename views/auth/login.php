<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-dark text-white text-center py-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-buildings-fill text-warning me-2"></i>Nexus ERP
                </h3>
                <small class="text-white-50">Acceso al Sistema</small>
            </div>
            
            <div class="card-body p-4">
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form action="/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($old_email ?? '') ?>" 
                                   placeholder="admin@nexus.com" 
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Ingresar <i class="bi bi-box-arrow-in-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer text-center py-3 bg-light">
                <div class="mb-2">
                    ¿Aún no tienes cuenta? 
                    <a href="/register" class="text-decoration-none fw-bold">Regístrate aquí</a>
                </div>
                <small class="text-muted" style="font-size: 0.8rem;">
                    ¿Olvidaste tu contraseña? Contacta al Soporte IT.
                </small>
            </div>
        </div>
    </div>
</div>