<?php

namespace App\Models;

class DailyReport
{
    public ?int $id;
    public int $project_id;
    public int $author_id;
    public string $report_date;
    public string $content;
    public bool $incidents_flag;
    public ?string $created_at;

    // Propiedades extendidas
    public ?string $author_name = null;
    public ?string $author_role = null;
    
    // Relación: Un reporte tiene muchas imágenes
    /** @var ReportImage[] */
    public array $images = [];

    public function __construct(
        ?int $id,
        int $project_id,
        int $author_id,
        string $content,
        bool $incidents_flag = false,
        ?string $report_date = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->project_id = $project_id;
        $this->author_id = $author_id;
        $this->content = $content;
        $this->incidents_flag = $incidents_flag;
        $this->report_date = $report_date ?? date('Y-m-d');
        $this->created_at = $created_at;
    }

    public static function fromArray(array $data): self
    {
        $report = new self(
            $data['id'] ?? null,
            (int) $data['project_id'],
            (int) $data['author_id'],
            $data['content'],
            (bool) ($data['incidents_flag'] ?? false),
            $data['report_date'] ?? null,
            $data['created_at'] ?? null
        );

        if (isset($data['author_name'])) {
            $report->author_name = $data['author_name'];
            $report->author_role = $data['author_role'] ?? 'Usuario';
        }

        return $report;
    }
}