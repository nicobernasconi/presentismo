<?php
/**
 * Vista: Formulario de Departamento (crear/editar)
 * Variables:
 * - $department: array|null
 */

if (!is_array($department)) $department = null;

$editing = !empty($department);
$action = $editing ? $baseUrl . 'departamentos/' . (int)$department['id'] : $baseUrl . 'departamentos';
$title = $editing ? 'Editar Departamento' : 'Nuevo Departamento';
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900"><?= $title ?></h2>
        <a href="<?= $baseUrl ?>departamentos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Volver</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="<?= $action ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
            <input type="text" name="name" value="<?= htmlspecialchars($department['name'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
            <input type="text" name="code" value="<?= htmlspecialchars($department['code'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
            <textarea name="description" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" rows="3"><?= htmlspecialchars($department['description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Activo</label>
            <?php $isActive = isset($department['is_active']) ? (bool)$department['is_active'] : true; ?>
            <select name="is_active" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="1" <?= $isActive ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= !$isActive ? 'selected' : '' ?>>No</option>
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
                <?= $editing ? 'Guardar Cambios' : 'Crear Departamento' ?>
            </button>
        </div>
    </form>
</div>
