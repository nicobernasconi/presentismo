<?php
/**
 * Formulario de Plan
 */
$isEdit = !empty($plan);
?>

<div class="mb-6">
    <a href="<?= $baseUrl ?>/admin/planes" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Planes
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6"><?= $isEdit ? 'Editar Plan' : 'Nuevo Plan' ?></h2>

    <form method="POST" action="<?= $isEdit ? $baseUrl . '/admin/planes/' . $plan['id'] : $baseUrl . '/admin/planes' ?>" class="space-y-6">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $plan['id'] ?>">
        <?php endif; ?>

        <!-- Nombre del Plan -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Plan *</label>
            <input type="text" id="name" name="name" required 
                   value="<?= htmlspecialchars($plan['name'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Ej: Starter, Professional, Enterprise">
        </div>

        <!-- Descripción -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Descripción del plan"><?= htmlspecialchars($plan['description'] ?? '') ?></textarea>
        </div>

        <!-- Máximo de Empleados -->
        <div>
            <label for="max_employees" class="block text-sm font-medium text-gray-700 mb-2">Máximo de Empleados *</label>
            <input type="number" id="max_employees" name="max_employees" required min="1"
                   value="<?= htmlspecialchars($plan['max_employees'] ?? 10) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="10">
        </div>

        <!-- Precios -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="monthly_price" class="block text-sm font-medium text-gray-700 mb-2">Precio Mensual ($) *</label>
                <input type="number" id="monthly_price" name="monthly_price" required step="0.01" min="0"
                       value="<?= htmlspecialchars($plan['monthly_price'] ?? 0) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="9.99">
            </div>

            <div>
                <label for="annual_price" class="block text-sm font-medium text-gray-700 mb-2">Precio Anual ($) *</label>
                <input type="number" id="annual_price" name="annual_price" required step="0.01" min="0"
                       value="<?= htmlspecialchars($plan['annual_price'] ?? 0) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="99.90">
            </div>
        </div>

        <?php if ($isEdit): ?>
        <!-- Estado -->
        <div>
            <label for="is_active" class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" 
                       <?= ($plan['is_active'] ?? 0) ? 'checked' : '' ?>
                       class="w-4 h-4 rounded">
                <span class="text-sm font-medium text-gray-700">Plan Activo</span>
            </label>
        </div>
        <?php endif; ?>

        <!-- Botones -->
        <div class="flex gap-3 pt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-save mr-2"></i><?= $isEdit ? 'Actualizar' : 'Crear' ?> Plan
            </button>
            <a href="<?= $baseUrl ?>/admin/planes" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-2 rounded-lg font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>
