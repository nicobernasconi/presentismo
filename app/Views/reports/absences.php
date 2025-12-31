<?php
/**
 * Reportes - Ausencias
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Reporte de Ausencias</h2>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/ausencias" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>" class="w-full border-gray-300 rounded-lg">
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg w-full">Filtrar</button>
        </div>
    </form>
</div>

<!-- Tabla de Reportes -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($absences)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Inicio</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($absences as $absence): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($absence['name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($absence['type_name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y', strtotime($absence['start_date'] ?? '')) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y', strtotime($absence['end_date'] ?? '')) ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php 
                            $status = $absence['status'] ?? 'pending';
                            $badgeClass = $status === 'approved' ? 'bg-green-100 text-green-800' : ($status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800');
                            $statusLabel = $status === 'approved' ? 'Aprobada' : ($status === 'rejected' ? 'Rechazada' : 'Pendiente');
                            ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>"><?= $statusLabel ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-gray-600">No hay datos para mostrar</p>
        <?php endif; ?>
    </div>
</div>
