<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Centraliza la subida y el borrado de imágenes de admin (productos, combos,
 * logo).
 *
 * Cuando la librería intervention/image (ver composer.json) está instalada
 * en vendor/, comprime cada imagen y la convierte a WebP antes de
 * guardarla. Mientras no esté instalada, hace un fallback transparente y
 * guarda el archivo tal cual llegó (igual que antes de este cambio), para
 * que la subida de imágenes del admin nunca quede rota mientras se termina
 * de instalar la librería (requiere la extensión GD o Imagick de PHP).
 */
class ImageUploadService
{
    protected ?object $manager = null;

    /**
     * Comprime la imagen subida (redimensiona si excede el ancho máximo y
     * la convierte a WebP) y la guarda en el disco indicado. Si
     * intervention/image todavía no está instalado, guarda el archivo
     * original sin procesar.
     *
     * @return string Ruta relativa dentro del disco (para guardar en BD).
     */
    public function store(
        UploadedFile $file,
        string $folder,
        ?string $disk = null,
        int $maxWidth = 1600,
        int $quality = 75,
    ): string {
        $disk ??= config('filesystems.uploads_disk', 'public');
        $folder = trim($folder, '/');

        if (! class_exists(\Intervention\Image\ImageManager::class)) {
            return $file->storeAs($folder, (string) Str::uuid() . '.' . $file->extension(), $disk);
        }

        $image = $this->manager()->read($file->getRealPath());

        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $encoded = $image->toWebp($quality);

        $path = $folder . '/' . (string) Str::uuid() . '.webp';

        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Borra el archivo del disco indicado si la ruta no viene vacía.
     */
    public function delete(?string $path, ?string $disk = null): void
    {
        if (! empty($path)) {
            Storage::disk($disk ?? config('filesystems.uploads_disk', 'public'))->delete($path);
        }
    }

    protected function manager(): \Intervention\Image\ImageManager
    {
        if ($this->manager === null) {
            $this->manager = extension_loaded('imagick')
                ? \Intervention\Image\ImageManager::imagick()
                : \Intervention\Image\ImageManager::gd();
        }

        return $this->manager;
    }
}
