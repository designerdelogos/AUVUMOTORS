<?php
// Card de produto — $p = linha de produto.
// Usa platforms_map() (cache de request) em vez de find_platform() por card,
// eliminando N queries no grid de produtos.
if (!isset($platform)) {
    $__pmap = platforms_map();
    $platform = $p['platform_id'] && isset($__pmap[$p['platform_id']])
        ? $__pmap[$p['platform_id']]
        : fallback_platform($p['platform_id'] ?? null);
}
$hasDiscount = !empty($p['previous_price']) && $p['previous_price'] > $p['price'];
$images = product_images($p);
$cover = $images[0] ?? ($p['image_url'] ?? '');
$photoCount = count($images);
$specs = array_values(array_filter([
    $p['year_model'] ?? '',
    !empty($p['mileage']) ? number_format((int)$p['mileage'], 0, ',', '.') . ' km' : '',
    $p['transmission'] ?? '',
    $p['fuel'] ?? '',
]));
?>
<a href="<?= url('produto.php?slug=' . urlencode($p['slug'])) ?>" class="degra-product-card group flex flex-col overflow-hidden rounded-2xl border border-border bg-card transition hover:border-neon/60">
  <div class="relative aspect-[4/3] overflow-hidden bg-secondary/40">
    <img src="<?= e($cover) ?>" alt="<?= e($p['name']) ?>" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
    <?php if (!empty($p['badge'])): ?>
      <span class="absolute left-3 top-3 rounded-full bg-black px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm"><?= e($p['badge']) ?></span>
    <?php endif; ?>
    <?php if ($hasDiscount): ?>
      <span class="absolute right-3 top-3 rounded-full bg-black px-2.5 py-1 text-[11px] font-bold text-white shadow-sm">-<?= discount_pct((float)$p['previous_price'], (float)$p['price']) ?>%</span>
    <?php endif; ?>
    <span class="absolute bottom-3 right-3 rounded-full bg-black px-2.5 py-1 text-[11px] font-bold text-white shadow-sm"><?= $photoCount ?> foto<?= $photoCount === 1 ? '' : 's' ?></span>
  </div>
  <div class="flex flex-1 flex-col gap-3 p-4">
    <?php if ($platform): ?><span class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground"><?= e($platform['name']) ?></span><?php endif; ?>
    <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-foreground"><?= e($p['name']) ?></h3>
    <?php if ($specs): ?><div class="flex flex-wrap gap-1.5 text-[11px] font-semibold">
      <?php foreach (array_slice($specs, 0, 4) as $spec): ?><span class="rounded-full border border-black bg-black px-2 py-1 text-white"><?= e($spec) ?></span><?php endforeach; ?>
    </div><?php endif; ?>
    <div class="mt-auto flex items-end justify-between gap-2">
      <div>
        <?php if ($hasDiscount): ?><div class="text-xs text-muted-foreground line-through"><?= money((float)$p['previous_price']) ?></div><?php endif; ?>
        <div class="text-lg font-bold text-neon"><?= money((float)$p['price']) ?></div>
      </div>
      <span class="inline-flex items-center gap-1 rounded-lg bg-black px-3 py-1.5 text-xs font-bold text-white opacity-90 group-hover:opacity-100">Ver fotos →</span>
    </div>
  </div>
</a>
