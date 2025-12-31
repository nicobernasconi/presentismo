<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\User;

/**
 * Controlador de Autenticación
 */
class AuthController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth.login', [
            'title' => 'Iniciar Sesión',
        ]);
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegister(): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth.register', [
            'title' => 'Crear Cuenta',
        ]);
    }

    /**
     * Procesa el login
     */
    public function login(): void
    {
        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember') ? true : false;

        // Validar campos
        $errors = $this->validate([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            $this->withErrors($errors)
                 ->withOld(['email' => $email]);
            $this->redirect('/login');
            return;
        }

        // Intentar autenticación
        if (!Auth::attempt($email, $password)) {
            $this->withError('Credenciales incorrectas')
                 ->withOld(['email' => $email]);
            $this->redirect('/login');
            return;
        }

        // Regenerar ID de sesión por seguridad (sin destruir la anterior)
        session_regenerate_id();

        // Redirigir a URL destino o dashboard
        $intended = Session::flash('intended_url', '/dashboard');
        $this->redirect($intended);
    }

    /**
     * Procesa el registro de nuevos usuarios
     */
    public function register(): void
    {
        $email = $this->input('email');
        $name = $this->input('name');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');

        // Validar campos
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'El nombre es requerido';
        }

        if (empty($email)) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        }

        if (empty($password)) {
            $errors['password'] = 'La contraseña es requerida';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden';
        }

        if (!empty($errors)) {
            $this->withErrors($errors)
                 ->withOld(['email' => $email, 'name' => $name]);
            $this->redirect('/register');
            return;
        }

        // Verificar si el email ya existe
        $db = \Core\Database::getInstance();
        $existingUser = $db->fetch(
            "SELECT id FROM users WHERE email = ? AND deleted_at IS NULL",
            [$email]
        );

        if ($existingUser) {
            $this->withError('Este email ya está registrado')
                 ->withOld(['email' => $email, 'name' => $name]);
            $this->redirect('/register');
            return;
        }

        // Crear usuario (sin tenant_id, será asignado después si es necesario)
        // Por defecto, role_id = 0 (empleado)
        try {
            $db->insert('users', [
                'tenant_id' => null, // Sin empresa asignada
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role_id' => 0, // Empleado por defecto
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->withSuccess('Cuenta creada exitosamente. Por favor, inicia sesión.');
            $this->redirect('/login');
        } catch (\Exception $e) {
            $this->withError('Error al crear la cuenta: ' . $e->getMessage())
                 ->withOld(['email' => $email, 'name' => $name]);
            $this->redirect('/register');
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout(): void
    {
        Auth::logout();
        session_regenerate_id(true);
        
        $this->withSuccess('Sesión cerrada correctamente');
        $this->redirect('/login');
    }

    /**
     * Muestra el formulario de registro de empresa
     */
    public function showRegisterCompany(): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth.register_company', [
            'title' => 'Registrar Empresa',
        ]);
    }

    /**
     * Procesa el registro de empresa con superadmin
     */
    public function registerCompany(): void
    {
        $db = \Core\Database::getInstance();
        
        // Datos del plan
        $plan = $this->input('plan') ?: 'professional';
        
        // Datos de la empresa
        $companyName = trim($this->input('company_name'));
        $taxId = trim($this->input('tax_id'));
        $companyEmail = trim($this->input('company_email'));
        $companyPhone = trim($this->input('company_phone'));
        $address = trim($this->input('address'));
        $city = trim($this->input('city'));
        $postalCode = trim($this->input('postal_code'));
        $country = trim($this->input('country')) ?: 'ES';
        
        // Datos del administrador
        $adminName = trim($this->input('admin_name'));
        $adminEmail = trim($this->input('admin_email'));
        $adminPassword = $this->input('admin_password');
        $adminPasswordConfirm = $this->input('admin_password_confirm');
        
        // Validaciones
        $errors = [];
        $oldData = [
            'plan' => $plan,
            'company_name' => $companyName,
            'tax_id' => $taxId,
            'company_email' => $companyEmail,
            'company_phone' => $companyPhone,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postalCode,
            'country' => $country,
            'admin_name' => $adminName,
            'admin_email' => $adminEmail,
        ];

        // Validar datos de empresa
        if (empty($companyName)) {
            $errors['company_name'] = 'El nombre de la empresa es requerido';
        }
        
        if (empty($taxId)) {
            $errors['tax_id'] = 'El CIF/NIF es requerido';
        }
        
        if (empty($companyEmail)) {
            $errors['company_email'] = 'El email de la empresa es requerido';
        } elseif (!filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['company_email'] = 'El email de la empresa no es válido';
        }

        // Validar datos del admin
        if (empty($adminName)) {
            $errors['admin_name'] = 'El nombre del administrador es requerido';
        }
        
        if (empty($adminEmail)) {
            $errors['admin_email'] = 'El email del administrador es requerido';
        } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['admin_email'] = 'El email del administrador no es válido';
        }
        
        if (empty($adminPassword)) {
            $errors['admin_password'] = 'La contraseña es requerida';
        } elseif (strlen($adminPassword) < 8) {
            $errors['admin_password'] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if ($adminPassword !== $adminPasswordConfirm) {
            $errors['admin_password_confirm'] = 'Las contraseñas no coinciden';
        }

        if (!empty($errors)) {
            $this->withErrors($errors)->withOld($oldData);
            $this->redirect('/register-company');
            return;
        }

        // Verificar si el email del admin ya existe
        $existingUser = $db->fetch(
            "SELECT id FROM users WHERE email = ? AND deleted_at IS NULL",
            [$adminEmail]
        );

        if ($existingUser) {
            $this->withError('Este email ya está registrado')
                 ->withOld($oldData);
            $this->redirect('/register-company');
            return;
        }

        // Verificar si el CIF/NIF ya existe
        $existingTenant = $db->fetch(
            "SELECT id FROM tenants WHERE tax_id = ? AND deleted_at IS NULL",
            [$taxId]
        );

        if ($existingTenant) {
            $this->withError('Ya existe una empresa con este CIF/NIF')
                 ->withOld($oldData);
            $this->redirect('/register-company');
            return;
        }

        // Crear empresa y usuario en transacción
        try {
            $db->getConnection()->beginTransaction();

            // Generar slug único para la empresa
            $slug = $this->generateSlug($companyName);
            
            // Calcular fecha de fin de suscripción (14 días de prueba)
            $subscriptionEndsAt = date('Y-m-d H:i:s', strtotime('+14 days'));

            // Crear la empresa (tenant)
            $tenantId = $db->insert('tenants', [
                'name' => $companyName,
                'slug' => $slug,
                'tax_id' => $taxId,
                'email' => $companyEmail,
                'phone' => $companyPhone,
                'address' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'country' => $country,
                'subscription_plan' => $plan,
                'subscription_ends_at' => $subscriptionEndsAt,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Crear el superadmin de la empresa
            $db->insert('users', [
                'tenant_id' => $tenantId,
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => password_hash($adminPassword, PASSWORD_BCRYPT),
                'role_id' => 1, // Superadmin de empresa
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->getConnection()->commit();

            $this->withSuccess('¡Empresa creada exitosamente! Ya puedes iniciar sesión con tu cuenta de administrador.');
            $this->redirect('/login');

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            $this->withError('Error al crear la empresa: ' . $e->getMessage())
                 ->withOld($oldData);
            $this->redirect('/register-company');
        }
    }

    /**
     * Genera un slug único para la empresa
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar unicidad
        $db = \Core\Database::getInstance();
        $baseSlug = $slug;
        $counter = 1;
        
        while ($db->fetch("SELECT id FROM tenants WHERE slug = ?", [$slug])) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
