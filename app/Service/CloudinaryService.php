<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        try {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key'    => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Error al inicializar Cloudinary: ' . $e->getMessage());
            throw new Exception('No se pudo conectar con Cloudinary');
        }
    }

    /**
     * Subir imagen a Cloudinary
     *
     * @param mixed $file Archivo a subir
     * @param string $folder Carpeta destino
     * @return array|null
     */
    public function upload($file, $folder = 'productos')
    {
        try {
            // Validar que el archivo existe
            if (!$file || !$file->isValid()) {
                throw new Exception('Archivo inválido');
            }

            // Validar tamaño (max 5MB)
            if ($file->getSize() > 5242880) {
                throw new Exception('El archivo excede el tamaño máximo de 5MB');
            }

            // Validar tipo de archivo
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                throw new Exception('Tipo de archivo no permitido');
            }

            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'allowed_formats' => ['jpg', 'png', 'gif', 'webp'],
                ]
            );

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'],
                'width' => $result['width'],
                'height' => $result['height'],
            ];

        } catch (Exception $e) {
            Log::error('Error al subir imagen a Cloudinary: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar imagen de Cloudinary
     *
     * @param string $publicId ID público de la imagen
     * @return array
     */
    public function deleteImage($publicId)
    {
        try {
            if (empty($publicId)) {
                throw new Exception('Public ID no puede estar vacío');
            }

            $result = $this->cloudinary->uploadApi()->destroy($publicId);

            return [
                'success' => $result['result'] === 'ok',
                'message' => $result['result'] === 'ok' ? 'Imagen eliminada' : 'No se pudo eliminar'
            ];

        } catch (Exception $e) {
            Log::error('Error al eliminar imagen de Cloudinary: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener URL optimizada de imagen
     *
     * @param string $publicId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getOptimizedUrl($publicId, $width = 400, $height = 400)
    {
        try {
            return $this->cloudinary->image($publicId)
                ->resize(Resize::fill($width, $height))
                ->toUrl();
        } catch (Exception $e) {
            Log::error('Error al obtener URL optimizada: ' . $e->getMessage());
            return null;
        }
    }
}
