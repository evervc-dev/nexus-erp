<?php
/**
 * Vista de emergencia sin dependencias.
 * Recibe variables: $title, $message
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Sistema</title>
    <style>
        body { font-family: -apple-system, system-ui, sans-serif; background-color: #f8f9fa; color: #333; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .error-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; text-align: center; border-left: 5px solid #dc3545; }
        h1 { color: #dc3545; margin-top: 0; font-size: 24px; }
        p { color: #6c757d; line-height: 1.6; }
        .tech-info { background: #f1f3f5; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; margin-top: 20px; color: #495057; text-align: left; overflow-x: auto; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #343a40; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn:hover { background-color: #23272b; }
    </style>
</head>
<body>
    <div class="error-card">
        <h1><?= htmlspecialchars($title ?? 'Error') ?></h1>
        <p>Ocurrió un problema crítico que impide cargar la aplicación.</p>
        <?php if (!empty($message)): ?>
            <div class="tech-info"><strong>Detalle:</strong> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <a href="/" class="btn">Intentar Recargar</a>
    </div>
</body>
</html>