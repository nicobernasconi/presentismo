<?php
/**
 * Formulario de Empresa
 */
$isEdit = !empty($company);
?>

<div class="mb-6">
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Empresas
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6"><?= $isEdit ? 'Editar Empresa' : 'Nueva Empresa' ?></h2>

    <form method="POST" action="<?= $isEdit ? (htmlspecialchars($baseUrl ?? '') . '/admin/empresas/' . $company['id']) : (htmlspecialchars($baseUrl ?? '') . '/admin/empresas') ?>" class="space-y-6">
        <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $company['id'] ?>">
        <?php endif; ?>

        <!-- Nombre -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa *</label>
            <input type="text" id="name" name="name" required 
                   value="<?= htmlspecialchars($company['name'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Nombre de la empresa">
        </div>

        <!-- RFC -->
        <div>
            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">RFC *</label>
            <input type="text" id="tax_id" name="tax_id" required 
                   value="<?= htmlspecialchars($company['tax_id'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="RFC">
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
            <input type="email" id="email" name="email" required 
                   value="<?= htmlspecialchars($company['email'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="email@empresa.com">
        </div>

        <!-- Teléfono -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
            <input type="tel" id="phone" name="phone" 
                   value="<?= htmlspecialchars($company['phone'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="+52 1234567890">
        </div>

        <!-- Dirección -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
            <textarea id="address" name="address" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Dirección completa"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
        </div>

        <!-- Ciudad -->
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
            <input type="text" id="city" name="city" 
                   value="<?= htmlspecialchars($company['city'] ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Ciudad">
        </div>

        <!-- Plan -->
        <div>
            <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">Plan *</label>
            <select id="plan_id" name="plan_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Selecciona un plan</option>
                <?php foreach ($plans as $plan): ?>
                <option value="<?= $plan['id'] ?>" <?= ($company['plan_id'] ?? null) == $plan['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($plan['name'] ?? '') ?> (<?= $plan['max_employees'] ?> empleados)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($isEdit): ?>
        <!-- Estado -->
        <div>
            <label for="is_active" class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" 
                       <?= ($company['is_active'] ?? 0) ? 'checked' : '' ?>
                       class="w-4 h-4 rounded">
                <span class="text-sm font-medium text-gray-700">Empresa Activa</span>
            </label>
        </div>
        <?php endif; ?>

        <!-- Botones -->
        <div class="flex gap-3 pt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-save mr-2"></i><?= $isEdit ? 'Actualizar' : 'Crear' ?> Empresa
            </button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-2 rounded-lg font-medium">
                Cancelar
            </a>
        </div>
    </form>
</div>
