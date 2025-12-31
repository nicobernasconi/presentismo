<?php
/**
 * Vista: QR inválido
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Inválido</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        <div class="text-red-500 text-6xl mb-6">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Código QR Inválido</h1>
        <p class="text-gray-600 mb-6"><?= htmlspecialchars($message ?? 'El código QR no es válido o ha expirado.') ?></p>
        <p class="text-gray-500 text-sm">
            Por favor, contacta con el administrador de tu empresa para obtener un código QR válido.
        </p>
    </div>
</body>
</html>
