<?php
require __DIR__ . '/includes/bootstrap.php';

$__title = 'Auto Store — veículos selecionados';

$products = q('SELECT * FROM products WHERE active=1 ORDER BY featured DESC, updated_at DESC')->fetchAll();
if (!$products) $products = fallback_products();

$offers = q('SELECT * FROM products WHERE active=1 AND offer=1 ORDER BY updated_at DESC LIMIT 4')->fetchAll();
if (!$offers) $offers = array_values(array_filter(fallback_products(), fn($p) => !empty($p['offer'])));

$banners = all_banners();
if (!$banners) $banners = fallback_banners();

$cats = all_categories();
if (!$cats) $cats = fallback_categories();

require __DIR__ . '/includes/header.php';
?>

<!-- Hero carousel -->
<section class="degra-hero" aria-label="Banner principal">
  <div id="hero-carousel" class="degra-hero-viewport">
    <div class="degra-hero-track">
      <?php foreach ($banners as $i => $b): ?>
        <div class="degra-hero-slide">
          <img src="<?= e($b['image_url']) ?>" alt="<?= e($b['alt'] ?? 'Banner Auto Store') ?>" <?= $i===0 ? 'loading="eager"' : 'loading="lazy"' ?>>
          <div class="degra-hero-shade"></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($banners) > 1): ?>
      <button type="button" class="degra-hero-btn degra-hero-prev" aria-label="Slide anterior">‹</button>
      <button type="button" class="degra-hero-btn degra-hero-next" aria-label="Próximo slide">›</button>
      <div class="degra-hero-dots" aria-label="Selecionar banner">
        <?php foreach ($banners as $i => $b): ?>
          <button type="button" class="degra-hero-dot <?= $i===0 ? 'is-active' : '' ?>" aria-label="Ir para o slide <?= $i + 1 ?>" data-slide="<?= $i ?>"></button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Category marquee (infinite carousel) -->
<style>
  .degra-cat-marquee{position:relative;overflow:hidden;border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--background);padding:18px 0}
  .degra-cat-marquee::before,.degra-cat-marquee::after{content:"";position:absolute;top:0;bottom:0;width:118px;z-index:2;pointer-events:none}
  .degra-cat-marquee::before{left:0;background:linear-gradient(to right,var(--background) 0%,var(--background) 24%,color-mix(in oklch,var(--background) 88%,transparent) 52%,transparent 100%)}
  .degra-cat-marquee::after{right:0;background:linear-gradient(to left,var(--background) 0%,var(--background) 24%,color-mix(in oklch,var(--background) 88%,transparent) 52%,transparent 100%)}
  .degra-cat-track{display:flex;width:max-content;gap:14px;padding:4px 14px;animation:degra-cat-scroll 38s linear infinite;will-change:transform}
  .degra-cat-marquee:hover .degra-cat-track{animation-play-state:paused}
  .degra-cat-pill{display:inline-flex;align-items:center;gap:10px;flex-shrink:0;white-space:nowrap;border:1px solid var(--border);background:color-mix(in oklch, var(--secondary) 60%, transparent);border-radius:9999px;padding:10px 22px;font-size:14px;font-weight:800;letter-spacing:.04em;color:color-mix(in oklch, var(--foreground) 92%, transparent);line-height:1;text-decoration:none;transition:border-color .2s,color .2s,box-shadow .2s}
  .degra-cat-pill svg{width:18px;height:18px;color:var(--neon);flex-shrink:0}
  .degra-cat-pill:hover{border-color:var(--neon);color:var(--neon);box-shadow:0 0 18px color-mix(in oklch, var(--neon) 18%, transparent)}
  @keyframes degra-cat-scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
  @media (max-width:640px){.degra-cat-marquee{padding:16px 0}.degra-cat-marquee::before,.degra-cat-marquee::after{width:58px}.degra-cat-track{gap:10px;padding:3px 10px;animation-duration:28s}.degra-cat-pill{padding:9px 16px;font-size:13px;gap:8px}.degra-cat-pill svg{width:16px;height:16px}}
</style>
<?php
$__catIcons = [
  'sedans'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13l2-5h14l2 5"/><path d="M5 13h14v5H5z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>',
  'mousepad'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/></svg>',
  'armazenamento'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>',
  'acessorios'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 22v-4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M22 12h-4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>',
  'teclados'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M7 14h10"/></svg>',
  'mouses'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="3" width="12" height="18" rx="6"/><path d="M12 7v4"/></svg>',
  'monitores'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
  'video-games'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2h-5L8 6H5a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-3z"/><circle cx="12" cy="14" r="3"/></svg>',
  'headsets'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1v-6h3zM3 19a2 2 0 0 0 2 2h1v-6H3z"/></svg>',
];
function degra_cat_icon($slug, $icons){
  $key = strtolower(str_replace(['ó','ô','õ','á','ã','é','ê','í','ú','ç',' ','_'],['o','o','o','a','a','e','e','i','u','c','-','-'],$slug));
  if (isset($icons[$key])) return $icons[$key];
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/></svg>';
}
$__catLoop = [];
for ($i = 0; $i < 8; $i++) {
  $__catLoop = array_merge($__catLoop, $cats);
}
?>
<section class="degra-cat-marquee" aria-label="Categorias">
  <div class="degra-cat-track">
    <?php foreach ($__catLoop as $c): ?>
      <a href="<?= url('categoria.php?slug='.urlencode($c['slug'])) ?>" class="degra-cat-pill"><?= degra_cat_icon($c['slug'], $__catIcons) ?><span><?= e($c['name']) ?></span></a>
    <?php endforeach; ?>
  </div>
</section>

<?php if ($offers): ?>
<section class="mx-auto max-w-7xl px-4 pt-14 lg:px-6">
  <div class="flex items-end justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-widest text-neon">Oportunidades</div>
      <h2 class="mt-1 text-2xl font-extrabold tracking-tight md:text-3xl">Veículos em destaque</h2>
    </div>
    <a href="<?= url('ofertas.php') ?>" class="text-sm font-semibold text-neon hover:underline">Ver todos →</a>
  </div>
  <div class="degra-product-grid mt-6">
    <?php foreach ($offers as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
</section>
<?php endif; ?>

<section class="mx-auto max-w-7xl px-4 pt-14 lg:px-6">
  <div class="flex items-end justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-widest text-neon">Estoque</div>
      <h2 class="mt-1 text-2xl font-extrabold tracking-tight md:text-3xl">Todos os veículos</h2>
    </div>
    <a href="<?= url('produtos.php') ?>" class="text-sm font-semibold text-neon hover:underline">Página completa →</a>
  </div>
  <div class="degra-product-grid mt-6">
    <?php foreach ($products as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
