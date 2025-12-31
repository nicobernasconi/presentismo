<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Superadmins por Empresa</h1>
            <p class="text-gray-600 mt-1">Usuarios administradores asignados a cada empresa</p>
        </div>
        <a href="<?= $baseUrl ?>/admin/usuarios/crear" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition">
            <i class="fas fa-plus mr-2"></i>Crear Superadmin
        </a>
    </div>

    <?php if (!empty($users)): ?>
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Empresa Asignada</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Desde</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-crown text-primary-600"></i>
                            </div>
                            <?= htmlspecialchars($user['name']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            <i class="fas fa-building mr-1"></i><?= htmlspecialchars($user['tenant_name']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php if ($user['is_active']): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Activo
                        </span>
                        <?php else: ?>
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                            <i class="fas fa-times-circle mr-1"></i>Inactivo
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                    <td class="px-6 py-4 text-sm text-right space-x-3">
                        <a href="<?= $baseUrl ?>/admin/usuarios/<?= $user['id'] ?>/editar" class="text-blue-600 hover:text-blue-900 font-medium inline-block">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form method="POST" action="<?= $baseUrl ?>/admin/usuarios/<?= $user['id'] ?>/eliminar" style="display:inline;" data-confirm="¿Está seguro? Se eliminará el acceso de este superadmin.">
                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
        <i class="fas fa-user-secret text-5xl text-gray-300 mb-4 block"></i>
        <p class="text-gray-600 text-lg font-medium">No hay superadmins creados</p>
        <p class="text-gray-500 text-sm mt-1">Crea un superadmin para cada empresa desde el botón "Crear Superadmin"</p>
        <a href="<?= $baseUrl ?>/admin/usuarios/crear" class="text-primary-600 hover:text-primary-900 text-sm font-medium mt-4 inline-block">
            <i class="fas fa-plus mr-1"></i>Crear el primer superadmin
        </a>
    </div>
    <?php endif; ?>
</div>
