<?php
/**
 * Reportes - Página principal
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Reportes</h2>
    <p class="text-gray-600 mt-1">Genera y visualiza reportes del sistema</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Reporte de Fichajes -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/fichajes" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Fichajes</h3>
            <i class="fas fa-clock text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Reporte detallado de fichajes y asistencia</p>
    </a>

    <!-- Reporte de Ausencias -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/ausencias" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Ausencias</h3>
            <i class="fas fa-calendar-alt text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Resumen de ausencias y vacaciones</p>
    </a>

    <!-- Reporte de Horas -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/reportes/horas" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Horas</h3>
            <i class="fas fa-hourglass-end text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Análisis de horas trabajadas</p>
    </a>
</div>
