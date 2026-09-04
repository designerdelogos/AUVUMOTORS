<?php
// ============================================================
// AUTO STORE — Popular banco com os mesmos dados do site
// Execute UMA vez em: https://seusite.com.br/seed.php
// Depois APAGUE este arquivo do servidor.
// ============================================================
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/fallbacks.php';

$logs = [];

try {
    $pdo = db();

    // Plataformas
    $stmt = $pdo->prepare('INSERT INTO platforms (id,name,slug,color,active) VALUES (?,?,?,?,1)
        ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug), color=VALUES(color), active=1');
    foreach (fallback_platforms() as $p) {
        $stmt->execute([$p['id'], $p['name'], $p['slug'], $p['color']]);
    }
    $logs[] = '✔ Plataformas inseridas/atualizadas: ' . count(fallback_platforms());

    // Categorias
    $stmt = $pdo->prepare('INSERT INTO categories (id,name,slug,`order`,icon,active) VALUES (?,?,?,?,?,1)
        ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug), `order`=VALUES(`order`), icon=VALUES(icon), active=1');
    foreach (fallback_categories() as $c) {
        $stmt->execute([$c['id'], $c['name'], $c['slug'], $c['order'], $c['icon']]);
    }
    $logs[] = '✔ Categorias inseridas/atualizadas: ' . count(fallback_categories());

    // Banners
    $stmt = $pdo->prepare('INSERT INTO banners (id,image_url,alt,active) VALUES (?,?,?,1)
        ON DUPLICATE KEY UPDATE image_url=VALUES(image_url), alt=VALUES(alt), active=1');
    foreach (fallback_banners() as $b) {
        $stmt->execute([$b['id'], $b['image_url'], $b['alt']]);
    }
    $logs[] = '✔ Banners inseridos/atualizados: ' . count(fallback_banners());

    foreach ([
        'make' => 'VARCHAR(80)',
        'model' => 'VARCHAR(120)',
        'year_model' => 'VARCHAR(20)',
        'mileage' => 'INT NULL',
        'transmission' => 'VARCHAR(40)',
        'fuel' => 'VARCHAR(40)',
        'color' => 'VARCHAR(40)',
    ] as $column => $type) {
        $exists = q('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=?', [DB_NAME, 'products', $column])->fetch();
        if (!$exists) {
            $pdo->exec('ALTER TABLE products ADD COLUMN ' . $column . ' ' . $type);
        }
    }

    // Veículos
    $stmt = $pdo->prepare('INSERT INTO products
        (id,name,slug,short_description,description,price,previous_price,image_url,make,model,year_model,mileage,transmission,fuel,color,category_id,platform_id,affiliate_url,badge,featured,offer,active,clicks)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)
        ON DUPLICATE KEY UPDATE
          name=VALUES(name), short_description=VALUES(short_description), description=VALUES(description),
          price=VALUES(price), previous_price=VALUES(previous_price), image_url=VALUES(image_url),
          make=VALUES(make), model=VALUES(model), year_model=VALUES(year_model), mileage=VALUES(mileage),
          transmission=VALUES(transmission), fuel=VALUES(fuel), color=VALUES(color),
          category_id=VALUES(category_id), platform_id=VALUES(platform_id), affiliate_url=VALUES(affiliate_url),
          badge=VALUES(badge), featured=VALUES(featured), offer=VALUES(offer), active=1');
    $count = 0;
    foreach (fallback_products() as $p) {
        // usa slug como id estável (para não duplicar em re-execuções)
        $id = $p['id'];
        $stmt->execute([
            $id, $p['name'], $p['slug'], $p['short_description'], $p['description'],
            $p['price'], $p['previous_price'], $p['image_url'],
            $p['make'] ?? '', $p['model'] ?? '', $p['year_model'] ?? '', $p['mileage'] ?? null,
            $p['transmission'] ?? '', $p['fuel'] ?? '', $p['color'] ?? '',
            $p['category_id'], $p['platform_id'], $p['affiliate_url'],
            $p['badge'], $p['featured'], $p['offer'], $p['clicks'],
        ]);
        $count++;
    }
    $logs[] = '✔ Veículos inseridos/atualizados: ' . $count;

    if (admin_count() === 0) {
        admin_create(TEMP_ADMIN_NAME, TEMP_ADMIN_EMAIL, TEMP_ADMIN_PASS);
        $logs[] = '✔ Administrador temporário criado: ' . TEMP_ADMIN_EMAIL . ' / ' . TEMP_ADMIN_PASS;
    } else {
        $logs[] = '✔ Administrador existente preservado.';
    }

} catch (Throwable $e) {
    http_response_code(500);
    $logs[] = '✘ ERRO: ' . $e->getMessage();
}
?>
<!doctype html>
<html lang="pt-br"><head><meta charset="utf-8"><title>Seed — Auto Store</title>
<style>body{font-family:system-ui,sans-serif;background:#fff;color:#111;padding:40px;max-width:720px;margin:auto}h1{color:#111}code{background:#f3f3f3;padding:2px 6px;border-radius:4px}a{color:#111}.ok{color:#111}.err{color:#b00020}li{margin:6px 0}</style>
</head><body>
<h1>Popular banco de dados</h1>
<ul>
<?php foreach ($logs as $l): ?><li class="<?= str_contains($l, '✔') ? 'ok' : 'err' ?>"><?= e($l) ?></li><?php endforeach; ?>
</ul>
<p><a href="admin/dashboard.php">→ Abrir painel administrativo</a></p>
<p style="color:#f5a623">⚠ Por segurança, <strong>APAGUE este arquivo <code>seed.php</code></strong> após concluir.</p>
</body></html>
