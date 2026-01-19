<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">Gestión de Personal</h2>
        <p class="text-muted mb-0">Administra la cuadrilla y recursos humanos.</p>
    </div>
    <a href="/employees/create" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Empleado
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold">Nombre Completo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Cargo</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">DUI / ID</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Teléfono</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Salario Diario</th>
                        <th class="py-3 text-secondary text-uppercase small fw-bold">Estado</th>
                        <th class="pe-4 py-3 text-end text-secondary text-uppercase small fw-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                No hay empleados registrados aún.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td class="ps-4 fw-medium">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial rounded-circle bg-light text-primary fw-bold me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                            <?= substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1) ?>
                                        </div>
                                        <div>
                                            <?= htmlspecialchars($emp->getFullName()) ?>
                                            <div class="small text-muted d-lg-none">
                                                <?= htmlspecialchars($emp->position) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($emp->position) ?></td>
                                <td class="font-monospace"><?= htmlspecialchars($emp->dui ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($emp->phone): ?>
                                        <a href="tel:<?= htmlspecialchars($emp->phone) ?>" class="text-decoration-none text-body">
                                            <?= htmlspecialchars($emp->phone) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-success">
                                    $<?= number_format($emp->daily_salary, 2) ?>
                                </td>
                                <td>
                                    <?php if ($emp->is_active): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>