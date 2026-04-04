<?php

namespace App\Support;

class CrmNavigation
{
    /**
     * Valida que la URL sea del mismo sitio (evita redirección abierta).
     */
    public static function isSafeReturnUrl(?string $url): bool
    {
        if ($url === null || $url === '') {
            return false;
        }

        if (str_starts_with($url, '//')) {
            return false;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        if (! empty($parsed['scheme'])) {
            if (! in_array(strtolower($parsed['scheme']), ['http', 'https'], true)) {
                return false;
            }
            if (empty($parsed['host'])) {
                return false;
            }
            $configHost = parse_url((string) config('app.url'), PHP_URL_HOST);

            return $parsed['host'] === $configHost
                || $parsed['host'] === request()->getHost();
        }

        // Ruta relativa del mismo sitio (p. ej. /CRMnv/public/ejecutivos) cuando ?return= no trae host.
        if (isset($parsed['path']) && str_starts_with($url, '/')) {
            $basePath = rtrim((string) request()->getBasePath(), '/');
            $pathOnly = (string) $parsed['path'];

            if ($basePath === '') {
                return true;
            }

            return $pathOnly === $basePath || str_starts_with($pathOnly, $basePath.'/');
        }

        return false;
    }

    /**
     * URL preferida para el botón "Volver" (query ?return=).
     */
    public static function preferredBackUrlFromRequest(): ?string
    {
        $candidate = request()->query('return');
        if (! is_string($candidate) || $candidate === '') {
            return null;
        }

        return static::isSafeReturnUrl($candidate) ? $candidate : null;
    }

    /**
     * Añade ?return= o &return= con la página actual para encadenar el flujo de "Volver".
     */
    public static function withReturn(string $targetUrl): string
    {
        // Sin query string en la URL de retorno: evita URIs enormes y errores al abrir Editar/Volver.
        $current = request()->url();
        $separator = str_contains($targetUrl, '?') ? '&' : '?';

        return $targetUrl.$separator.'return='.rawurlencode($current);
    }

    /**
     * Tras guardar un formulario, redirige a la URL ?return= si viene en la petición y es segura.
     */
    public static function redirectTargetFromRequest(\Illuminate\Http\Request $request, string $fallbackUrl): string
    {
        $back = $request->input('return');
        if (is_string($back) && static::isSafeReturnUrl($back)) {
            return $back;
        }

        return $fallbackUrl;
    }
}
