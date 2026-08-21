<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('upload_url')) {
    /**
     * Resuelve la URL pública de un archivo subido (imagen de producto,
     * combo o logo) usando el disco configurado en filesystems.uploads_disk
     * (local en desarrollo, Cloudflare R2 u otro S3-compatible en
     * producción vía UPLOADS_DISK). Reemplaza los `asset('storage/' . $x)`
     * hardcodeados para que las vistas no dependan de que el disco sea
     * siempre local.
     */
    function upload_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk(config('filesystems.uploads_disk', 'public'))->url($path);
    }
}
