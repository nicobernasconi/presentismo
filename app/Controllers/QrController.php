<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkCenter;

/**
 * Controlador para fichaje mediante QR
 */
class QrController extends Controller
{
    /**
     * Muestra la página de administración de QR (para admins)
     */
    public function index(): void
    {
        $this->setLayout('app');
        $tenantId = Auth::tenantId();
        
        // Obtener centros de trabajo
        $workCenters = WorkCenter::where('tenant_id', $tenantId)->where('is_active', 1)->get();
        
        // Generar token único para el tenant si no existe
        $db = Database::getInstance();
        $tenant = $db->fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        
        if (!$tenant) {
            $this->withError('No se encontró la empresa');
            $this->redirect('/dashboard');
            return;
        }
        
        if (empty($tenant['qr_token'])) {
            $token = bin2hex(random_bytes(32));
            $db->query("UPDATE tenants SET qr_token = ? WHERE id = ?", [$token, $tenantId]);
            $tenant['qr_token'] = $token;
        }
        
        $this->view('qr.index', [
            'title' => 'Fichaje por QR',
            'workCenters' => $workCenters,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Genera el QR para un centro de trabajo específico
     */
    public function generate(): void
    {
        $this->setLayout('app');
        $tenantId = Auth::tenantId();
        $workCenterId = $this->query('work_center_id');
        
        $db = Database::getInstance();
        $tenant = $db->fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        
        if (!$tenant) {
            $this->withError('No se encontró la empresa');
            $this->redirect('/dashboard');
            return;
        }
        
        if (empty($tenant['qr_token'])) {
            $token = bin2hex(random_bytes(32));
            $db->query("UPDATE tenants SET qr_token = ? WHERE id = ?", [$token, $tenantId]);
            $tenant['qr_token'] = $token;
        }
        
        // Construir URL del QR
        $baseUrl = $this->getBaseUrl();
        $qrUrl = $baseUrl . 'fichar/' . $tenant['qr_token'];
        
        if ($workCenterId) {
            $workCenter = WorkCenter::find($workCenterId);
            $qrUrl .= '/' . $workCenterId;
        } else {
            $workCenter = null;
        }
        
        $this->view('qr.generate', [
            'title' => 'Código QR para Fichaje',
            'qrUrl' => $qrUrl,
            'workCenter' => $workCenter,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Página pública de fichaje (accesible vía QR)
     */
    public function scan(string $token, ?string $workCenterId = null): void
    {
        $this->setLayout('guest');
        
        $db = Database::getInstance();
        $tenant = $db->fetch("SELECT * FROM tenants WHERE qr_token = ? AND is_active = 1", [$token]);
        
        if (!$tenant) {
            $this->view('qr.invalid', [
                'title' => 'QR Inválido',
                'message' => 'El código QR no es válido o ha expirado.'
            ]);
            return;
        }
        
        $workCenter = null;
        if ($workCenterId) {
            $workCenter = WorkCenter::find($workCenterId);
        }
        
        $this->view('qr.scan', [
            'title' => 'Fichar - ' . $tenant['name'],
            'tenant' => $tenant,
            'token' => $token,
            'workCenter' => $workCenter,
            'workCenterId' => $workCenterId,
        ]);
    }

    /**
     * Procesa el fichaje desde QR
     */
    public function clockAction(): void
    {
        $token = $this->input('token');
        $email = $this->input('email');
        $pin = $this->input('pin');
        $action = $this->input('action'); // 'in' o 'out'
        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');
        $latValue = is_numeric($latitude) ? (float) $latitude : null;
        $lngValue = is_numeric($longitude) ? (float) $longitude : null;
        $workCenterId = $this->input('work_center_id');
        
        $db = Database::getInstance();
        
        // Validar token
        $tenant = $db->fetch("SELECT * FROM tenants WHERE qr_token = ? AND is_active = 1", [$token]);
        
        if (!$tenant) {
            $this->jsonResponse(['success' => false, 'message' => 'Token inválido']);
            return;
        }
        
        // Buscar usuario por email y PIN
        $user = $db->fetch(
            "SELECT * FROM users WHERE tenant_id = ? AND email = ? AND is_active = 1",
            [$tenant['id'], $email]
        );
        
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'Usuario no encontrado']);
            return;
        }
        
        // Verificar PIN (últimos 4 dígitos del DNI o PIN personalizado)
        $validPin = $user['pin'] ?? substr($user['dni'] ?? '0000', -4);
        if ($pin !== $validPin) {
            $this->jsonResponse(['success' => false, 'message' => 'PIN incorrecto']);
            return;
        }
        
        // Verificar geolocalización si el centro lo requiere
        if ($workCenterId) {
            /** @var WorkCenter|null $workCenter */
            $workCenter = WorkCenter::find($workCenterId);
            if ($workCenter && $workCenter->requires_geolocation && $latValue !== null && $lngValue !== null) {
                if (!$workCenter->isWithinRadius($latValue, $lngValue)) {
                    $distance = $workCenter->calculateDistance($latValue, $lngValue);
                    $this->jsonResponse([
                        'success' => false, 
                        'message' => "Estás a {$distance}m del centro. Máximo permitido: {$workCenter->radius}m"
                    ]);
                    return;
                }
            }
        }
        
        try {
            $data = [
                'tenant_id' => $tenant['id'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'work_center_id' => $workCenterId ?: $user['work_center_id'],
                'source' => 'qr',
                'notes' => 'Fichaje mediante QR',
            ];
            
            if ($action === 'in') {
                // Verificar si ya tiene entrada activa
                /** @var User|null $userModel */
                $userModel = User::find($user['id']);
                $status = $userModel->getCurrentClockStatus();
                
                if ($status['is_clocked_in'] ?? false) {
                    $this->jsonResponse([
                        'success' => false, 
                        'message' => 'Ya tienes una entrada registrada. Debes fichar salida primero.'
                    ]);
                    return;
                }
                
                TimeEntry::clockIn($user['id'], $data);
                $message = '✅ Entrada registrada correctamente';
                $time = date('H:i:s');
            } else {
                // Verificar si tiene entrada para hacer salida
                /** @var User|null $userModel */
                $userModel = User::find($user['id']);
                $status = $userModel->getCurrentClockStatus();
                
                if (!($status['is_clocked_in'] ?? false)) {
                    $this->jsonResponse([
                        'success' => false, 
                        'message' => 'No tienes entrada registrada. Debes fichar entrada primero.'
                    ]);
                    return;
                }
                
                TimeEntry::clockOut($user['id'], $data);
                $message = '✅ Salida registrada correctamente';
                $time = date('H:i:s');
            }
            
            $this->jsonResponse([
                'success' => true, 
                'message' => $message,
                'time' => $time,
                'user' => $user['first_name'] . ' ' . $user['last_name']
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Error al registrar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene la URL base
     */
    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        
        return $protocol . '://' . $host . $scriptDir . '/index.php?route=';
    }

    /**
     * Respuesta JSON
     */
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
