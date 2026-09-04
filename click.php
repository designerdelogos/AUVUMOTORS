<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
$id = $_POST['id'] ?? $_GET['id'] ?? '';
$p = q('SELECT * FROM products WHERE id=?', [$id])->fetch();
if (!$p) { http_response_code(404); die('Produto não encontrado.'); }
q('UPDATE products SET clicks = clicks + 1 WHERE id=?', [$id]);
q('INSERT INTO click_logs (product_id,platform_id,device) VALUES (?,?,?)', [$id, $p['platform_id'], device()]);
header('Location: ' . $p['affiliate_url']);
exit;
