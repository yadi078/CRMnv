<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Deduce el estado de prospecto (status_color) desde texto o color de relleno de celdas Excel.
 * En muchas plantillas "Seguimiento" va con fondo mostaza/amarillo; en el CRM ese estado es el verde "seguimiento".
 */
class SpreadsheetProspectStatusResolver
{
    /**
     * @param  callable(array, array): mixed  $getValue
     */
    public static function resolve(
        Worksheet $sheet,
        int $excelRowNumber,
        array $row,
        array $normalizedHeaders,
        callable $getValue
    ): ?string {
        $textCandidates = [
            ['estado de prospecto'],
            ['estado prospecto'],
            ['estado del prospecto'],
            ['estado del contacto'],
            ['semaforo', 'semáforo', 'semaforo de prospecto'],
        ];

        foreach ($textCandidates as $candidates) {
            $raw = $getValue($row, $candidates);
            if ($raw !== null && trim((string) $raw) !== '') {
                $fromText = self::statusFromText((string) $raw);
                if ($fromText !== null) {
                    return $fromText;
                }
            }
        }

        $fillColumnGroups = [
            ['estado de prospecto', 'estado prospecto', 'estado del prospecto', 'estado del contacto', 'semaforo', 'semáforo'],
            ['nombre completo', 'nombre contacto', 'nombre del contacto'],
            ['nombre de empresa', 'nombre empresa', 'nombre de la empresa', 'empresa', 'nombre comercial'],
            ['area de trabajo', 'área de trabajo', 'area trabajo'],
        ];

        foreach ($fillColumnGroups as $candidates) {
            $col = self::columnLetterForCandidates($normalizedHeaders, $candidates);
            if ($col === null) {
                continue;
            }
            $rgb = self::rgbFromCoordinate($sheet, $col.$excelRowNumber);
            $fromColor = self::mapRgbToStatus($rgb);
            if ($fromColor !== null) {
                return $fromColor;
            }
        }

        return null;
    }

    public static function statusFromText(string $raw): ?string
    {
        $t = Str::lower(trim($raw));
        $t = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'u', 'n'], $t);
        $t = preg_replace('/\s+/u', ' ', $t) ?? $t;

        if ($t === '') {
            return null;
        }

        if (str_contains($t, 'si le interesa') || str_contains($t, 'nos llaman') || str_contains($t, 'no compro') || str_contains($t, 'no compró')) {
            return 'si_le_interesa_nos_llaman_o_no_compro';
        }
        if (str_contains($t, 'no estaba')) {
            return 'no_estaba';
        }
        if (str_contains($t, 'vendido')) {
            return 'vendido';
        }
        if (str_contains($t, 'interesado')) {
            return 'interesado';
        }
        if (str_contains($t, 'seguimiento')) {
            return 'seguimiento';
        }

        foreach (Company::PROSPECT_STATUS_LABELS as $key => $label) {
            if ($t === Str::lower(str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $label))) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    public static function rgbFromCoordinate(Worksheet $sheet, string $coordinate): ?array
    {
        $fill = $sheet->getStyle($coordinate)->getFill();
        $type = $fill->getFillType();
        if ($type === Fill::FILL_NONE || $type === null || $type === '') {
            return null;
        }

        $argb = $fill->getStartColor()->getARGB();
        if ($argb === null || $argb === '' || strlen($argb) < 8) {
            return null;
        }

        $r = hexdec(substr($argb, 2, 2));
        $g = hexdec(substr($argb, 4, 2));
        $b = hexdec(substr($argb, 6, 2));

        if ($r > 250 && $g > 250 && $b > 250) {
            return null;
        }

        return [(int) $r, (int) $g, (int) $b];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}|null  $rgb
     */
    public static function mapRgbToStatus(?array $rgb): ?string
    {
        if ($rgb === null) {
            return null;
        }

        [$r, $g, $b] = $rgb;

        if ($b > 130 && $b >= $r - 20 && $b >= $g - 20 && ($b > $r || $b > $g)) {
            return 'si_le_interesa_nos_llaman_o_no_compro';
        }

        if ($r > 130 && $b > 130 && $g < min($r, $b) + 50 && ($r + $b) > $g * 1.8) {
            return 'no_estaba';
        }

        if ($g > $r + 28 && $g > $b + 28) {
            return 'seguimiento';
        }

        if ($r > 200 && $g > 160 && $b < 150) {
            return 'seguimiento';
        }

        if ($r > 210 && $g > 210 && $b < 120) {
            return 'seguimiento';
        }

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $chroma = $max - $min;

        if ($chroma < 28) {
            return null;
        }

        if ($r > 230 && $g > 210 && $b > 115 && $b > 90 && ($r - $g) < 45) {
            return 'vendido';
        }

        if ($r > 170 && $r > $g + 35 && $r > $b + 35) {
            return 'interesado';
        }

        return null;
    }

    /**
     * @param  array<string, string>  $normalizedHeaders
     */
    private static function columnLetterForCandidates(array $normalizedHeaders, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $key = Str::of($candidate)->lower()->trim()->replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'])->toString();
            if (isset($normalizedHeaders[$key])) {
                return $normalizedHeaders[$key];
            }
        }

        return null;
    }
}
