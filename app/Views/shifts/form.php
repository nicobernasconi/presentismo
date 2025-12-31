<?php
/**
 * Turnos - Formulario
 */
$isEdit = isset($shift) && !empty($shift);
$submitUrl = $isEdit ? "/turnos/{$shift['id']}" : '/turnos';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900"><?= $isEdit ? 'Editar' : 'Crear' ?> Turno</h2>
</div>

<div x-data="shiftForm()" class="space-y-6">
    <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') . $submitUrl ?>" @submit="prepareSubmit">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Información Básica -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Información Básica</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="name" required class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                    <input type="text" name="code" x-model="code" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="color" name="color" x-model="color" class="h-10 w-20 border-gray-300 rounded">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" x-model="is_active" class="rounded border-gray-300">
                    <label class="ml-2 text-sm">Turno activo</label>
                </div>
            </div>
        </div>

        <!-- Tipo de Horario -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Tipo de Horario</h3>
            <div class="space-y-3">
                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer" 
                       :class="useTimeBlocks == 0 ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                    <input type="radio" name="use_time_blocks" value="0" x-model.number="useTimeBlocks" class="mt-1">
                    <div class="ml-3">
                        <div class="font-semibold">Horario Tradicional</div>
                        <div class="text-sm text-gray-600">Mismo horario para todos los días laborables</div>
                    </div>
                </label>
                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer"
                       :class="useTimeBlocks == 1 ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                    <input type="radio" name="use_time_blocks" value="1" x-model.number="useTimeBlocks" class="mt-1">
                    <div class="ml-3">
                        <div class="font-semibold">Bloques de Tiempo por Día</div>
                        <div class="text-sm text-gray-600">Define horarios específicos por cada día de la semana</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Horario Tradicional -->
        <div x-show="useTimeBlocks == 0" class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Configuración de Horario</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora Inicio</label>
                    <input type="time" name="start_time" x-model="start_time" :required="useTimeBlocks == 0" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fin</label>
                    <input type="time" name="end_time" x-model="end_time" :required="useTimeBlocks == 0" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descanso (min)</label>
                    <input type="number" name="break_duration" x-model.number="break_duration" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Días Laborables</label>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(day, idx) in ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']">
                        <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer"
                               :class="working_days.includes(idx + 1) ? 'bg-primary-50 border-primary-500' : 'border-gray-300'">
                            <input type="checkbox" name="working_days[]" :value="idx + 1" x-model="working_days" class="sr-only">
                            <span x-text="day"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>

        <!-- Bloques de Tiempo -->
        <div x-show="useTimeBlocks == 1" class="space-y-4 mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Los bloques de tiempo se configurarán después de crear el turno. Haz clic en "Gestionar Bloques" desde la lista de turnos.
                </p>
            </div>
        </div>

        <input type="hidden" name="time_blocks" x-model="timeBlocksJSON">

        <div class="flex gap-2">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg">
                <?= $isEdit ? 'Actualizar' : 'Crear' ?> Turno
            </button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg">
                Cancelar
            </a>
        </div>
    </form>
</div>
<script>
function shiftForm() {
    return {
        name: '<?= $shift['name'] ?? '' ?>',
        code: '<?= $shift['code'] ?? '' ?>',
        color: '<?= $shift['color'] ?? '#3B82F6' ?>',
        start_time: '<?= isset($shift['start_time']) ? substr($shift['start_time'], 0, 5) : '09:00' ?>',
        end_time: '<?= isset($shift['end_time']) ? substr($shift['end_time'], 0, 5) : '18:00' ?>',
        break_duration: <?= $shift['break_duration'] ?? 30 ?>,
        is_active: <?= isset($shift) ? ($shift['is_active'] ? 'true' : 'false') : 'true' ?>,
        useTimeBlocks: <?= isset($shift['use_time_blocks']) ? (int)$shift['use_time_blocks'] : 0 ?>,
        working_days: <?= isset($shift['working_days']) ? $shift['working_days'] : json_encode([1,2,3,4,5]) ?>,
        timeBlocks: {},
        
        get timeBlocksJSON() {
            return JSON.stringify(this.timeBlocks);
        },
        
        prepareSubmit(e) {
            console.log('Submitting form...');
        }
    }
}
</script>