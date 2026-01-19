<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Probamos que el renderizado funciona
        $this->view('home/index', [
            'title' => 'Dashboard - Nexus ERP',
            'username' => 'EverVC' // Dato dummy para probar
        ]);
    }
}