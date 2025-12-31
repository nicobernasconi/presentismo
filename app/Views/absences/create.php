<?php
/**
 * Vista de Crear Solicitud de Ausencia
 * Variables disponibles:
 * - $absenceTypes: tipos de ausencias disponibles
 * - $availableVacationDays: días de vacaciones disponibles
 */
?>

<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-gray-600 mb-3">
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" class="hover:text-primary-600">Ausencias</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900">Nueva Solicitud</span>
    </div>
    <h2 class="text-2xl font-bold text-gray-900">Nueva Solicitud de Ausencia</h2>
</div>

<!-- Información de Vacaciones -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-semibold text-blue-900 mb-1">Balance de Vacaciones</h3>
            <p class="text-sm text-blue-800">
                Tienes <span class="font-bold"><?= $availableVacationDays ?? 0 ?> días</span> de vacaciones disponibles
            </p>
        </div>
    </div>
</div>

<form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" x-data="absenceForm()" class="space-y-6">
    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <!-- Tipo de Ausencia -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-list-alt text-primary-600 mr-2"></i>
            Tipo de Ausencia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($absenceTypes ?? [] as $type): ?>
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 hover:border-primary-500 transition-colors">
                    <input type="radio" 
                           name="absence_type_id" 
                           value="<?= $type['id'] ?>" 
                           x-model="selectedType"
                           @change="calculateDays()"
                           required
                           class="sr-only">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 mr-3">
                                <i class="fas fa-<?= 
                                    $type['name'] === 'Vacaciones' ? 'umbrella-beach' :
                                    ($type['name'] === 'Baja Médica' ? 'medkit' :
                                    ($type['name'] === 'Permiso Personal' ? 'user' :
                                    'calendar-alt'))
                                ?> text-primary-600"></i>
                            </span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-900">
                                    <?= htmlspecialchars($type['name']) ?>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <?= $type['is_paid'] ? '✓ Remunerada' : '✗ No remunerada' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-3 flex items-center" x-show="selectedType == <?= $type['id'] ?>">
                        <i class="fas fa-check-circle text-primary-600 text-xl"></i>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($errors['absence_type_id'])): ?>
            <p class="mt-2 text-sm text-red-600"><?= $errors['absence_type_id'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Fechas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-calendar text-primary-600 mr-2"></i>
            Período de Ausencia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Inicio <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="start_date" 
                       name="start_date" 
                       x-model="startDate"
                       @change="calculateDays()"
                       min="<?= date('Y-m-d') ?>"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <?php if (!empty($errors['start_date'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['start_date'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Fin <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       x-model="endDate"
                       @change="calculateDays()"
                       min="<?= date('Y-m-d') ?>"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <?php if (!empty($errors['end_date'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['end_date'] ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Duración Calculada -->
        <div x-show="totalDays > 0" class="mt-4 bg-primary-50 border border-primary-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-primary-900">Duración total:</span>
                <span class="text-2xl font-bold text-primary-700">
                    <span x-text="totalDays"></span> día<span x-show="totalDays > 1">s</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Motivo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-comment-alt text-primary-600 mr-2"></i>
            Motivo de la Ausencia
        </h3>

        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                Descripción <span class="text-red-500">*</span>
            </label>
            <textarea id="reason" 
                      name="reason" 
                      rows="4" 
                      required
                      placeholder="Describe el motivo de tu ausencia..."
                      class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"><?= old('reason', '') ?></textarea>
            <p class="mt-2 text-sm text-gray-500">
                Proporciona una descripción clara del motivo de tu ausencia
            </p>
            <?php if (!empty($errors['reason'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= $errors['reason'] ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex items-center justify-end space-x-4">
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition duration-200">
            <i class="fas fa-paper-plane mr-2"></i>
            Enviar Solicitud
        </button>
    </div>
</form>

<script>
function absenceForm() {
    return {
        selectedType: '',
        startDate: '',
        endDate: '',
        totalDays: 0,

        calculateDays() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                
                if (end >= start) {
                    // Calcular días laborables (excluyendo fines de semana)
                    let days = 0;
                    let current = new Date(start);
                    
                    while (current <= end) {
                        const dayOfWeek = current.getDay();
                        // 0 = Domingo, 6 = Sábado
                        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                            days++;
                        }
                        current.setDate(current.getDate() + 1);
                    }
                    
                    this.totalDays = days;
                } else {
                    this.totalDays = 0;
                }
            }
        }
    }
}
</script>
