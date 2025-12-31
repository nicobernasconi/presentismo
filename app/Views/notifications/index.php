<?php
/**
 * Notificaciones
 */
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Notificaciones</h2>
        <?php if (!empty($notifications)): ?>
        <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/notificaciones/leer-todas" style="display:inline;">
            <button type="submit" class="text-primary-600 hover:text-primary-900 text-sm font-medium">Marcar todo como leído</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($notifications)): ?>
        <div class="space-y-2">
            <?php foreach ($notifications as $notification): ?>
            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 <?= $notification['is_read'] ? 'bg-gray-50' : 'bg-primary-50' ?>">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900"><?= htmlspecialchars($notification['title'] ?? '') ?></p>
                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($notification['message'] ?? '') ?></p>
                        <p class="text-xs text-gray-500 mt-2"><?= date('d/m/Y H:i', strtotime($notification['created_at'] ?? '')) ?></p>
                    </div>
                    <?php if (!$notification['is_read']): ?>
                    <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/notificaciones/<?= $notification['id'] ?>/leer" style="display:inline;">
                        <button type="submit" class="text-primary-600 hover:text-primary-900 text-sm">Marcar leída</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-600">No hay notificaciones</p>
        <?php endif; ?>
    </div>
</div>
