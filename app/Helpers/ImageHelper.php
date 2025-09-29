<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * Comprimir una imagen y devolver la ruta donde se guardó
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @return string|null
     */
    public static function compressAndStoreImage(UploadedFile $file, string $directory, string $filename): ?string
    {
        try {
            // Verificar que sea una imagen
            $mimeType = $file->getMimeType();
            if (!str_starts_with($mimeType, 'image/')) {
                Log::warning('El archivo subido no es una imagen: ' . $mimeType);
                return null;
            }

            // Crear la imagen usando GD
            $image = null;
            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getPathname());
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file->getPathname());
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($file->getPathname());
                    break;
                default:
                    Log::warning('Tipo de imagen no soportado: ' . $mimeType);
                    return null;
            }

            if (!$image) {
                Log::error('No se pudo crear la imagen desde el archivo');
                return null;
            }

            // Obtener dimensiones originales
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calcular nuevas dimensiones (máximo 1200px en el lado más largo)
            $maxDimension = 1200;
            if ($originalWidth > $originalHeight) {
                $newWidth = min($originalWidth, $maxDimension);
                $newHeight = ($originalHeight * $newWidth) / $originalWidth;
            } else {
                $newHeight = min($originalHeight, $maxDimension);
                $newWidth = ($originalWidth * $newHeight) / $originalHeight;
            }

            // Crear nueva imagen con las dimensiones calculadas
            $compressedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($compressedImage, false);
                imagesavealpha($compressedImage, true);
            }

            // Redimensionar la imagen
            imagecopyresampled(
                $compressedImage,
                $image,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // Crear directorio si no existe
            $fullPath = storage_path('app/public/' . $directory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            // Generar nombre de archivo único
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $extension = 'jpg'; // fallback
            }

            $compressedFilename = $filename . '_compressed.' . $extension;
            $compressedPath = $fullPath . '/' . $compressedFilename;

            // Guardar la imagen comprimida
            $quality = 85; // 85% de calidad
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($compressedImage, $compressedPath, $quality);
                    break;
                case 'image/png':
                    imagepng($compressedImage, $compressedPath, 8); // Nivel de compresión 0-9
                    break;
                case 'image/gif':
                    imagegif($compressedImage, $compressedPath);
                    break;
                case 'image/webp':
                    imagewebp($compressedImage, $compressedPath, $quality);
                    break;
            }

            // Liberar memoria
            imagedestroy($image);
            imagedestroy($compressedImage);

            // Devolver la ruta relativa para almacenar en la base de datos
            return 'storage/' . $directory . '/' . $compressedFilename;

        } catch (\Exception $e) {
            Log::error('Error al comprimir imagen: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Comprimir una imagen existente y reemplazarla
     *
     * @param string $existingPath
     * @return string|null
     */
    public static function recompressExistingImage(string $existingPath): ?string
    {
        try {
            $fullPath = storage_path('app/public/' . $existingPath);

            if (!file_exists($fullPath)) {
                Log::warning('Imagen no encontrada: ' . $fullPath);
                return null;
            }

            // Crear imagen desde el archivo existente
            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                Log::warning('No se pudo obtener información de la imagen: ' . $fullPath);
                return null;
            }

            $mimeType = $imageInfo['mime'];
            $image = null;

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($fullPath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($fullPath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($fullPath);
                    break;
            }

            if (!$image) {
                Log::error('No se pudo crear la imagen desde el archivo existente');
                return null;
            }

            // Obtener dimensiones
            $width = imagesx($image);
            $height = imagesy($image);

            // Si la imagen ya es pequeña, no comprimir más
            if ($width <= 1200 && $height <= 1200) {
                imagedestroy($image);
                return $existingPath;
            }

            // Calcular nuevas dimensiones
            $maxDimension = 1200;
            if ($width > $height) {
                $newWidth = min($width, $maxDimension);
                $newHeight = ($height * $newWidth) / $width;
            } else {
                $newHeight = min($height, $maxDimension);
                $newWidth = ($width * $newHeight) / $height;
            }

            // Crear nueva imagen
            $compressedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preservar transparencia para PNG
            if ($mimeType === 'image/png') {
                imagealphablending($compressedImage, false);
                imagesavealpha($compressedImage, true);
            }

            // Redimensionar
            imagecopyresampled(
                $compressedImage,
                $image,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            // Guardar la imagen comprimida sobre la original
            $quality = 85;
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($compressedImage, $fullPath, $quality);
                    break;
                case 'image/png':
                    imagepng($compressedImage, $fullPath, 8);
                    break;
                case 'image/gif':
                    imagegif($compressedImage, $fullPath);
                    break;
                case 'image/webp':
                    imagewebp($compressedImage, $fullPath, $quality);
                    break;
            }

            // Liberar memoria
            imagedestroy($image);
            imagedestroy($compressedImage);

            return $existingPath;

        } catch (\Exception $e) {
            Log::error('Error al recomprimir imagen existente: ' . $e->getMessage());
            return null;
        }
    }
}
