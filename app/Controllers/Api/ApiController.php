<?php
namespace App\Controllers\Api;

use Core\Database;
use App\Models\User;

/**
 * Controlador base para API REST
 */
abstract class ApiController
{
    protected ?User $user = null;
    protected Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Establece el usuario autenticado
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
    
    /**
     * Obtiene el usuario autenticado
     */
    protected function user(): ?User
    {
        // Si ya está cargado, retornarlo
        if ($this->user) {
            return $this->user;
        }
        
        // Cargar desde sesión (establecido por ApiAuthMiddleware)
        if (isset($_SESSION['api_user'])) {
            $userData = $_SESSION['api_user'];
            
            // Si es un array, hidratar el modelo
            if (is_array($userData)) {
                $this->user = User::find($userData['id']);
            } elseif ($userData instanceof User) {
                $this->user = $userData;
            }
        }
        
        return $this->user;
    }
    
    /**
     * Respuesta JSON exitosa
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Respuesta de éxito
     */
    protected function success(array $data = [], string $message = 'OK', int $status = 200): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    
    /**
     * Respuesta de error
     */
    protected function error(string $message, int $status = 400, array $errors = []): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        $this->json($response, $status);
    }
    
    /**
     * Obtiene datos del body JSON
     */
    protected function input(?string $key = null, $default = null)
    {
        static $data = null;
        
        if ($data === null) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?? [];
            
            // Fallback a POST si no hay JSON
            if (empty($data)) {
                $data = $_POST;
            }
        }
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    /**
     * Valida campos requeridos
     */
    protected function validate(array $rules): array
    {
        $errors = [];
        $data = $this->input();
        
        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field] = "El campo {$field} es requerido";
                }
                
                if (strpos($r, 'min:') === 0 && isset($data[$field])) {
                    $min = (int) substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = "El campo {$field} debe tener al menos {$min} caracteres";
                    }
                }
                
                if ($r === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "El campo {$field} debe ser un email válido";
                }
                
                if ($r === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field] = "El campo {$field} debe ser numérico";
                }
            }
        }
        
        return $errors;
    }
}
