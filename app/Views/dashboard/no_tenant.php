<?php
$baseUrl = isset($baseUrl) ? $baseUrl : '/presentismo/public';
?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 border-t-4 border-primary-600">
    <div class="text-center">
        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-primary-600 text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Bienvenido</h1>
        <p class="text-gray-600 mb-6">Tu cuenta ha sido creada correctamente, pero aún no tienes asignada una empresa.</p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">¿Qué ocurre ahora?</h3>
        <ul class="text-sm text-blue-800 space-y-2">
            <li><i class="fas fa-check-circle text-blue-600 mr-2"></i>Tu cuenta está activa</li>
            <li><i class="fas fa-hourglass-half text-blue-600 mr-2"></i>Espera a que el administrador te asigne a una empresa</li>
            <li><i class="fas fa-sign-in-alt text-blue-600 mr-2"></i>Una vez asignado, podrás acceder al sistema</li>
        </ul>
    </div>

    <div class="space-y-3">
        <button onclick="window.Dialog.alert('Contacta con tu administrador para que te asigne a una empresa.', 'Próximos pasos')" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
            <i class="fas fa-envelope mr-2"></i>Contactar Administrador
        </button>
        <a href="<?= $baseUrl ?>/logout" class="block text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition">
            <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
        </a>
    </div>

    <p class="text-center text-gray-500 text-xs mt-6">
        Si crees que esto es un error, contacta con soporte
    </p>
</div>
