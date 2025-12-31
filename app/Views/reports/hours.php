<?php
/**
 * Reportes - Horas
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Reporte de Horas</h2>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/horas" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
            <select id="month" name="month" class="w-full border-gray-300 rounded-lg">
                <?php 
                $monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                for ($m = 1; $m <= 12; $m++): 
                $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
                ?>
                <option value="<?= $monthValue ?>" <?= $month == $monthValue ? 'selected' : '' ?>>
                    <?= $monthNames[$m - 1] ?>
                </option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Año</label>
            <input type="number" id="year" name="year" value="<?= htmlspecialchars($year ?? '') ?>" min="2020" class="w-full border-gray-300 rounded-lg">
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg w-full">Filtrar</button>
        </div>
    </form>
</div>

<!-- Tabla de Reportes -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($hours)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Horas Totales</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Registros</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($hours as $hour): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($hour['name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= $hour['total_hours'] ?? 0 ?> h</td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= $hour['entries'] ?? 0 ?></td>
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
