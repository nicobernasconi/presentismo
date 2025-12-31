<?php
/**
 * Turnos - Listado
 */
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Turnos</h2>
            <p class="text-gray-600 mt-1">Gestiona los turnos de trabajo</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos/crear" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nuevo Turno
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($shifts)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($shifts as $shift): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($shift['name'] ?? '') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?= htmlspecialchars($shift['code'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if (!empty($shift['use_time_blocks'])): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-calendar-week mr-1"></i>Bloques
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-clock mr-1"></i>Fijo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php if (!empty($shift['use_time_blocks'])): ?>
                                <span class="text-gray-500 italic">Variable por día</span>
                            <?php else: ?>
                                <?= substr($shift['start_time'] ?? '', 0, 5) ?> - <?= substr($shift['end_time'] ?? '', 0, 5) ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($shift['is_active']): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos/<?= $shift['id'] ?>/bloques" 
                                   class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded hover:bg-purple-700"
                                   title="Gestionar bloques de tiempo por día">
                                    <i class="fas fa-calendar-week mr-1"></i>
                                    Bloques
                                </a>
                                <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos/<?= $shift['id'] ?>/editar" 
                                   class="text-blue-600 hover:text-blue-900"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos/<?= $shift['id'] ?>/eliminar" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este turno?')">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-gray-600">No hay turnos registrados</p>
        <?php endif; ?>
    </div>
</div>
