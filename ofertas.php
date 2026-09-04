<?php
require __DIR__ . '/includes/bootstrap.php';

$__title = 'Oportunidades — Auto Store';
$products = q('SELECT * FROM products WHERE active=1 AND offer=1 ORDER BY updated_at DESC')->fetchAll();
if (!$products) $products = array_values(array_filter(fallback_products(), fn($p) => !empty($p['offer'])));

require __DIR__ . '/includes/header.php';
?>
<section class="mx-auto max-w-7xl px-4 py-10 lg:px-6">
  <div class="rounded-3xl border border-neon/30 bg-gradient-to-r from-neon/10 via-transparent to-transparent p-8">
    <div class="inline-flex items-center gap-2 rounded-full bg-neon/15 px-3 py-1 text-xs font-bold uppercase tracking-widest text-neon">Destaques</div>
    <h1 class="mt-4 text-3xl font-extrabold tracking-tight md:text-5xl">Oportunidades da semana</h1>
    <p class="mt-2 max-w-2xl text-muted-foreground">Condições e disponibilidade podem mudar conforme negociação e estoque.</p>
  </div>
  <div class="degra-product-grid mt-10">
    <?php foreach ($products as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
