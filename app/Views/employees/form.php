<?php
/**
 * Vista de Crear/Editar Empleado
 * Variables disponibles:
 * - $employee: datos del empleado (si es edición)
 * - $departments: lista de departamentos
 * - $roles: lista de roles
 * - $shifts: lista de turnos
 * - $workCenters: lista de centros de trabajo
 */

$isEdit = !empty($employee);
$pageTitle = $isEdit ? 'Editar Empleado' : 'Nuevo Empleado';

// Determinar si usa query string o URL limpia
$usesQueryString = strpos($baseUrl, 'index.php?route=') !== false;
if ($usesQueryString) {
    // Extraer la base sin el query string
    $formAction = str_replace('?route=', '', $baseUrl);
    $routeValue = $isEdit ? "empleados/{$employee['id']}" : 'empleados';
} else {
    $formAction = $isEdit ? rtrim($baseUrl, '/') . "/empleados/{$employee['id']}" : rtrim($baseUrl, '/') . '/empleados';
    $routeValue = null;
}
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
        <a href="<?= $baseUrl ?>empleados" class="hover:text-indigo-600 transition-colors flex items-center">
            <i class="fas fa-users mr-1"></i> Empleados
        </a>
        <i class="fas fa-chevron-right text-xs text-gray-300"></i>
        <span class="text-gray-900 font-medium"><?= $pageTitle ?></span>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-900"><?= $pageTitle ?></h2>
            <p class="text-gray-500 mt-1"><?= $isEdit ? 'Modifica los datos del empleado' : 'Completa el formulario para registrar un nuevo empleado' ?></p>
        </div>
        <a href="<?= $baseUrl ?>empleados" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>
</div>

<form method="POST" action="<?= $formAction ?>" class="space-y-8">
    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <?php if ($routeValue): ?>
        <input type="hidden" name="route" value="<?= $routeValue ?>">
    <?php endif; ?>

    <!-- Información Personal -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                Información Personal
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="<?= htmlspecialchars($employee['first_name'] ?? old('first_name', '')) ?>"
                           required
                           placeholder="Ingresa el nombre"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                    <?php if (!empty($errors['first_name'])): ?>
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i><?= $errors['first_name'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="<?= htmlspecialchars($employee['last_name'] ?? old('last_name', '')) ?>"
                           required
                           placeholder="Ingresa los apellidos"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                    <?php if (!empty($errors['last_name'])): ?>
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i><?= $errors['last_name'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="dni" class="block text-sm font-semibold text-gray-700 mb-2">
                        DNI/NIE <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="dni" 
                           name="dni" 
                           value="<?= htmlspecialchars($employee['dni'] ?? old('dni', '')) ?>"
                           required
                           placeholder="12345678A"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                    <?php if (!empty($errors['dni'])): ?>
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i><?= $errors['dni'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="<?= htmlspecialchars($employee['phone'] ?? old('phone', '')) ?>"
                           placeholder="+34 600 000 000"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                </div>

                <div>
                    <label for="hire_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha de Alta <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="hire_date" 
                           name="hire_date" 
                           value="<?= htmlspecialchars($employee['hire_date'] ?? old('hire_date', date('Y-m-d'))) ?>"
                           required
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Información Laboral -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-briefcase text-white"></i>
                </div>
                Información Laboral
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="role_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Rol <span class="text-red-500">*</span>
                    </label>
                    <select id="role_id" 
                            name="role_id" 
                            required
                            class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                        <option value="">Selecciona un rol</option>
                        <?php foreach ($roles ?? [] as $roleId => $roleName): ?>
                            <option value="<?= $roleId ?>" 
                                    <?= ($employee['role_id'] ?? old('role_id', '')) == $roleId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($roleName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['role_id'])): ?>
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i><?= $errors['role_id'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Departamento <span class="text-red-500">*</span>
                    </label>
                    <select id="department_id" 
                            name="department_id" 
                            required
                            class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                        <option value="">Selecciona un departamento</option>
                        <?php foreach ($departments ?? [] as $deptId => $deptName): ?>
                        <?php if ($deptId === '') continue; ?>
                        <option value="<?= $deptId ?>" 
                                <?= ($employee['department_id'] ?? old('department_id', '')) == $deptId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($deptName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['department_id'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['department_id'] ?></p>
                <?php endif; ?>
                </div>

                <div>
                    <label for="work_center_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        Centro de Trabajo
                    </label>
                    <select id="work_center_id" 
                            name="work_center_id" 
                            class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                        <option value="">Sin asignar</option>
                        <?php foreach ($workCenters ?? [] as $centerId => $centerName): ?>
                            <?php if ($centerId === '') continue; ?>
                            <option value="<?= $centerId ?>" 
                                    <?= ($employee['work_center_id'] ?? old('work_center_id', '')) == $centerId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($centerName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="employee_code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Código de Empleado
                    </label>
                    <input type="text" 
                           id="employee_code" 
                           name="employee_code" 
                           value="<?= htmlspecialchars($employee['employee_code'] ?? old('employee_code', '')) ?>"
                           placeholder="EMP001"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar-check text-indigo-500 mr-1"></i>
                        Turnos Asignados
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php foreach ($shifts ?? [] as $shiftId => $shiftName): ?>
                            <?php if ($shiftId === '') continue; ?>
                            <?php $isChecked = in_array((string)$shiftId, array_map('strval', $employee['shift_ids'] ?? [])); ?>
                            <label class="relative flex items-center p-4 bg-gray-50 border-2 rounded-xl cursor-pointer transition-all hover:border-indigo-300 hover:bg-indigo-50/30 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                                <input type="checkbox" 
                                       name="shifts[]" 
                                       value="<?= $shiftId ?>"
                                       <?= $isChecked ? 'checked' : '' ?>
                                       class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <?= htmlspecialchars($shiftName) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Credenciales de Acceso -->
    <?php if (!$isEdit): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-key text-white"></i>
                </div>
                Credenciales de Acceso
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="••••••••"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                    <p class="mt-2 text-sm text-gray-500 flex items-center"><i class="fas fa-info-circle mr-1 text-indigo-400"></i>Mínimo 8 caracteres</p>
                    <?php if (!empty($errors['password'])): ?>
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i><?= $errors['password'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirmar Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="••••••••"
                           class="form-input-modern w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Estado -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-slate-600 to-gray-700">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-toggle-on text-white"></i>
                </div>
                Estado del Empleado
            </h3>
        </div>

        <div class="p-6">
            <label class="relative flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="is_active" 
                       value="1"
                       <?= ($employee['is_active'] ?? true) ? 'checked' : '' ?>
                       class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                <span class="ml-3 text-sm font-semibold text-gray-700">Empleado Activo</span>
            </label>
            <p class="mt-3 text-sm text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                Los empleados inactivos no podrán acceder al sistema ni realizar fichajes
            </p>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
        <a href="<?= $baseUrl ?>empleados" class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-400 font-semibold transition-all flex items-center justify-center">
            <i class="fas fa-times mr-2"></i>
            Cancelar
        </a>
        <button type="submit" class="w-full sm:w-auto px-8 py-3 btn-primary-modern text-white rounded-xl font-semibold shadow-lg flex items-center justify-center">
            <i class="fas fa-<?= $isEdit ? 'save' : 'plus-circle' ?> mr-2"></i>
            <?= $isEdit ? 'Guardar Cambios' : 'Crear Empleado' ?>
        </button>
    </div>
</form>
