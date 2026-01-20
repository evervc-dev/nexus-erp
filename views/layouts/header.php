<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <i class="bi bi-buildings-fill text-warning"></i>
                <span class="fw-bold">Nexus ERP</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/">Dashboard</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/projects">Proyectos</a>
                        </li>
                        
                        <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero', 'Maestro de Obra'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/employees">Planilla (Obreros)</a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/users">Usuarios (Staff)</a>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-flex flex-column text-end lh-1">
                                    <span class="fw-bold text-white" style="font-size: 0.9rem;">
                                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                                    </span>
                                    <small class="text-white-50" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($_SESSION['role_name'] ?? 'Usuario') ?>
                                    </small>
                                </div>
                                <i class="bi bi-person-circle fs-4 text-secondary"></i>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                <li>
                                    <h6 class="dropdown-header">Cuenta</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/profile">
                                        <i class="bi bi-person-gear me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="nav-link" href="/login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-warning btn-sm text-dark fw-bold" href="/register">
                                <i class="bi bi-person-plus-fill me-1"></i> Crear Cuenta
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </nav>
    <div style="height: 80px;"></div>
</header>