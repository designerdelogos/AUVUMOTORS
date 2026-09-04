<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
$__title = $__title ?? 'Auto Store — veículos selecionados';
$__description = $__description ?? 'Vitrine de veículos com fotos, detalhes, valores e atendimento direto.';
$__cats = all_categories();
if (!$__cats) $__cats = fallback_categories();
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($__title) ?></title>
<meta name="description" content="<?= e($__description) ?>">
<?php
  $__og_image = $__og_image ?? url('assets/img/og-image.png');
  $__og_url   = $__og_url   ?? url(ltrim($_SERVER['REQUEST_URI'] ?? '/', '/'));
?>
<meta property="og:title" content="<?= e($__title) ?>">
<meta property="og:description" content="<?= e($__description) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e($__og_url) ?>">
<meta property="og:image" content="<?= e($__og_image) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="Auto Store">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($__title) ?>">
<meta name="twitter:description" content="<?= e($__description) ?>">
<meta name="twitter:image" content="<?= e($__og_image) ?>">
<link rel="canonical" href="<?= e($__og_url) ?>">
<link rel="icon" type="image/webp" href="<?= asset('favicon2.webp') ?>?v=20260904">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260901-slideshow">
<style>
  .mobile-menu{display:none}
  .mobile-menu.open{display:block}
  .hamburger{display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;border:1px solid var(--border);border-radius:.5rem;background:transparent;color:inherit;cursor:pointer}
  @media (min-width:768px){.hamburger{display:none}}
</style>
</head>
<body class="min-h-screen bg-background text-foreground font-sans antialiased">

<header class="sticky top-0 z-40 border-b border-border/60 bg-background/85 backdrop-blur-lg">
  <div class="mx-auto flex max-w-7xl items-center gap-6 px-4 py-3 lg:px-6">
    <a href="<?= url('index.php') ?>" class="inline-flex items-center">
      <img src="<?= asset('logo_auvumotors.webp') ?>" alt="Auvu Motors" class="h-9">
    </a>
    <nav class="hidden items-center gap-1 md:flex">
      <a href="<?= url('index.php') ?>" class="rounded-md px-3 py-2 text-sm font-medium text-foreground/80 hover:text-neon">Início</a>
      <a href="<?= url('produtos.php') ?>" class="rounded-md px-3 py-2 text-sm font-medium text-foreground/80 hover:text-neon">Estoque</a>
      <a href="<?= url('ofertas.php') ?>" class="rounded-md px-3 py-2 text-sm font-medium text-neon">Destaques</a>
      <div class="relative ml-2 group">
        <button class="rounded-md px-3 py-2 text-sm font-medium text-foreground/80 hover:text-neon">Categorias</button>
        <div class="invisible absolute left-0 top-full mt-1 min-w-[240px] rounded-xl border border-border bg-card p-2 opacity-0 shadow-2xl transition-all group-hover:visible group-hover:opacity-100">
          <?php foreach (array_slice($__cats,0,9) as $c): ?>
            <a href="<?= url('categoria.php?slug=' . urlencode($c['slug'])) ?>" class="block rounded-md px-3 py-2 text-sm text-foreground/85 hover:bg-secondary hover:text-neon"><?= e($c['name']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </nav>
    <div class="ml-auto flex items-center gap-2">
      <form action="<?= url('produtos.php') ?>" method="get" class="hidden items-center gap-2 rounded-lg border border-border bg-secondary/60 px-3 py-1.5 md:flex">
        <input name="q" placeholder="Buscar veículo…" value="<?= e($_GET['q'] ?? '') ?>" class="w-52 bg-transparent text-sm outline-none placeholder:text-muted-foreground">
      </form>
      <button id="btnMobileMenu" type="button" class="hamburger" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobileMenu">
        <svg id="iconMenu" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        <svg id="iconClose" style="display:none" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </div>

  <!-- mobile hamburger panel -->
  <div id="mobileMenu" class="mobile-menu md:hidden border-t border-border bg-background">
    <div class="mx-auto max-w-7xl px-4 py-3 space-y-1">
      <form action="<?= url('produtos.php') ?>" method="get" class="mb-3 flex items-center gap-2 rounded-lg border border-border bg-secondary/60 px-3 py-2">
        <input name="q" placeholder="Buscar veículo…" value="<?= e($_GET['q'] ?? '') ?>" class="w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground">
      </form>
      <a href="<?= url('index.php') ?>" class="block rounded-md px-3 py-2 text-sm hover:bg-secondary">Início</a>
      <a href="<?= url('produtos.php') ?>" class="block rounded-md px-3 py-2 text-sm hover:bg-secondary">Estoque</a>
      <a href="<?= url('ofertas.php') ?>" class="block rounded-md px-3 py-2 text-sm text-neon hover:bg-secondary">Destaques</a>
      <div class="pt-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Categorias</div>
      <?php foreach (array_slice($__cats,0,12) as $c): ?>
        <a href="<?= url('categoria.php?slug=' . urlencode($c['slug'])) ?>" class="block rounded-md px-3 py-2 text-sm hover:bg-secondary"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</header>

<script>
(function(){
  var btn = document.getElementById('btnMobileMenu');
  var menu = document.getElementById('mobileMenu');
  var iM = document.getElementById('iconMenu');
  var iC = document.getElementById('iconClose');
  if(!btn||!menu) return;
  btn.addEventListener('click', function(){
    var open = menu.classList.toggle('open');
    btn.setAttribute('aria-expanded', open?'true':'false');
    iM.style.display = open?'none':'';
    iC.style.display = open?'':'none';
  });
})();
</script>

<main>
