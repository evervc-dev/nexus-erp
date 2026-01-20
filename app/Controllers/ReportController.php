<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Repositories\ReportRepository;
use App\Services\ImageUploader;
use App\Models\DailyReport;

class ReportController extends Controller
{
    private ReportRepository $reportRepo;
    private ImageUploader $uploader;

    public function __construct(Database $db, Request $request)
    {
        parent::__construct($db, $request);
        $this->reportRepo = new ReportRepository($db);
        $this->uploader = new ImageUploader();
    }

    public function store(): void
    {
        $data = $this->request->getBody();
        $projectId = (int) $data['project_id'];

        if (empty($data['content'])) {
            $this->redirect("/projects/view/$projectId?tab=reports&error=empty_content");
            return;
        }

        // Procesa las imágenes (Múltiples)
        $uploadedImagesData = [];
        
        // PHP organiza los archivos múltiples de forma rara (Array de Arrays vs Array de Indices)
        // $_FILES['images']['name'] es un array [0 => 'a.jpg', 1 => 'b.jpg']
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            
            $files = $_FILES['images'];
            $fileCount = count($files['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    
                    // Subir archivo temporal individualmente
                    $tmpName = $files['tmp_name'][$i];
                    $result = $this->uploader->upload($tmpName, 'nexus_reports');

                    if ($result) {
                        $uploadedImagesData[] = [
                            'url' => $result['url'],
                            'public_id' => $result['public_id']
                        ];
                    }
                }
            }
        }

        // Crea el Objeto Reporte
        $report = new DailyReport(
            null,
            $projectId,
            $_SESSION['user_id'],
            $data['content'],
            isset($data['incidents_flag']), // Checkbox envía 'on' si está marcado, nada si no
            date('Y-m-d') // Fecha de hoy
        );

        // Guarda todo
        $this->reportRepo->create($report, $uploadedImagesData);

        $this->redirect("/projects/view/$projectId?tab=reports");
    }

    public function delete(string $id): void
    {
        $reportId = (int) $id;
        
        // Obtiene el reporte para saber qué imágenes borrar
        $report = $this->reportRepo->findById($reportId);

        if (!$report) {
            // Si no existe, regresar (podrías manejar error 404)
            header('Location: /projects');
            return;
        }

        // Verificación de seguridad: ¿Quién puede borrar?
        // - SuperAdmin: Siempre
        // - Ingeniero: Siempre
        // - Autor del reporte: Sí (opcional, dejémoslo que sí por ahora)
        $canDelete = in_array($_SESSION['role_name'], ['SuperAdmin', 'Ingeniero']) || 
                     $report->author_id === $_SESSION['user_id'];

        if (!$canDelete) {
            (new ErrorController())->show(
                403,
                "No tienes permiso para borrar el reporte con ID [$reportId]"
            );
            return;
        }

        // Borrar imágenes de Cloudinary
        foreach ($report->images as $img) {
            if ($img->public_id) {
                $this->uploader->delete($img->public_id);
            }
        }

        // Borrar de la Base de Datos
        $this->reportRepo->delete($reportId);

        // Redirigir al proyecto
        $this->redirect("/projects/view/{$report->project_id}?tab=reports");
    }
}