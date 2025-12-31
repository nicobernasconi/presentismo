<?php
/**
 * Vista del Módulo de Ausencias
 * Variables disponibles:
 * - $absences: lista de ausencias
 * - $absenceTypes: tipos de ausencias disponibles
 * - $filters: filtros aplicados
 * - $statistics: estadísticas de ausencias
 */
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gestión de Ausencias</h2>
            <p class="text-gray-600 mt-1">Solicita y gestiona ausencias y vacaciones</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/solicitar" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nueva Solicitud
        </a>
    </div>
</div>

<!-- Estadísticas de Ausencias -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Vacaciones Disponibles</p>
                <p class="text-2xl font-bold text-gray-900"><?= $statistics['available_vacation_days'] ?? 0 ?> días</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-umbrella-beach text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Vacaciones Usadas</p>
                <p class="text-2xl font-bold text-gray-900"><?= $statistics['used_vacation_days'] ?? 0 ?> días</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Ausencias Este Año</p>
                <p class="text-2xl font-bold text-gray-900"><?= $statistics['total_absences'] ?? 0 ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-calendar-times text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pendientes Aprobación</p>
                <p class="text-2xl font-bold text-orange-600"><?= $statistics['pending_approvals'] ?? 0 ?></p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Ausencia</label>
            <select id="type" 
                    name="type" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Todos</option>
                <?php foreach ($absenceTypes ?? [] as $id => $name): ?>
                    <option value="<?= htmlspecialchars($id) ?>" <?= ($filters['type'] ?? '') == $id ? 'selected' : '' ?>>
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
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Aprobado</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
            </select>
        </div>

        <div>
            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Año</label>
            <select id="year" 
                    name="year" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++):
                ?>
                    <option value="<?= $i ?>" <?= ($filters['year'] ?? $currentYear) == $i ? 'selected' : '' ?>>
                        <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-search mr-2"></i>
                Buscar
            </button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Lista de Ausencias -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-list text-primary-600 mr-2"></i>
            Mis Solicitudes
        </h3>

        <?php if (!empty($absences)): ?>
            <div class="space-y-4">
                <?php foreach ($absences as $absence): ?>
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?= 
                                        ($absence['absence_type_name'] === 'Vacaciones') ? 'bg-blue-100 text-blue-800' :
                                        (($absence['absence_type_name'] === 'Baja Médica') ? 'bg-red-100 text-red-800' :
                                        'bg-gray-100 text-gray-800')
                                    ?>">
                                        <i class="fas fa-<?= 
                                            ($absence['absence_type_name'] === 'Vacaciones') ? 'umbrella-beach' :
                                            (($absence['absence_type_name'] === 'Baja Médica') ? 'medkit' : 'calendar-alt')
                                        ?> mr-1"></i>
                                        <?= htmlspecialchars($absence['absence_type_name']) ?>
                                    </span>

                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado'
                                    ];
                                    $status = $absence['status'] ?? 'pending';
                                    ?>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <i class="fas fa-<?= 
                                            $status === 'approved' ? 'check-circle' : 
                                            ($status === 'rejected' ? 'times-circle' : 'clock')
                                        ?> mr-1"></i>
                                        <?= $statusLabels[$status] ?? $status ?>
                                    </span>

                                    <?php if ($absence['is_paid']): ?>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Remunerada
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-calendar text-primary-600 w-5 mr-2"></i>
                                        <span class="text-sm">
                                            <span class="font-medium">Desde:</span> 
                                            <?= date('d/m/Y', strtotime($absence['start_date'])) ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-calendar-check text-primary-600 w-5 mr-2"></i>
                                        <span class="text-sm">
                                            <span class="font-medium">Hasta:</span> 
                                            <?= date('d/m/Y', strtotime($absence['end_date'])) ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-hourglass-half text-primary-600 w-5 mr-2"></i>
                                        <span class="text-sm">
                                            <span class="font-medium">Duración:</span> 
                                            <?= $absence['total_days'] ?> día<?= $absence['total_days'] != 1 ? 's' : '' ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-clock text-primary-600 w-5 mr-2"></i>
                                        <span class="text-sm">
                                            <span class="font-medium">Solicitado:</span> 
                                            <?= date('d/m/Y', strtotime($absence['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($absence['reason'])): ?>
                                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                        <p class="text-sm text-gray-700">
                                            <i class="fas fa-comment-alt text-gray-500 mr-2"></i>
                                            <span class="font-medium">Motivo:</span> <?= htmlspecialchars($absence['reason']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($status === 'approved' && !empty($absence['approved_by_name'])): ?>
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-user-check text-green-600 mr-1"></i>
                                        Aprobado por <?= htmlspecialchars($absence['approved_by_name']) ?>
                                        el <?= date('d/m/Y', strtotime($absence['approved_at'])) ?>
                                    </div>
                                <?php elseif ($status === 'rejected' && !empty($absence['rejection_reason'])): ?>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                        <p class="text-sm text-red-800">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <span class="font-medium">Motivo del rechazo:</span> <?= htmlspecialchars($absence['rejection_reason']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Acciones -->
                            <?php if ($status === 'pending'): ?>
                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/<?= $absence['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $absence['id'] ?>)" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Cancelar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-calendar-alt text-6xl"></i>
                </div>
                <p class="text-gray-600 text-lg">No hay solicitudes de ausencia</p>
                <p class="text-gray-500 text-sm mt-2">Comienza creando una nueva solicitud</p>
                <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/solicitar" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Solicitud
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
        window.Dialog.confirm('¿Estás seguro de que deseas cancelar esta solicitud?')
            .then((ok) => {
                if (ok) {
                    const form = document.getElementById('delete-form');
                    form.action = '<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/' + id + '/cancelar';
                    form.submit();
                }
            });
    } else {
        if (confirm('¿Estás seguro de que deseas cancelar esta solicitud?')) {
            const form = document.getElementById('delete-form');
            form.action = '<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/' + id + '/cancelar';
            form.submit();
        }
    }
}
</script>
