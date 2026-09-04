<?php
require __DIR__ . '/includes/bootstrap.php';

$__title = 'Estoque de veículos — Auto Store';
$q_search = trim($_GET['q'] ?? '');

if ($q_search !== '') {
  $like = '%' . $q_search . '%';
  $products = q('SELECT * FROM products WHERE active=1 AND (name LIKE ? OR short_description LIKE ?) ORDER BY updated_at DESC', [$like, $like])->fetchAll();
  $has_db_products = ((int) q('SELECT COUNT(*) AS total FROM products')->fetch()['total']) > 0;
  if (!$has_db_products) {
    $products = array_values(array_filter(
      fallback_products(),
      fn($p) => stripos($p['name'], $q_search) !== false || stripos($p['short_description'], $q_search) !== false
    ));
  }
} else {
  $products = q('SELECT * FROM products WHERE active=1 ORDER BY updated_at DESC')->fetchAll();
  if (!$products) $products = fallback_products();
}

require __DIR__ . '/includes/header.php';
?>
<section class="mx-auto max-w-7xl px-4 py-10 lg:px-6">
  <div class="rounded-3xl border border-border bg-card p-8">
    <h1 class="text-3xl font-extrabold tracking-tight md:text-5xl">Estoque de veículos</h1>
    <p class="mt-2 max-w-2xl text-muted-foreground">Confira os carros disponíveis com fotos, detalhes e valores atualizados.</p>
    <?php if ($q_search): ?><p class="mt-3 text-sm text-neon">Resultados para "<?= e($q_search) ?>" (<?= count($products) ?>)</p><?php endif; ?>
  </div>
  <div class="degra-product-grid degra-product-grid-page mt-10">
    <?php foreach ($products as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
    <?php if (!$products): ?><p class="col-span-full text-center text-muted-foreground py-10">Nenhum veículo encontrado.</p><?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
