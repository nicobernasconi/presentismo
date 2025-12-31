<?php
/**
 * Mi Perfil
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Mi Perfil</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Información Personal -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Personal</h3>
        
        <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/perfil" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($profileUser['name'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($profileUser['email'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profileUser['phone'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="employee_code" class="block text-sm font-medium text-gray-700 mb-1">Código de Empleado</label>
                <input type="text" id="employee_code" name="employee_code" value="<?= htmlspecialchars($profileUser['employee_code'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cambiar Contraseña</h3>
        
        <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/perfil/password" class="space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                <input type="password" id="current_password" name="current_password" required class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                <input type="password" id="new_password" name="new_password" required class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full border-gray-300 rounded-lg">
            </div>

            <div>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg w-full">Actualizar</button>
            </div>
        </form>
    </div>
</div>
