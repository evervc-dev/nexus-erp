<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\DailyReport;
use App\Models\ReportImage;
use PDO;

class ReportRepository
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->getPdo();
    }

    /**
     * Guarda un reporte y sus imágenes en una transacción
     * @param DailyReport $report
     * @param array $uploadedImages Array de datos ['url' => ..., 'public_id' => ...]
     */
    public function create(DailyReport $report, array $uploadedImages): void
    {
        try {
            $this->pdo->beginTransaction();

            // Inserta el reporte (Texto)
            $sql = "INSERT INTO daily_reports (project_id, author_id, content, incidents_flag, report_date) 
                    VALUES (:pid, :uid, :content, :flag, :date) 
                    RETURNING id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'pid' => $report->project_id,
                'uid' => $report->author_id,
                'content' => $report->content,
                'flag' => $report->incidents_flag ? 'true' : 'false',
                'date' => $report->report_date
            ]);

            $reportId = (int) $stmt->fetchColumn();

            // Inserta las imágenes (Loop)
            if (!empty($uploadedImages)) {
                $sqlImg = "INSERT INTO report_images (report_id, image_url, public_id) VALUES (:rid, :url, :pid)";
                $stmtImg = $this->pdo->prepare($sqlImg);

                foreach ($uploadedImages as $img) {
                    $stmtImg->execute([
                        'rid' => $reportId,
                        'url' => $img['url'],
                        'pid' => $img['public_id']
                    ]);
                }
            }

            $this->pdo->commit();

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getByProject(int $projectId): array
    {
        // Obtiene los reportes
        $sql = "SELECT dr.*, CONCAT(u.name, ' ', u.last_name) as author_name, r.name as author_role
                FROM daily_reports dr
                JOIN users u ON dr.author_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE dr.project_id = :pid
                ORDER BY dr.report_date DESC, dr.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $projectId]);
        $reportsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $reports = [];
        
        // Para cada reporte, busca sus imágenes
        $sqlImg = "SELECT * FROM report_images WHERE report_id = :rid";
        $stmtImg = $this->pdo->prepare($sqlImg);

        foreach ($reportsData as $row) {
            $report = DailyReport::fromArray($row);

            // Cargar imágenes
            $stmtImg->execute(['rid' => $report->id]);
            $imagesData = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
            
            $report->images = array_map(fn($img) => ReportImage::fromArray($img), $imagesData);
            
            $reports[] = $report;
        }

        return $reports;
    }

    /**
     * Busca un reporte por ID y carga sus imágenes
     */
    public function findById(int $id): ?DailyReport
    {
        // 1. Datos del reporte
        $sql = "SELECT * FROM daily_reports WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $report = DailyReport::fromArray($row);

        // 2. Cargar imágenes asociadas
        $sqlImg = "SELECT * FROM report_images WHERE report_id = :rid";
        $stmtImg = $this->pdo->prepare($sqlImg);
        $stmtImg->execute(['rid' => $id]);
        $imagesData = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

        $report->images = array_map(fn($img) => ReportImage::fromArray($img), $imagesData);

        return $report;
    }

    public function delete(int $id): void
    {
        // Gracias al ON DELETE CASCADE en la BD, 
        // esto borrará también las filas en 'report_images' automáticamente.
        $sql = "DELETE FROM daily_reports WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}