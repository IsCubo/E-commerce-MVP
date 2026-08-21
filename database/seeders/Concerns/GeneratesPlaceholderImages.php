<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Generates simple, on-brand SVG placeholder images for seeded products
 * and combos, so a fresh install has a fully populated catalog without
 * needing to download or bundle real product photos (no network access
 * or external assets required at seed time).
 */
trait GeneratesPlaceholderImages
{
    /**
     * Build a placeholder product image as an inline SVG string.
     */
    protected function placeholderImage(string $title, string $bgColor, string $accent = '#C5A059', string $textColor = '#1A1A1A'): string
    {
        $lines = $this->wrapLabel($title);
        $lineHeight = 44;
        $startY = 560 - (count($lines) - 1) * ($lineHeight / 2);

        $tspans = '';
        foreach ($lines as $i => $line) {
            $y = (int) round($startY + $i * $lineHeight);
            $escaped = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
            $tspans .= "<tspan x=\"300\" y=\"{$y}\">{$escaped}</tspan>";
        }

        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800" width="600" height="800" role="img" aria-label="{$escapedTitle}">
    <rect width="600" height="800" fill="{$bgColor}"/>
    <circle cx="300" cy="300" r="100" fill="{$accent}" fill-opacity="0.22"/>
    <circle cx="300" cy="300" r="60" fill="{$accent}" fill-opacity="0.4"/>
    <text text-anchor="middle" font-family="Georgia, 'Times New Roman', serif" font-size="34" font-weight="700" fill="{$textColor}">{$tspans}</text>
</svg>
SVG;
    }

    /**
     * Word-wrap a label into lines of at most $maxChars characters,
     * since SVG <text> does not wrap on its own.
     */
    protected function wrapLabel(string $text, int $maxChars = 20): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current . ' ' . $word);
            if ($current !== '' && mb_strlen($candidate) > $maxChars) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines ?: [$text];
    }

    /**
     * Borra del disco los archivos de $folder que ya no están referenciados
     * por ningún registro de la BD (p. ej. el placeholder de un producto
     * que fue renombrado, quitado del array de seed, o eliminado), para que
     * re-sembrar el catálogo no deje imágenes huérfanas acumulándose en
     * storage/app/public.
     *
     * $keepPaths debe traer las rutas (relativas al disco) que SÍ siguen
     * en uso — típicamente Product::pluck('image_path') o similar — para
     * no tocar archivos de imágenes reales subidas por un admin.
     */
    protected function pruneOrphanedImages(string $folder, array $keepPaths, string $disk = 'public'): void
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($folder)) {
            return;
        }

        $orphans = array_diff($storage->files($folder), $keepPaths);

        if ($orphans !== []) {
            $storage->delete($orphans);
        }
    }
}
