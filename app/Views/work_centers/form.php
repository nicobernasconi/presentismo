<?php
/**
 * Centros de Trabajo - Formulario
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($title ?? '') ?></h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 max-w-2xl">
    <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/centros<?= isset($workCenter) && $workCenter['id'] ? '/' . $workCenter['id'] : '' ?>" class="p-6 space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($workCenter['name'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
            <input type="text" id="code" name="code" value="<?= htmlspecialchars($workCenter['code'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($workCenter['address'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
            <input type="text" id="city" name="city" value="<?= htmlspecialchars($workCenter['city'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
            <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($workCenter['postal_code'] ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="is_active" class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" <?= (isset($workCenter) && $workCenter['is_active']) || !isset($workCenter) ? 'checked' : '' ?> class="w-4 h-4">
                <span class="ml-2 text-sm text-gray-700">Activo</span>
            </label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">Guardar</button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/centros" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Cancelar</a>
        </div>
    </form>
</div>
