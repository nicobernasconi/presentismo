<?php
/**
 * Dashboard - Vista principal del sistema
 */
?>

<!-- Welcome Banner -->
<div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 rounded-2xl p-6 mb-8 shadow-xl">
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl"></div>
    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                ¡Bienvenido de nuevo! 👋
            </h2>
            <p class="text-indigo-200">
                Aquí tienes un resumen de la actividad de hoy, <?= date('d \d\e F \d\e Y') ?>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="<?= $baseUrl ?? '' ?>/fichajes" class="inline-flex items-center px-5 py-2.5 bg-white text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <i class="fas fa-qrcode mr-2"></i>
                Fichar Ahora
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Empleados -->
    <div class="stat-card card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm animate-fade-in">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Empleados Activos</p>
                <p class="text-3xl font-bold text-gray-900"><?= $statistics['employees_count'] ?? 0 ?></p>
                <p class="text-xs text-emerald-600 mt-2 flex items-center">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>+2 esta semana</span>
                </p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Proyectos Activos -->
    <div class="stat-card card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm animate-fade-in animate-delay-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Proyectos Activos</p>
                <p class="text-3xl font-bold text-gray-900"><?= $statistics['active_projects'] ?? 0 ?></p>
                <p class="text-xs text-gray-500 mt-2 flex items-center">
                    <i class="fas fa-minus mr-1"></i>
                    <span>Sin cambios</span>
                </p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <i class="fas fa-project-diagram text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pendientes Aprobación -->
    <div class="stat-card card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm animate-fade-in animate-delay-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Pendientes Aprobación</p>
                <p class="text-3xl font-bold text-orange-600"><?= $statistics['pending_approvals'] ?? 0 ?></p>
                <p class="text-xs text-orange-600 mt-2 flex items-center">
                    <i class="fas fa-clock mr-1"></i>
                    <span>Requiere atención</span>
                </p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                <i class="fas fa-hourglass-half text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Fichajes Incompletos -->
    <div class="stat-card card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm animate-fade-in animate-delay-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Sin Salida Hoy</p>
                <p class="text-3xl font-bold text-red-600"><?= $statistics['late_checkouts'] ?? 0 ?></p>
                <p class="text-xs text-red-600 mt-2 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span>Pendientes</span>
                </p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-500/30">
                <i class="fas fa-user-clock text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Fichajes de Hoy (2 columns) -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
                Fichajes de Hoy
            </h3>
            <a href="<?= $baseUrl ?? '' ?>/fichajes/historial" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
                Ver todos <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="p-6">
            <?php if (!empty($todayCheckIns)): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-modern">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empleado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entrada</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Salida</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($todayCheckIns as $entry): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-medium text-sm mr-3 shadow">
                                        <?= strtoupper(substr($entry['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($entry['name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-medium">
                                    <i class="fas fa-sign-in-alt mr-1.5 text-xs"></i>
                                    <?= date('H:i', strtotime($entry['clock_in_time'] ?? '')) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <?php if ($entry['clock_out_time']): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-medium">
                                        <i class="fas fa-sign-out-alt mr-1.5 text-xs"></i>
                                        <?= date('H:i', strtotime($entry['clock_out_time'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 font-medium">
                                        <i class="fas fa-hourglass-half mr-1.5 text-xs animate-pulse"></i>
                                        Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <?php if ($entry['clock_out_time']): ?>
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm">
                                        <i class="fas fa-check mr-1"></i> Completado
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm">
                                        <i class="fas fa-sync-alt mr-1 animate-spin"></i> En curso
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No hay fichajes registrados hoy</p>
                <p class="text-sm text-gray-400 mt-1">Los fichajes aparecerán aquí cuando se registren</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions (1 column) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-violet-500/20">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                Acciones Rápidas
            </h3>
        </div>
        <div class="p-4 space-y-3">
            <a href="<?= $baseUrl ?? '' ?>/fichajes" class="flex items-center p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl hover:from-emerald-100 hover:to-teal-100 transition-all group border border-emerald-100">
                <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-qrcode text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Registrar Fichaje</p>
                    <p class="text-xs text-gray-500">Entrada o salida</p>
                </div>
                <i class="fas fa-chevron-right text-emerald-500"></i>
            </a>
            
            <a href="<?= $baseUrl ?? '' ?>/ausencias/solicitar" class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl hover:from-orange-100 hover:to-amber-100 transition-all group border border-orange-100">
                <div class="w-11 h-11 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-plus text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Solicitar Ausencia</p>
                    <p class="text-xs text-gray-500">Vacaciones, permisos...</p>
                </div>
                <i class="fas fa-chevron-right text-orange-500"></i>
            </a>
            
            <a href="<?= $baseUrl ?? '' ?>/empleados/crear" class="flex items-center p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl hover:from-indigo-100 hover:to-purple-100 transition-all group border border-indigo-100">
                <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Nuevo Empleado</p>
                    <p class="text-xs text-gray-500">Registrar personal</p>
                </div>
                <i class="fas fa-chevron-right text-indigo-500"></i>
            </a>
            
            <a href="<?= $baseUrl ?? '' ?>/reportes" class="flex items-center p-4 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl hover:from-cyan-100 hover:to-blue-100 transition-all group border border-cyan-100">
                <div class="w-11 h-11 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-bar text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Ver Reportes</p>
                    <p class="text-xs text-gray-500">Estadísticas y análisis</p>
                </div>
                <i class="fas fa-chevron-right text-cyan-500"></i>
            </a>
        </div>
    </div>
</div>

<!-- Ausencias Pendientes -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-orange-500/20">
                <i class="fas fa-calendar-alt text-white text-sm"></i>
            </div>
            Ausencias Pendientes de Aprobación
        </h3>
        <a href="<?= $baseUrl ?? '' ?>/ausencias" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium flex items-center">
            Ver todas <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <div class="p-6">
        <?php if (!empty($pendingAbsences)): ?>
        <div class="overflow-x-auto">
            <table class="w-full table-modern">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fechas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($pendingAbsences as $absence): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center text-white font-medium text-sm mr-3 shadow">
                                    <?= strtoupper(substr($absence['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($absence['name'] ?? '') ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-purple-50 text-purple-700 text-sm font-medium">
                                <?= htmlspecialchars($absence['type_name'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            <i class="fas fa-calendar-day mr-1 text-gray-400"></i>
                            <?= date('d/m/Y', strtotime($absence['start_date'] ?? '')) ?> 
                            <i class="fas fa-arrow-right mx-1 text-gray-300 text-xs"></i>
                            <?= date('d/m/Y', strtotime($absence['end_date'] ?? '')) ?>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm">
                                <i class="fas fa-clock mr-1"></i> Pendiente
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right">
                            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/ausencias/<?= $absence['id'] ?>" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors font-medium text-sm">
                                <i class="fas fa-eye mr-2"></i> Revisar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-emerald-500 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No hay ausencias pendientes de aprobación</p>
            <p class="text-sm text-gray-400 mt-1">¡Todo al día!</p>
        </div>
        <?php endif; ?>
    </div>
</div>
