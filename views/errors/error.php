<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center p-5">
                <div class="display-1 text-danger mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h1 class="card-title text-danger mb-3">Error <?= $code ?></h1>
                <p class="card-text lead text-muted mb-4"><?= $message ?></p>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="/" class="btn btn-dark">
                        <i class="bi bi-house-door"></i> Volver al Inicio
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Regresar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>