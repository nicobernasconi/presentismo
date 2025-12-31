<?php
/**
 * Listado de Planes
 */
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-900">Gestión de Planes</h2>
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/planes/crear" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i> Nuevo Plan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($plans)): ?>
        <?php foreach ($plans as $plan): ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($plan['name'] ?? '') ?></h3>
                    <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($plan['description'] ?? '') ?></p>
                </div>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium whitespace-nowrap ml-2">
                    <?= htmlspecialchars($plan['max_employees'] ?? 0) ?> emp
                </span>
            </div>

            <div class="border-t border-gray-200 pt-4 pb-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Precio Mensual</span>
                    <span class="text-lg font-bold text-gray-900">$<?= number_format($plan['monthly_price'], 2) ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Precio Anual</span>
                    <span class="text-lg font-bold text-gray-900">$<?= number_format($plan['annual_price'], 2) ?></span>
                </div>
            </div>

            <div class="mt-auto pt-4 border-t border-gray-200 flex gap-2">
                <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/planes/<?= $plan['id'] ?>/editar" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded text-sm text-center font-medium">
                    <i class="fas fa-edit mr-1"></i>Editar
                </a>
                <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/planes/<?= $plan['id'] ?>/eliminar" style="flex: 1;"
                      data-confirm="¿Está seguro?">
                    <input type="hidden" name="id" value="<?= $plan['id'] ?>">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full bg-gray-50 rounded-lg border border-gray-200 p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4 block"></i>
            <p class="text-gray-600">No hay planes registrados</p>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/planes/crear" class="text-blue-600 hover:text-blue-900 text-sm font-medium mt-2 inline-block">
                Crear el primer plan →
            </a>
        </div>
    <?php endif; ?>
</div>
