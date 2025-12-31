<?php
/**
 * Vista: Detalle de Empleado
 * Variables:
 * - $employee: objeto User
 * - $department: objeto Department|null
 * - $workCenter: objeto WorkCenter|null
 * - $role: objeto Role|null
 * - $currentShift: array|object|null
 * - $holidayBalance: array|null
 */

?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Empleado: <?= htmlspecialchars($employee->name ?? '') ?></h2>
            <p class="text-gray-600 mt-1">Código: <?= htmlspecialchars($employee->employee_code ?? '—') ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados/<?= (int)($employee->id ?? 0) ?>/editar" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
                <i class="fas fa-edit mr-2"></i>
                Editar
            </a>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/empleados" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">
                Volver
            </a>
        </div>
    </div>
</div>

<!-- Tarjetas resumen -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600 mb-1">Estado</p>
        <p class="text-2xl font-bold <?= ($employee->is_active ?? true) ? 'text-green-700' : 'text-red-700' ?>">
            <?= ($employee->is_active ?? true) ? 'Activo' : 'Inactivo' ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600 mb-1">Departamento</p>
        <p class="text-2xl font-bold text-gray-900">
            <?= htmlspecialchars($department->name ?? 'Sin asignar') ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600 mb-1">Rol</p>
        <p class="text-2xl font-bold text-gray-900">
            <?= htmlspecialchars($role->name ?? 'Empleado') ?>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600 mb-1">Vacaciones disponibles</p>
        <p class="text-2xl font-bold text-gray-900">
            <?= (int)($holidayBalance->available_vacation_days ?? 0) ?> días
        </p>
    </div>
</div>

<!-- Información general -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-id-card text-primary-600 mr-2"></i>
                Información Personal
            </h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-medium">Nombre:</span> <?= htmlspecialchars(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) ?></p>
                <p><span class="font-medium">Email:</span> <?= htmlspecialchars($employee->email ?? '') ?></p>
                <p><span class="font-medium">DNI/NIE:</span> <?= htmlspecialchars($employee->dni ?? '—') ?></p>
                <p><span class="font-medium">Teléfono:</span> <?= htmlspecialchars($employee->phone ?? '—') ?></p>
                <p><span class="font-medium">Alta:</span> <?= $employee->hire_date ? date('d/m/Y', strtotime($employee->hire_date)) : '—' ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-briefcase text-primary-600 mr-2"></i>
                Información Laboral
            </h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p><span class="font-medium">Centro:</span> <?= htmlspecialchars($workCenter->name ?? 'Sin asignar') ?></p>
                <p><span class="font-medium">Posición:</span> <?= htmlspecialchars($employee->position ?? '—') ?></p>
                <p><span class="font-medium">Contrato:</span> <?= htmlspecialchars($employee->contract_type ?? '—') ?></p>
                <p><span class="font-medium">Horas/semana:</span> <?= (int)($employee->hours_per_week ?? 40) ?></p>
                <p><span class="font-medium">Turno actual:</span>
                    <?php if (!empty($currentShift)): ?>
                        <?= htmlspecialchars(($currentShift['name'] ?? $currentShift->name ?? '')) ?>
                        <span class="text-gray-500">(<?= ($currentShift['start_time'] ?? $currentShift->start_time ?? '') ?> - <?= ($currentShift['end_time'] ?? $currentShift->end_time ?? '') ?>)</span>
                    <?php else: ?>
                        Sin asignar
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex flex-wrap gap-3">
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/fichajes" class="inline-flex items-center bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-fingerprint mr-2"></i> Ver Fichajes
        </a>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-calendar-alt mr-2"></i> Ver Ausencias
        </a>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/vacaciones" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-umbrella-beach mr-2"></i> Ver Vacaciones
        </a>
    </div>
</div>
