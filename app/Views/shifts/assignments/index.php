<?php
$baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
$pageTitle = __('shifts.shift_assignments') ?? 'Asignaciones de Turnos';
?>

<?php include __DIR__ . '/../../layouts/app.php'; ?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
            <p class="text-gray-600 mt-1"><?= __('shifts.manage_employee_shifts') ?? 'Gestionar turnos de empleados' ?></p>
        </div>
        <a href="<?= $baseUrl ?>/turnos/asignaciones/crear" 
           class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            <?= __('shifts.assign_shift') ?? 'Asignar Turno' ?>
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?= __('common.search') ?></label>
                <input type="text" id="searchInput" placeholder="<?= __('employees.search_employee') ?? 'Buscar empleado' ?>" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?= __('common.status') ?></label>
                <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value=""><?= __('common.all') ?></option>
                    <option value="active"><?= __('common.active') ?></option>
                    <option value="inactive"><?= __('common.inactive') ?></option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><?= __('shifts.shift') ?></label>
                <select id="shiftFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value=""><?= __('common.all') ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de asignaciones -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('employees.employee') ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('shifts.shift') ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('common.schedule') ?? 'Horario' ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('shifts.assignment_period') ?? 'Período' ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('common.status') ?>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?= __('common.actions') ?>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($assignments)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-25"></i>
                            <p><?= __('shifts.no_assignments') ?? 'No hay asignaciones de turnos' ?></p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($assignment['employee_name']) ?></div>
                                        <?php if ($assignment['employee_code']): ?>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($assignment['employee_code']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: <?= $assignment['color'] ?>20; color: <?= $assignment['color'] ?>">
                                    <span class="w-2 h-2 rounded-full mr-1.5" style="background-color: <?= $assignment['color'] ?>"></span>
                                    <?= htmlspecialchars($assignment['shift_name']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= date('H:i', strtotime($assignment['start_time'])) ?> - <?= date('H:i', strtotime($assignment['end_time'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div><?= __('common.from') ?>: <?= date('d/m/Y', strtotime($assignment['start_date'])) ?></div>
                                <?php if ($assignment['end_date']): ?>
                                    <div><?= __('common.until') ?>: <?= date('d/m/Y', strtotime($assignment['end_date'])) ?></div>
                                <?php else: ?>
                                    <div class="text-gray-500"><?= __('shifts.indefinite') ?? 'Indefinido' ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($assignment['is_active']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        <?= __('common.active') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-minus-circle mr-1"></i>
                                        <?= __('common.inactive') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <?php if ($assignment['is_active']): ?>
                                        <form method="POST" action="<?= $baseUrl ?>/turnos/asignaciones/<?= $assignment['id'] ?>/desactivar" class="inline">
                                            <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="<?= __('common.deactivate') ?? 'Desactivar' ?>">
                                                <i class="fas fa-pause-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= $baseUrl ?>/turnos/asignaciones/<?= $assignment['id'] ?>/eliminar" 
                                          class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta asignación?')">
                                        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="<?= __('common.delete') ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Filtros en tiempo real
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('shiftFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const shiftFilter = document.getElementById('shiftFilter').value;
    
    const rows = document.querySelectorAll('tbody tr:not(:first-child)');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = text.includes(searchTerm);
        
        // Aquí puedes agregar más lógica de filtrado
        row.style.display = matchesSearch ? '' : 'none';
    });
}
</script>
