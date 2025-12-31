<?php

namespace App\Controllers;

use Core\Controller;

class LegalController extends Controller
{
    public function privacy(): void
    {
        $this->setLayout('guest');
        $this->view('legal.privacy', [
            'title' => 'Política de Privacidad',
        ]);
    }

    public function cookies(): void
    {
        $this->setLayout('guest');
        $this->view('legal.cookies', [
            'title' => 'Política de Cookies',
        ]);
    }

    public function terms(): void
    {
        $this->setLayout('guest');
        $this->view('legal.terms', [
            'title' => 'Términos y Condiciones',
        ]);
    }
}
