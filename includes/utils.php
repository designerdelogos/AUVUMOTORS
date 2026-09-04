<?php
// ============================================================
// DEGRA STORE — utilitários puros (sem dependência de DB)
// ============================================================

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string {
    $base = SITE_URL;
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        // Se estivermos em /admin, subir um nível
        if (str_ends_with($dir, '/admin')) $dir = substr($dir, 0, -6);
        $base = $scheme . '://' . $host . $dir;
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function money(float $v): string {
    return 'R$ ' . number_format($v, 2, ',', '.');
}

function discount_pct(?float $prev, float $now): int {
    if (!$prev || $prev <= $now) return 0;
    return (int) round((($prev - $now) / $prev) * 100);
}

function slugify(string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
    $s = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $s));
    return trim($s, '-');
}

function uid(): string {
    return substr(bin2hex(random_bytes(6)), 0, 10);
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function device(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/mobile|iphone|android/i', $ua)) return 'mobile';
    if (preg_match('/ipad|tablet/i', $ua)) return 'tablet';
    return 'desktop';
}
