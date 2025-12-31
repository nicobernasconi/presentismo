<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $currentYear = date('Y');
        $year = $this->query('year', $currentYear);

        $holidays = $db->fetchAll(
            "SELECT h.*, u.name as user_name FROM holidays h 
             LEFT JOIN users u ON h.user_id = u.id 
             WHERE h.tenant_id = ? AND h.year = ? ORDER BY h.created_at DESC",
            [$tenantId, $year]
        );

        $this->view('holidays.index', [
            'title' => 'Vacaciones',
            'holidays' => $holidays,
            'year' => $year,
        ]);
    }

    public function calendar(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $year = $this->query('year', date('Y'));

        // Obtener vacaciones del año
        $holidays = $db->fetchAll(
            "SELECT h.*, u.name as user_name FROM holidays h 
             LEFT JOIN users u ON h.user_id = u.id 
             WHERE h.tenant_id = ? AND h.year = ? ORDER BY h.created_at ASC",
            [$tenantId, $year]
        );

        $this->view('holidays.calendar', [
            'title' => 'Calendario de Vacaciones',
            'holidays' => $holidays,
            'year' => $year,
        ]);
    }
}
