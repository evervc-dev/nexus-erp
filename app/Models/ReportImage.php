<?php

namespace App\Models;

class ReportImage
{
    public ?int $id;
    public int $report_id;
    public string $image_url;
    public ?string $public_id;
    public ?string $uploaded_at;

    public function __construct(?int $id, int $report_id, string $image_url, ?string $public_id, ?string $uploaded_at = null)
    {
        $this->id = $id;
        $this->report_id = $report_id;
        $this->image_url = $image_url;
        $this->public_id = $public_id;
        $this->uploaded_at = $uploaded_at;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) $data['report_id'],
            $data['image_url'],
            $data['public_id'] ?? null,
            $data['uploaded_at'] ?? null
        );
    }
}