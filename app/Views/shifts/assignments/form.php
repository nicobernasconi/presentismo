<?php
$baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
$pageTitle = __('shifts.assign_shift') ?? 'Asignar Turno';
?>

<?php include __DIR__ . '/../../layouts/app.php'; ?>

<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <a href="<?= $baseUrl ?>/turnos/asignaciones" class="text-primary-600 hover:text-primary-700">
            <i class="fas fa-arrow-left mr-2"></i><?= __('common.back') ?>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6"><?= $pageTitle ?></h1>

        <form method="POST" action="<?= $baseUrl ?>/turnos/asignaciones/guardar" class="space-y-6">
            <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Empleado -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= __('employees.employee') ?> <span class="text-red-500">*</span>
                </label>
                <select name="user_id" id="user_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value=""><?= __('employees.select_employee') ?? 'Seleccionar empleado' ?></option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee->id ?>" <?= isset($selectedUserId) && $selectedUserId == $employee->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($employee->name) ?>
                            <?php if ($employee->employee_code): ?>
                                (<?= htmlspecialchars($employee->employee_code) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Turno -->
            <div>
                <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= __('shifts.shift') ?> <span class="text-red-500">*</span>
                </label>
                <select name="shift_id" id="shift_id" required 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value=""><?= __('shifts.select_shift') ?? 'Seleccionar turno' ?></option>
                    <?php foreach ($shifts as $shift): ?>
                        <option value="<?= $shift->id ?>" data-start="<?= $shift->start_time ?>" data-end="<?= $shift->end_time ?>">
                            <?= htmlspecialchars($shift->name) ?> 
                            (<?= date('H:i', strtotime($shift->start_time)) ?> - <?= date('H:i', strtotime($shift->end_time)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="shiftInfo" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <p class="text-sm text-blue-800"></p>
                </div>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        <?= __('shifts.start_date') ?? 'Fecha de inicio' ?> <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required 
                           value="<?= date('Y-m-d') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        <?= __('shifts.end_date') ?? 'Fecha de fin' ?>
                        <span class="text-gray-500 text-xs">(<?= __('common.optional') ?? 'Opcional' ?>)</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">
                        <?= __('shifts.leave_empty_indefinite') ?? 'Dejar vacío para asignación indefinida' ?>
                    </p>
                </div>
            </div>

            <!-- Opciones -->
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="deactivate_current" id="deactivate_current" 
                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    </div>
                    <div class="ml-3">
                        <label for="deactivate_current" class="font-medium text-gray-700">
                            <?= __('shifts.deactivate_current_assignment') ?? 'Desactivar asignación actual' ?>
                        </label>
                        <p class="text-sm text-gray-500">
                            <?= __('shifts.deactivate_current_help') ?? 'Si el empleado tiene un turno activo, se desactivará automáticamente' ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= __('common.notes') ?>
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          placeholder="<?= __('shifts.assignment_notes_placeholder') ?? 'Notas adicionales sobre esta asignación' ?>"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="<?= $baseUrl ?>/turnos/asignaciones" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <?= __('common.cancel') ?>
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i>
                    <?= __('shifts.assign_shift') ?? 'Asignar Turno' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Mostrar información del turno seleccionado
document.getElementById('shift_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('shiftInfo');
    
    if (this.value) {
        const startTime = selectedOption.dataset.start;
        const endTime = selectedOption.dataset.end;
        const shiftName = selectedOption.text;
        
        infoDiv.querySelector('p').innerHTML = `
            <strong><?= __('shifts.selected_shift') ?? 'Turno seleccionado' ?>:</strong> ${shiftName}<br>
            <strong><?= __('common.schedule') ?? 'Horario' ?>:</strong> ${startTime} - ${endTime}
        `;
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
});

// Validar fechas
document.getElementById('start_date').addEventListener('change', validateDates);
document.getElementById('end_date').addEventListener('change', validateDates);

function validateDates() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (endDate && startDate && endDate < startDate) {
        alert('<?= __('shifts.end_date_after_start') ?? 'La fecha de fin debe ser posterior a la fecha de inicio' ?>');
        document.getElementById('end_date').value = '';
    }
}
</script>
