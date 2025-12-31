<?php
/**
 * Vista del Listado de Empleados
 * Variables disponibles:
 * - $employees: listado de empleados
 * - $departments: departamentos disponibles
 * - $filters: filtros aplicados
 */
?>

<?php
// Normalizar variables para evitar errores de tipos
if (!is_array($employees)) $employees = [];
if (!is_array($departments)) $departments = [];
if (!is_array($filters)) $filters = [];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Empleados</h2>
            <p class="text-gray-600 mt-1">Administra el personal de la empresa</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/crear" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Empleado
        </a>
    </div>
</div>

<?php 
// Mostrar aviso si el usuario no es supervisor
if (!isset($isSupervisor) || !$isSupervisor()) {
    echo '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-yellow-800"><i class="fas fa-info-circle mr-2"></i>Solo supervisores y administradores pueden crear empleados.</p>
    </div>';
}
?>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   placeholder="Nombre, email o DNI"
                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                   class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>

        <div>
            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
            <select id="department" 
                    name="department_id" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <?php foreach ($departments ?? [] as $id => $name): ?>
                    <option value="<?= htmlspecialchars($id) ?>" <?= ($filters['department_id'] ?? '') == $id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select id="status" 
                    name="status" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Todos</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-search mr-2"></i>
                Buscar
            </button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Lista de Empleados -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($employees)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Empleado
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departamento
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Último Fichaje
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($employees as $employee): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-primary-700 font-semibold text-sm">
                                                    <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= htmlspecialchars($employee['dni'] ?? 'Sin DNI') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($employee['email']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= htmlspecialchars($employee['department_name'] ?? 'Sin asignar') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($employee['role_name'] ?? 'Employee') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($employee['is_active']): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= isset($employee['last_clock_in']) && $employee['last_clock_in'] ? date('d/m/Y H:i', strtotime($employee['last_clock_in'])) : 'Nunca' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/<?= $employee['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/<?= $employee['id'] ?>/editar" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?= $employee['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-users text-6xl"></i>
                </div>
                <p class="text-gray-600 text-lg">No se encontraron empleados</p>
                <p class="text-gray-500 text-sm mt-2">Comienza agregando un nuevo empleado</p>
                <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/crear" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Primer Empleado
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(id) {
    if (window.Dialog && window.Dialog.confirm) {
        window.Dialog.confirm('¿Estás seguro de que deseas eliminar este empleado? Esta acción no se puede deshacer.')
            .then((ok) => {
                if (ok) {
                    const form = document.getElementById('delete-form');
                    form.action = '<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/' + id + '/eliminar';
                    form.submit();
                }
            });
    } else {
        if (confirm('¿Estás seguro de que deseas eliminar este empleado? Esta acción no se puede deshacer.')) {
            const form = document.getElementById('delete-form');
            form.action = '<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/' + id + '/eliminar';
            form.submit();
        }
    }
}
</script>
