<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$cat = find_category_by_slug($slug);
if (!$cat) {
  foreach (fallback_categories() as $__fb) if ($__fb['slug'] === $slug) { $cat = $__fb; break; }
}
if (!$cat) {
  http_response_code(404);
  $__title = 'Categoria não encontrada';
  require __DIR__ . '/includes/header.php';
  echo '<section class="mx-auto max-w-7xl px-4 py-20 text-center"><h1 class="text-3xl font-bold">Categoria não encontrada</h1></section>';
  require __DIR__ . '/includes/footer.php';
  exit;
}

$__title = $cat['name'] . ' — Auto Store';
$products = q('SELECT * FROM products WHERE active=1 AND category_id=? ORDER BY updated_at DESC', [$cat['id']])->fetchAll();
if (!$products) $products = array_values(array_filter(fallback_products(), fn($p) => $p['category_id'] === $cat['id']));

require __DIR__ . '/includes/header.php';
?>
<section class="mx-auto max-w-7xl px-4 py-10 lg:px-6">
  <div class="rounded-3xl border border-border bg-card p-8">
    <div class="text-[11px] font-bold uppercase tracking-widest text-neon">Categoria</div>
    <h1 class="mt-1 text-3xl font-extrabold tracking-tight md:text-5xl"><?= e($cat['name']) ?></h1>
    <?php if (!empty($cat['description'])): ?><p class="mt-2 max-w-2xl text-muted-foreground"><?= e($cat['description']) ?></p><?php endif; ?>
  </div>
  <div class="degra-product-grid mt-10">
    <?php foreach ($products as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
    <?php if (!$products): ?><p class="col-span-full text-center text-muted-foreground py-10">Ainda não há veículos nessa categoria.</p><?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
