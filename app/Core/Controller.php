<?php

namespace App\Core;

abstract class Controller
{
    protected ?Database $db;
    protected ?Request $request;

    /**
     * El constructor recibe la conexión junto con el request y los deja disponible para los hijos.
     * Son opcionales para que el ErrorController pueda funcionar si la BD se cae.
     * @param Database|null $db
     * @param Request|null $request
     */
    public function __construct(?Database $db = null, ?Request $request = null)
    {
        $this->db = $db;
        $this->request = $request;
    }

    /**
     * Método helper para renderizar vistas más fácil desde los hijos.
     * Ejemplo: $this->view('home', ['title' => 'Inicio']);
     */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    /**
     * Redirección interna
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}