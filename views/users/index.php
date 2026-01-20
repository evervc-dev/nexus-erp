<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Usuarios del Sistema</h2>
        <p class="text-muted mb-0">Administra Ingenieros, Maestros y Clientes.</p>
    </div>
    <a href="/users/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Usuario</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-4 fw-medium">
                            <?= htmlspecialchars($u->name . ' ' . $u->last_name) ?>
                        </td>
                        <td>
                            <?php 
                                $badgeClass = match($u->role_name) {
                                    'SuperAdmin' => 'bg-dark',
                                    'Ingeniero' => 'bg-primary',
                                    'Maestro de Obra' => 'bg-warning text-dark',
                                    'Cliente' => 'bg-info text-dark',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded-pill"><?= htmlspecialchars($u->role_name) ?></span>
                        </td>
                        <td><?= htmlspecialchars($u->email) ?></td>
                        <td><?= htmlspecialchars($u->phone ?? '-') ?></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Activo</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>