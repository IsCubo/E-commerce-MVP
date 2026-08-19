<?php

namespace Database\Seeders\Concerns;

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
}
