<?php
// ============================================================
// DEGRA STORE — camada de dados
// Acessores de banco com cache de request para reduzir queries repetidas.
// Depende de: db.php (função q()), fallbacks.php, utils.php
// ============================================================

/* ---------- settings ---------- */

function get_setting(string $key, ?string $default = null): ?string {
    $r = q('SELECT value FROM settings WHERE `key`=?', [$key])->fetch();
    return $r ? $r['value'] : $default;
}

function set_setting(string $key, string $value): void {
    q('INSERT INTO settings (`key`,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)', [$key, $value]);
}

/* ---------- categorias (cache de request) ---------- */

function all_categories(bool $onlyActive = true): array {
    static $cache = ['all' => null, 'active' => null];
    $k = $onlyActive ? 'active' : 'all';
    if ($cache[$k] !== null) return $cache[$k];
    $sql = 'SELECT * FROM categories' . ($onlyActive ? ' WHERE active=1' : '') . ' ORDER BY `order` ASC, name ASC';
    return $cache[$k] = q($sql)->fetchAll();
}

function categories_map(bool $onlyActive = false): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $map = [];
    foreach (all_categories($onlyActive) as $c) $map[$c['id']] = $c;
    return $cache = $map;
}

function find_category(string $id): ?array {
    $map = categories_map(false);
    if (isset($map[$id])) return $map[$id];
    $r = q('SELECT * FROM categories WHERE id=? LIMIT 1', [$id])->fetch();
    return $r ?: null;
}

function find_category_by_slug(string $slug): ?array {
    foreach (categories_map(false) as $c) if ($c['slug'] === $slug) return $c;
    $r = q('SELECT * FROM categories WHERE slug=? LIMIT 1', [$slug])->fetch();
    return $r ?: null;
}

/* ---------- plataformas (cache de request) ---------- */

function all_platforms(bool $onlyActive = true): array {
    static $cache = ['all' => null, 'active' => null];
    $k = $onlyActive ? 'active' : 'all';
    if ($cache[$k] !== null) return $cache[$k];
    $sql = 'SELECT * FROM platforms' . ($onlyActive ? ' WHERE active=1' : '') . ' ORDER BY name ASC';
    return $cache[$k] = q($sql)->fetchAll();
}

function platforms_map(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $map = [];
    foreach (all_platforms(false) as $p) $map[$p['id']] = $p;
    // mescla fallbacks para IDs conhecidos que não estejam no banco
    foreach (fallback_platforms() as $id => $p) if (!isset($map[$id])) $map[$id] = $p;
    return $cache = $map;
}

function find_platform(?string $id): ?array {
    if (!$id) return null;
    $map = platforms_map();
    return $map[$id] ?? fallback_platform($id);
}

/* ---------- banners ---------- */

function all_banners(bool $onlyActive = true): array {
    static $cache = ['all' => null, 'active' => null];
    $k = $onlyActive ? 'active' : 'all';
    if ($cache[$k] !== null) return $cache[$k];
    $sql = 'SELECT * FROM banners' . ($onlyActive ? ' WHERE active=1' : '') . ' ORDER BY id ASC';
    return $cache[$k] = q($sql)->fetchAll();
}

/* ---------- produtos ---------- */

function find_product_by_slug(string $slug): ?array {
    $r = q('SELECT * FROM products WHERE slug=? LIMIT 1', [$slug])->fetch();
    return $r ?: null;
}

function product_images(array $product): array {
    if (empty($product['id'])) return [];
    try {
        $rows = q('SELECT image_url FROM product_images WHERE product_id=? ORDER BY sort_order ASC, id ASC', [$product['id']])->fetchAll();
    } catch (Throwable $e) {
        $rows = [];
    }
    $images = array_values(array_filter(array_map(fn($r) => $r['image_url'] ?? '', $rows)));
    if (!$images && !empty($product['image_url'])) $images[] = $product['image_url'];
    return array_slice($images, 0, 5);
}

function product_primary_image(array $product): string {
    $images = product_images($product);
    return $images[0] ?? ($product['image_url'] ?? '');
}

function products_where(string $extra = '', array $params = [], string $order = 'updated_at DESC'): array {
    $sql = 'SELECT * FROM products WHERE 1=1 ' . $extra . ' ORDER BY ' . $order;
    return q($sql, $params)->fetchAll();
}

/**
 * Helper genérico: executa uma query de produtos e cai no fallback
 * se o resultado vier vazio, aplicando um filtro sobre fallback_products().
 */
function products_or_fallback(string $sql, array $params, ?callable $fallbackFilter = null): array {
    $rows = q($sql, $params)->fetchAll();
    if ($rows) return $rows;
    $fb = fallback_products();
    if ($fallbackFilter) $fb = array_values(array_filter($fb, $fallbackFilter));
    return $fb;
}
