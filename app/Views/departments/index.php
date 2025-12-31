<?php
/**
 * Vista: Lista de Departamentos
 * Variables:
 * - $departments: array de departamentos con employee_count
 */

if (!is_array($departments)) $departments = [];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Departamentos</h2>
            <p class="text-gray-600 mt-1">Organización y responsables</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos/crear" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Departamento
        </a>
    </div>
</div>

<?php if (empty($departments)): ?>
<div class="text-center py-12 bg-white rounded-lg border border-gray-200">
    <i class="fas fa-sitemap text-5xl text-gray-300 mb-4"></i>
    <p class="text-gray-600">No hay departamentos</p>
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos/crear" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg"><i class="fas fa-plus mr-2"></i>Crear Departamento</a>
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleados</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($departments as $d): ?>
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= htmlspecialchars($d['name'] ?? '') ?></td>
                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($d['code'] ?? '') ?></td>
                <td class="px-6 py-4 text-sm text-gray-700"><?= (int)($d['employee_count'] ?? 0) ?></td>
                <td class="px-6 py-4 text-sm">
                    <?php $active = (bool)($d['is_active'] ?? true); ?>
                    <span class="px-2 py-1 rounded-full text-<?= $active ? 'green' : 'red' ?>-800 bg-<?= $active ? 'green' : 'red' ?>-100 text-xs font-semibold">
                        <?= $active ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos/<?= (int)($d['id'] ?? 0) ?>" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fas fa-eye"></i></a>
                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos/<?= (int)($d['id'] ?? 0) ?>/editar" class="text-gray-600 hover:text-gray-900"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
