<?php
/**
 * Vacaciones
 */
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Vacaciones</h2>
        <form method="GET" class="flex items-center gap-2">
            <select name="year" class="px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                <?php for ($y = 2020; $y <= (int)date('Y') + 2; $y++): ?>
                <option value="<?= $y ?>" <?= $y == ($year ?? date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Empleado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Año</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Días Permitidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Días Utilizados</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Días Pendientes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Arrastrados</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Extras</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Notas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($holidays)): ?>
                    <?php foreach ($holidays as $holiday): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($holiday['user_name'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['year'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['entitled_days'] ?? '0') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['used_days'] ?? '0') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['pending_days'] ?? '0') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['carried_over'] ?? '0') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($holiday['extra_days'] ?? '0') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars(substr($holiday['notes'] ?? '', 0, 50)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-600">No hay vacaciones registradas para este año</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
