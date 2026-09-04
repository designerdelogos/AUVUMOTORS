<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Proteção: só executa se ainda não houver admin OU se um admin estiver logado.
try {
    $hasAdmin = admin_count() > 0;
} catch (Throwable $e) {
    $hasAdmin = false; // banco ainda não tem tabelas
}
if ($hasAdmin && !admin_user()) {
    http_response_code(403);
    die('<h1>Acesso restrito</h1><p>Faça login como administrador para executar o instalador.</p>');
}


$logs = [];

try {
    // Executa schema
    $sql = file_get_contents(__DIR__ . '/sql/schema.sql');
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt) db()->exec($stmt);
    }
    $logs[] = '✔ Tabelas criadas/verificadas.';

    $vehicleColumns = [
        'make' => 'VARCHAR(80)',
        'model' => 'VARCHAR(120)',
        'year_model' => 'VARCHAR(20)',
        'mileage' => 'INT NULL',
        'transmission' => 'VARCHAR(40)',
        'fuel' => 'VARCHAR(40)',
        'color' => 'VARCHAR(40)',
    ];
    foreach ($vehicleColumns as $column => $type) {
        $exists = q('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=?', [DB_NAME, 'products', $column])->fetch();
        if (!$exists) {
            db()->exec('ALTER TABLE products ADD COLUMN ' . $column . ' ' . $type);
        }
    }
    $logs[] = '✔ Campos automotivos verificados.';

    // Seed platforms
    $platforms = [
        ['pl-loja','Loja própria','loja-propria','#111111'],
        ['pl-consig','Consignado','consignado','#555555'],
    ];
    foreach ($platforms as $p) {
        q('INSERT IGNORE INTO platforms (id,name,slug,color,active) VALUES (?,?,?,?,1)', $p);
    }
    $logs[] = '✔ Plataformas seed.';

    // Seed categories
    $cats = [
        ['c-sed','Sedans','sedans',1,'car'],
        ['c-suv','SUVs','suvs',2,'car-front'],
        ['c-hat','Hatches','hatches',3,'car'],
        ['c-pic','Picapes','picapes',4,'truck'],
        ['c-lux','Premium','premium',5,'sparkles'],
    ];
    foreach ($cats as $c) {
        q('INSERT IGNORE INTO categories (id,name,slug,`order`,icon,active) VALUES (?,?,?,?,?,1)', $c);
    }
    $logs[] = '✔ Categorias seed.';

    // Seed banners
    $banners = [
        ['b1','https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=2000&q=80','Showroom automotivo premium'],
        ['b2','https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=2000&q=80','Veículo esportivo em destaque'],
        ['b3','https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=2000&q=80','Carro em estrada aberta'],
    ];
    foreach ($banners as $b) {
        q('INSERT IGNORE INTO banners (id,image_url,alt,active) VALUES (?,?,?,1)', $b);
    }
    $logs[] = '✔ Banners seed.';

    // Seed products
    $products = [
        ['Honda Civic Touring 1.5 Turbo 2021','honda-civic-touring-2021','Sedan completo, automático e baixa quilometragem.','Honda Civic Touring com motor 1.5 turbo, câmbio automático, interior em couro, multimídia e câmera de ré.',139900.00,145900.00,'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80','c-sed','pl-loja','https://wa.me/5500000000000','Destaque',1,1,38],
        ['Jeep Compass Limited 2.0 Flex 2022','jeep-compass-limited-2022','SUV completo com teto panorâmico e acabamento premium.','Jeep Compass Limited flex, automático, bancos em couro, teto panorâmico, rodas de liga e revisões em dia.',154900.00,null,'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80','c-suv','pl-loja','https://wa.me/5500000000000','IPVA pago',1,0,52],
        ['Chevrolet Onix Premier Turbo 2023','chevrolet-onix-premier-2023','Hatch econômico, turbo, automático e tecnológico.','Chevrolet Onix Premier Turbo com câmbio automático, seis airbags, multimídia, câmera de ré e ótima média de consumo.',92900.00,97900.00,'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80','c-hat','pl-consig','https://wa.me/5500000000000','Baixa km',1,1,44],
    ];
    foreach ($products as $p) {
        $id = uid();
        q('INSERT IGNORE INTO products (id,name,slug,short_description,description,price,previous_price,image_url,category_id,platform_id,affiliate_url,badge,featured,offer,active,clicks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)',
            array_merge([$id], $p));
    }
    $logs[] = '✔ Produtos seed.';

    set_setting('site_name', 'Auto Store');
    set_setting('primary_color', '#111111');
    $logs[] = '✔ Configurações padrão.';

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
<html lang="pt-br"><head><meta charset="utf-8"><title>Instalação — Auto Store</title>
<style>body{font-family:system-ui,sans-serif;background:#fff;color:#111;padding:40px;max-width:720px;margin:auto}h1{color:#111}code,pre{background:#f3f3f3;padding:2px 6px;border-radius:4px}a{color:#111}.ok{color:#111}.err{color:#b00020}li{margin:6px 0}</style>
</head><body>
<h1>Instalação Auto Store</h1>
<ul>
<?php foreach ($logs as $l): ?><li class="<?= str_contains($l, '✔') ? 'ok' : 'err' ?>"><?= e($l) ?></li><?php endforeach; ?>
</ul>
<p><strong>Próximo passo:</strong> abra <a href="admin/login.php">admin/login.php</a> e crie a conta do administrador.</p>
<p style="color:#f5a623">Por segurança, apague este arquivo <code>install.php</code> após concluir.</p>
<p><a href="index.php">← Ir para a loja</a></p>
</body></html>
