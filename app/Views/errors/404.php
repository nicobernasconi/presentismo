<!DOCTYPE html>
<html lang="es">
<head>
        <?php
        // URLs para assets y rutas
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        $assetsUrl = $scriptDir;
        
        if (!isset($baseUrl)) {
            if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
                $baseUrl = $scriptDir . '/index.php?route=';
            } else {
                $baseUrl = '/presentismo/public';
            }
        }
        ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl font-bold text-gray-300 mb-4">404</div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Página no encontrada</h1>
        <p class="text-gray-600 mb-6">Lo sentimos, la página que buscas no existe.</p>
        <a href="<?= $baseUrl ?>dashboard" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-home mr-2"></i>
            Volver al inicio
        </a>
    </div>
</body>
</html>
