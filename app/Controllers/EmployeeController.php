<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\EmployeeRepository;
use App\Models\Employee;

class EmployeeController extends Controller
{
    private EmployeeRepository $employeeRepo;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->employeeRepo = new EmployeeRepository($db);
    }

    /**
     * Listado de empleados
     */
    public function index(): void
    {
        $employees = $this->employeeRepo->getAll();

        $this->view('employees/index', [
            'title' => 'Gestión de Personal',
            'employees' => $employees
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(): void
    {
        $this->view('employees/create', [
            'title' => 'Registrar Nuevo Empleado'
        ]);
    }

    /**
     * Guardar empleado en BD
     */
    public function store(): void
    {
        // Recoge los datos
        $data = $this->request->getBody();

        // Validaciones básicas
        if (empty($data['first_name']) || empty($data['position']) || empty($data['daily_salary'])) {
            // Manejo de error simple por ahora
            $this->view('employees/create', ['error' => 'Nombre, Cargo y Salario son obligatorios.']);
            return;
        }

        // Crea la Entidad
        $employee = new Employee(
            null,
            $data['first_name'],
            $data['last_name'],
            $data['dui'],
            $data['position'],
            $data['phone'],
            (float) $data['daily_salary']
        );

        // Guarda en BD
        $this->employeeRepo->save($employee);

        // Redirige al listado
        $this->redirect('/employees');
    }
}