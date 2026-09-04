<?php
// ============================================================
// DEGRA STORE — uploads + CSRF (compartilhado entre admin sections)
// ============================================================

/**
 * Faz upload seguro de imagem para /uploads/<subdir>/.
 * - Valida MIME real via finfo
 * - Limite de 6 MB
 * - Remove imagem antiga se ela também era um upload local
 *
 * @return string|null Caminho web (ex.: /uploads/products/abc.jpg) ou null se nenhum arquivo enviado / falha.
 */
function degra_upload_image(string $subdir, ?string $current = null): ?string {
    if (empty($_FILES['image_file']) || ($_FILES['image_file']['error'] ?? 4) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES['image_file'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash'] = 'Falha no upload da imagem (código ' . (int)$f['error'] . ').';
        return null;
    }
    if ($f['size'] > 6 * 1024 * 1024) {
        $_SESSION['flash'] = 'Imagem muito grande (máx. 6MB).';
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($f['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    if (!isset($allowed[$mime])) {
        $_SESSION['flash'] = 'Formato não suportado (use JPG, PNG, WEBP ou GIF).';
        return null;
    }
    $ext = $allowed[$mime];
    $baseDir = dirname(__DIR__) . '/uploads/' . $subdir;
    if (!is_dir($baseDir)) @mkdir($baseDir, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = $baseDir . '/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        $_SESSION['flash'] = 'Não foi possível salvar a imagem no servidor.';
        return null;
    }
    if ($current && strpos($current, '/uploads/' . $subdir . '/') === 0) {
        $old = dirname(__DIR__) . $current;
        if (is_file($old)) @unlink($old);
    }
    return '/uploads/' . $subdir . '/' . $name;
}

function degra_store_uploaded_image(array $f, string $subdir): ?string {
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $_SESSION['flash'] = 'Falha no upload da imagem (código ' . (int)$f['error'] . ').';
        return null;
    }
    if (($f['size'] ?? 0) > 6 * 1024 * 1024) {
        $_SESSION['flash'] = 'Imagem muito grande (máx. 6MB).';
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($f['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    if (!isset($allowed[$mime])) {
        $_SESSION['flash'] = 'Formato não suportado (use JPG, PNG, WEBP ou GIF).';
        return null;
    }
    $baseDir = dirname(__DIR__) . '/uploads/' . $subdir;
    if (!is_dir($baseDir)) @mkdir($baseDir, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $dest = $baseDir . '/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        $_SESSION['flash'] = 'Não foi possível salvar a imagem no servidor.';
        return null;
    }
    return '/uploads/' . $subdir . '/' . $name;
}

function degra_upload_images(string $field, string $subdir, int $limit = 5): array {
    if (empty($_FILES[$field]) || empty($_FILES[$field]['name'])) return [];
    $files = $_FILES[$field];
    $paths = [];
    $total = is_array($files['name']) ? count($files['name']) : 0;
    for ($i = 0; $i < $total && count($paths) < $limit; $i++) {
        $path = degra_store_uploaded_image([
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ], $subdir);
        if ($path) $paths[] = $path;
    }
    return $paths;
}

/* ---------- CSRF ---------- */

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Valida token CSRF em requisições POST. Aborta com 403 se inválido.
 * Chame no topo das seções admin que processam POST.
 */
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    $sent = $_POST['_csrf'] ?? '';
    if (!$sent || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(403);
        die('Sessão expirada ou requisição inválida. <a href="javascript:history.back()">Voltar</a>.');
    }
}
