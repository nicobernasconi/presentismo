<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use App\Models\Shift;
use App\Models\ShiftTimeBlock;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        if (!Auth::isAdmin()) {
            $this->withError('No tienes permisos para ver turnos');
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $shifts = $db->fetchAll(
            "SELECT * FROM shifts WHERE tenant_id = ? AND deleted_at IS NULL ORDER BY name ASC",
            [$tenantId]
        );

        $this->view('shifts.index', [
            'title' => 'Turnos',
            'shifts' => $shifts,
        ]);
    }

    public function create(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('shifts.form', [
            'title' => 'Nuevo Turno',
            'shift' => null,
        ]);
    }

    public function store(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $useTimeBlocks = (int) $this->input('use_time_blocks', 0);

        $data = [
            'tenant_id' => $tenantId,
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'color' => $this->input('color', '#3B82F6'),
            'start_time' => $this->input('start_time', '09:00'),
            'end_time' => $this->input('end_time', '18:00'),
            'break_duration' => $this->input('break_duration', 0),
            'is_active' => $this->input('is_active', 1),
            'use_time_blocks' => $useTimeBlocks,
            'working_days' => json_encode($this->input('working_days', [1,2,3,4,5])),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $shiftId = $db->insert('shifts', $data);

        // Si usa bloques de tiempo, procesarlos
        if ($useTimeBlocks) {
            $timeBlocks = $this->input('time_blocks');
            if ($timeBlocks) {
                $blocksData = json_decode($timeBlocks, true);
                $this->saveTimeBlocks($shiftId, $blocksData);
            }
        }

        $this->withSuccess('Turno creado exitosamente');
        $this->redirect('/turnos');
    }

    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $shift = $db->fetch(
            "SELECT * FROM shifts WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$shift) {
            $this->withError('Turno no encontrado');
            $this->redirect('/turnos');
            return;
        }

        $this->view('shifts.form', [
            'title' => 'Editar Turno',
            'shift' => $shift,
        ]);
    }

    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $useTimeBlocks = (int) $this->input('use_time_blocks', 0);

        $data = [
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'color' => $this->input('color', '#3B82F6'),
            'start_time' => $this->input('start_time', '09:00'),
            'end_time' => $this->input('end_time', '18:00'),
            'break_duration' => $this->input('break_duration', 0),
            'is_active' => $this->input('is_active', 1),
            'use_time_blocks' => $useTimeBlocks,
            'working_days' => json_encode($this->input('working_days', [1,2,3,4,5])),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->update('shifts', $data, "id = ? AND tenant_id = ?", [$id, $tenantId]);

        // Si usa bloques de tiempo, actualizarlos
        if ($useTimeBlocks) {
            $timeBlocks = $this->input('time_blocks');
            if ($timeBlocks) {
                $blocksData = json_decode($timeBlocks, true);
                ShiftTimeBlock::deleteByShift($id);
                $this->saveTimeBlocks($id, $blocksData);
            }
        } else {
            // Si cambió a no usar bloques, eliminar los existentes
            ShiftTimeBlock::deleteByShift($id);
        }

        $this->withSuccess('Turno actualizado exitosamente');
        $this->redirect('/turnos');
    }

    public function destroy(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        // Eliminar bloques de tiempo asociados
        ShiftTimeBlock::deleteByShift($id);

        $db->update('shifts', ['deleted_at' => date('Y-m-d H:i:s')], "id = ? AND tenant_id = ?", [$id, $tenantId]);
        $this->withSuccess('Turno eliminado exitosamente');
        $this->redirect('/turnos');
    }

    /**
     * Muestra el formulario para gestionar bloques de tiempo
     */
    public function manageBlocks(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $shift = Shift::find($id);

        if (!$shift || $shift->tenant_id != $tenantId) {
            $this->withError('Turno no encontrado');
            $this->redirect('/turnos');
            return;
        }

        // Si el turno no usa bloques, activarlos automáticamente
        if (!$shift->usesTimeBlocks()) {
            $db = Database::getInstance();
            $db->update('shifts', ['use_time_blocks' => 1], "id = ?", [$id]);
            $shift->use_time_blocks = 1;
        }

        // Obtener bloques agrupados por día
        $blocksGrouped = [];
        $blocks = ShiftTimeBlock::getByShift($id);
        
        foreach ($blocks as $block) {
            $day = $block->day_of_week;
            if (!isset($blocksGrouped[$day])) {
                $blocksGrouped[$day] = [];
            }
            $blocksGrouped[$day][] = [
                'type' => $block->block_type,
                'start' => substr($block->start_time, 0, 5),
                'end' => substr($block->end_time, 0, 5),
                'spansNext' => (bool) ($block->spans_next_day ?? 0),
            ];
        }

        $this->view('shifts/blocks', [
            'title' => 'Gestionar Bloques de Tiempo',
            'shift' => $shift,
            'blocksGrouped' => $blocksGrouped,
        ]);
    }

    /**
     * Guarda los bloques de tiempo
     */
    public function saveBlocks(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $shift = Shift::find($id);

        if (!$shift || $shift->tenant_id != $tenantId) {
            $this->withError('Turno no encontrado');
            $this->redirect('/turnos');
            return;
        }

        $blocksData = $this->input('blocks_data');
        if ($blocksData) {
            $blocks = json_decode($blocksData, true);
            
            // Eliminar bloques existentes
            ShiftTimeBlock::deleteByShift($id);
            
            // Guardar nuevos bloques
            $this->saveTimeBlocks($id, $blocks);
        }

        $this->withSuccess('Bloques de tiempo guardados exitosamente');
        $this->redirect('/turnos');
    }

    /**
     * Método auxiliar para guardar bloques de tiempo
     */
    private function saveTimeBlocks(int $shiftId, array $blocksData): void
    {
        $db = Database::getInstance();
        
        foreach ($blocksData as $day => $blocks) {
            if (empty($blocks)) continue;
            
            $orderIndex = 0;
            foreach ($blocks as $block) {
                if (empty($block['start']) || empty($block['end'])) continue;
                
                $data = [
                    'shift_id' => $shiftId,
                    'day_of_week' => (int) $day,
                    'start_time' => $block['start'] . ':00',
                    'end_time' => $block['end'] . ':00',
                    'spans_next_day' => !empty($block['spansNext']) ? 1 : 0,
                    'block_type' => $block['type'] ?? 'work',
                    'order_index' => $orderIndex++,
                ];
                
                $db->insert('shift_time_blocks', $data);
            }
        }
    }
}
