<?php
/**
 * Configuración - Empresa
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Configuración de Empresa</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-2xl">
    <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/configuracion/empresa" class="p-6 space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Empresa</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($tenant['name'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($tenant['email'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($tenant['phone'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($tenant['address'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
            <input type="text" id="city" name="city" value="<?= htmlspecialchars($tenant['city'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
            <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($tenant['postal_code'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">País</label>
            <input type="text" id="country" name="country" value="<?= htmlspecialchars($tenant['country'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">Guardar</button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/configuracion" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Cancelar</a>
        </div>
    </form>
</div>
