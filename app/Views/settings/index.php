<?php
/**
 * Configuración - Principal
 */
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Configuración</h2>
    <p class="text-gray-600 mt-1">Gestiona la configuración del sistema</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Configuración Empresa -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/configuracion/empresa" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Empresa</h3>
            <i class="fas fa-building text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Información de la empresa y datos generales</p>
    </a>

    <!-- Horarios -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/turnos" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Turnos</h3>
            <i class="fas fa-clock text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Gestiona los turnos de trabajo</p>
    </a>

    <!-- Centros -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/centros" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Centros de Trabajo</h3>
            <i class="fas fa-map-marker-alt text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Administra centros de trabajo</p>
    </a>

    <!-- Departamentos -->
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Departamentos</h3>
            <i class="fas fa-sitemap text-primary-600 text-2xl"></i>
        </div>
        <p class="text-gray-600 text-sm">Gestiona departamentos</p>
    </a>
</div>
