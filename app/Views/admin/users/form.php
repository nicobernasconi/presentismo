<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($title ?? 'Superadmin') ?></h1>
        <p class="text-gray-600 mt-2">
            <?php if (isset($user) && is_array($user) && !empty($user['id'])): ?>
            Editar superadmin vinculado a una empresa
            <?php else: ?>
            Crear un nuevo superadmin vinculado a una empresa
            <?php endif; ?>
        </p>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-8">
        <?php 
        // DEBUG: Para asegurar que $user es null cuando se crea
        // error_log("Form DEBUG: isCreate=" . (is_null($user) ? 'true' : 'false') . ", user=" . gettype($user));
        
        // Determinar si es creación o edición
        $isCreate = is_null($user) || !is_array($user) || empty($user['id'] ?? null);
        ?>
        
        <form method="POST" action="<?= $baseUrl ?>/admin/usuarios<?php if (!$isCreate && isset($user['id'])): ?>/<?= htmlspecialchars($user['id']) ?><?php endif; ?>" class="space-y-6">
            <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <?php if (!$isCreate): ?>
            <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <!-- Empresa -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-building text-blue-600 text-xl mt-1"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-2">Empresa Asignada</h3>
                        <?php if ($isCreate): ?>
                        <!-- Creación: select de empresa -->
                        <?php if (empty($companies)): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>
                            No hay empresas disponibles. 
                            <a href="<?= $baseUrl ?>/admin/empresas/crear" class="underline font-semibold hover:text-yellow-900">Crear empresa primero</a>
                            </p>
                        </div>
                        <?php else: ?>
                        <select id="tenant_id" name="tenant_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium" required>
                            <option value="">-- Selecciona una empresa --</option>
                            <?php foreach ($companies as $company): ?>
                            <option value="<?= $company['id'] ?>">
                                <i class="fas fa-building"></i> <?= htmlspecialchars($company['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-gray-600 text-sm mt-2">Este superadmin tendrá acceso únicamente a la empresa seleccionada.</p>
                        <?php endif; ?>
                        <?php else: ?>
                        <!-- Edición: mostrar empresa actual -->
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <p class="text-gray-900 font-semibold text-lg"><?= htmlspecialchars($user['tenant_name'] ?? 'N/A') ?></p>
                        </div>
                        <p class="text-gray-600 text-sm mt-2">La empresa no puede ser cambiada. Para cambiarla, elimine este superadmin y cree uno nuevo.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Nombre Completo *
                </label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Ej: Juan García López" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email *
                </label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="admin@empresa.com" required>
                <p class="text-gray-600 text-sm mt-1">El email debe ser único dentro de la empresa.</p>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña 
                    <?php if (!empty($user)): ?>
                    <span class="text-gray-500 font-normal">(dejar en blanco para no cambiar)</span>
                    <?php else: ?>
                    <span class="text-red-600">*</span>
                    <?php endif; ?>
                </label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                       <?php if (empty($user)): ?>required<?php endif; ?> 
                       minlength="6"
                       placeholder="Mínimo 6 caracteres">
                <p class="text-gray-600 text-sm mt-1">Usa caracteres mixtos (mayúsculas, minúsculas, números) para mayor seguridad.</p>
            </div>

            <!-- Separador -->
            <hr class="border-gray-200">

            <!-- Botones -->
            <div class="flex gap-3 justify-end">
                <a href="<?= $baseUrl ?>/admin/usuarios" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i><?php if (!empty($user)): ?>Actualizar<?php else: ?>Crear Superadmin<?php endif; ?>
                </button>
            </div>
        </form>
    </div>
</div>
