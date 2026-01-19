<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Exception;
use App\Core\Logger;

class ImageUploader
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        // Inicializa la configuración de Cloudinary desde el entorno
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Sube una imagen al servidor de Cloudinary.
     * @return array|null Retorna datos de la imagen o null si falla.
     */
    public function upload(string $filePath, string $folder = 'nexus_erp_uploads'): ?array
    {
        try {
            if (!file_exists($filePath)) {
                Logger::error("Intento de subida fallido: Archivo no encontrado", ['path' => $filePath]);
                return null;
            }

            $result = $this->cloudinary->uploadApi()->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'image',
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]);

            return [
                'url'       => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format'    => $result['format']
            ];

        } catch (Exception $e) {
            // Registra el error técnico pero permite que la app continúe
            Logger::error("Cloudinary Upload Error", [
                'message' => $e->getMessage(),
                'file_path' => $filePath
            ]);
            return null;
        }
    }

    /**
     * Elimina una imagen de Cloudinary mediante su Public ID.
     */
    public function delete(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return ($result['result'] === 'ok');
        } catch (Exception $e) {
            Logger::error("Cloudinary Delete Error", [
                'message' => $e->getMessage(),
                'public_id' => $publicId
            ]);
            return false;
        }
    }
}