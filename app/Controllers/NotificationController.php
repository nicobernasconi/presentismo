<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        $userId = Auth::id();
        $db = Database::getInstance();

        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50",
            [$userId]
        );

        $this->view('notifications.index', [
            'title' => 'Notificaciones',
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(): void
    {
        $userId = Auth::id();
        $notificationId = $this->input('id');
        $db = Database::getInstance();

        $db->update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            "id = ? AND user_id = ?",
            [$notificationId, $userId]
        );

        $this->json(['success' => true]);
    }

    public function markAllAsRead(): void
    {
        $userId = Auth::id();
        $db = Database::getInstance();

        $db->update(
            'notifications',
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            "user_id = ? AND is_read = 0",
            [$userId]
        );

        $this->json(['success' => true]);
    }
}
