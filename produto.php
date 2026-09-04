<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$p = find_product_by_slug($slug);
if (!$p) $p = fallback_product_by_slug($slug);
if (!$p) {
  http_response_code(404);
  $__title = 'Produto não encontrado';
  require __DIR__ . '/includes/header.php';
  echo '<section class="mx-auto max-w-7xl px-4 py-20 text-center"><h1 class="text-3xl font-bold">Produto não encontrado</h1></section>';
  require __DIR__ . '/includes/footer.php';
  exit;
}

$cat = $p['category_id'] ? find_category($p['category_id']) : null;
if (!$cat) {
  foreach (fallback_categories() as $__fb) if ($__fb['id'] === $p['category_id']) { $cat = $__fb; break; }
}
$platform = find_platform($p['platform_id'] ?? null);
$hasDiscount = !empty($p['previous_price']) && $p['previous_price'] > $p['price'];
$images = product_images($p);
$specs = [
  'Marca' => $p['make'] ?? '',
  'Modelo' => $p['model'] ?? '',
  'Ano' => $p['year_model'] ?? '',
  'Quilometragem' => !empty($p['mileage']) ? number_format((int)$p['mileage'], 0, ',', '.') . ' km' : '',
  'Câmbio' => $p['transmission'] ?? '',
  'Combustível' => $p['fuel'] ?? '',
  'Cor' => $p['color'] ?? '',
];

$__title = $p['name'] . ' — Auto Store';
$__description = $p['short_description'];

$related = q('SELECT * FROM products WHERE active=1 AND category_id=? AND id<>? ORDER BY updated_at DESC LIMIT 4', [$p['category_id'], $p['id']])->fetchAll();
if (!$related) {
  $related = array_slice(array_values(array_filter(
    fallback_products(),
    fn($__p) => $__p['category_id'] === $p['category_id'] && $__p['id'] !== $p['id']
  )), 0, 4);
}

require __DIR__ . '/includes/header.php';
?>
<section class="mx-auto max-w-7xl px-4 py-10 lg:px-6">
  <nav class="mb-6 text-xs text-muted-foreground">
    <a href="<?= url('index.php') ?>" class="hover:text-neon">Início</a> /
    <a href="<?= url('produtos.php') ?>" class="hover:text-neon">Produtos</a>
    <?php if ($cat): ?> / <a href="<?= url('categoria.php?slug='.$cat['slug']) ?>" class="hover:text-neon"><?= e($cat['name']) ?></a><?php endif; ?>
  </nav>
  <div class="grid gap-10 md:grid-cols-2">
    <div class="overflow-hidden rounded-2xl border border-border bg-card">
      <img id="vehicle-main-image" src="<?= e($images[0] ?? $p['image_url']) ?>" alt="<?= e($p['name']) ?>" class="w-full object-cover aspect-[4/3]">
      <?php if (count($images) > 1): ?>
        <div class="border-t border-border bg-secondary p-3">
          <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Galeria de fotos</div>
          <div class="grid grid-cols-5 gap-2">
          <?php foreach ($images as $img): ?>
            <button type="button" class="overflow-hidden rounded-lg border border-border bg-card" onclick="document.getElementById('vehicle-main-image').src='<?= e($img) ?>'">
              <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" class="w-full object-cover" style="aspect-ratio:1;">
            </button>
          <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="border-t border-border bg-secondary p-3 text-xs text-muted-foreground">Este veículo tem 1 foto cadastrada. Fotos adicionais aparecem aqui quando forem adicionadas no painel.</div>
      <?php endif; ?>
    </div>
    <div>
      <?php if ($platform): ?><span class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground"><?= e($platform['name']) ?></span><?php endif; ?>
      <h1 class="mt-2 text-3xl font-extrabold tracking-tight md:text-4xl"><?= e($p['name']) ?></h1>
      <?php if (!empty($p['badge'])): ?><span class="mt-3 inline-block rounded-full bg-neon/15 px-3 py-1 text-xs font-bold uppercase tracking-widest text-neon"><?= e($p['badge']) ?></span><?php endif; ?>
      <p class="mt-4 text-muted-foreground"><?= nl2br(e($p['description'])) ?></p>
      <div class="mt-6 grid grid-cols-2 gap-2">
        <?php foreach ($specs as $label => $value): if (!$value) continue; ?>
          <div class="rounded-xl border border-border bg-card p-3">
            <div class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground"><?= e($label) ?></div>
            <div class="mt-1 text-sm font-semibold"><?= e($value) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-6 flex items-end gap-4">
        <?php if ($hasDiscount): ?><div class="text-sm text-muted-foreground line-through"><?= money((float)$p['previous_price']) ?></div><?php endif; ?>
        <div class="text-4xl font-extrabold text-neon"><?= money((float)$p['price']) ?></div>
        <?php if ($hasDiscount): ?><span class="mb-1 rounded-md bg-neon/15 px-2 py-1 text-xs font-bold text-neon">-<?= discount_pct((float)$p['previous_price'],(float)$p['price']) ?>%</span><?php endif; ?>
      </div>
      <form method="get" action="<?= url('click.php') ?>" class="mt-8" target="_blank" rel="noopener noreferrer">
        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
        <button type="submit" class="w-full cursor-pointer rounded-xl bg-neon py-4 text-base font-bold text-neon-foreground transition hover:brightness-110" style="cursor:pointer">Tenho interesse neste veículo →</button>
      </form>
      <p class="mt-3 text-xs text-muted-foreground">Você será direcionado para o canal de atendimento configurado para este veículo.</p>
    </div>
  </div>

  <?php if ($related): ?>
  <div class="mt-16">
    <h2 class="text-xl font-bold">Você também pode gostar</h2>
    <div class="degra-product-grid mt-6">
      <?php foreach ($related as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
    </div>
  </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
