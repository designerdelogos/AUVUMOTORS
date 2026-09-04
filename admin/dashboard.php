<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_admin();
csrf_check(); // valida token em qualquer POST do painel
$section = $_GET['section'] ?? 'overview';
$allowed = ['overview','products','categories','platforms','banners','clicks','settings'];
if (!in_array($section, $allowed, true)) $section = 'overview';
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$icons = [
  'overview' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h6v7H4z"/><path d="M14 4h6v16h-6z"/><path d="M4 4h6v5H4z"/></svg>',
  'products' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12l1.8-4.2A2 2 0 0 1 8.65 6h6.7a2 2 0 0 1 1.85 1.8L19 12"/><path d="M4 12h16v5H4z"/><circle cx="7" cy="17" r="1.5"/><circle cx="17" cy="17" r="1.5"/></svg>',
  'categories' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V4h9l8.6 8.6a2 2 0 0 1 0 .8z"/><circle cx="8" cy="8" r="1.5"/></svg>',
  'platforms' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1"/></svg>',
  'banners' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m7 15 3-3 2 2 3-4 2 5"/><circle cx="8" cy="9" r="1"/></svg>',
  'clicks' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 4-4 3 3 5-7"/></svg>',
  'settings' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5z"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2 3.4-.2-.1a1.7 1.7 0 0 0-2 .1 1.7 1.7 0 0 0-.8 1.7V22H9.2v-.2a1.7 1.7 0 0 0-.8-1.7 1.7 1.7 0 0 0-2-.1l-.2.1-2-3.4.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1.1H3V10h.1a1.7 1.7 0 0 0 1.5-1.1A1.7 1.7 0 0 0 4.3 7l-.1-.1 2-3.4.2.1a1.7 1.7 0 0 0 2-.1A1.7 1.7 0 0 0 9.2 2V2h5.6v.2a1.7 1.7 0 0 0 .8 1.7 1.7 1.7 0 0 0 2 .1l.2-.1 2 3.4-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.5 1.1h.1V14h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>',
];
$nav = [
  'overview' => ['Visão geral',$icons['overview']],
  'products' => ['Veículos',$icons['products']],
  'categories' => ['Categorias',$icons['categories']],
  'platforms' => ['Origens',$icons['platforms']],
  'banners' => ['Banners',$icons['banners']],
  'clicks' => ['Cliques',$icons['clicks']],
  'settings' => ['Configurações',$icons['settings']],
];
?>
<!doctype html>
<html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($nav[$section][0]) ?> — Painel Auto Store</title>
<meta name="robots" content="noindex">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260904-admin-icons">
<link rel="icon" type="image/webp" href="<?= asset('favicon2.webp') ?>?v=20260904">
</head>
<body class="min-h-screen bg-background text-foreground font-sans antialiased">
<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="hidden w-64 shrink-0 border-r border-border bg-sidebar md:block">
    <div class="p-5 border-b border-border">
      <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-2">
        <img src="<?= asset('logo_auvumotors.webp') ?>" alt="Auvu Motors" class="h-8">
      </a>
      <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-neon">Painel administrativo</p>
    </div>
    <nav class="p-3">
      <?php foreach ($nav as $key=>[$label,$icon]): ?>
        <a href="?section=<?= $key ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm <?= $section===$key ? 'bg-neon/15 text-neon font-semibold' : 'text-foreground/80 hover:bg-secondary' ?>">
          <span class="admin-nav-icon"><?= $icon ?></span> <?= e($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </aside>
  <!-- Content -->
  <div class="flex flex-1 flex-col">
    <header class="flex items-center justify-between border-b border-border bg-card/40 px-5 py-3">
      <div>
        <div class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Painel</div>
        <h1 class="text-lg font-bold"><?= e($nav[$section][0]) ?></h1>
      </div>
      <div class="flex items-center gap-3">
        <div class="hidden text-right md:block">
          <div class="text-sm font-semibold"><?= e($admin['name']) ?></div>
          <div class="text-[11px] text-muted-foreground"><?= e($admin['email']) ?></div>
        </div>
        <a href="<?= url('admin/logout.php') ?>" class="rounded-lg border border-border px-3 py-2 text-sm hover:border-destructive hover:text-destructive">Sair</a>
      </div>
    </header>
    <!-- Mobile section nav -->
    <div class="md:hidden overflow-x-auto border-b border-border bg-card/40 px-3 py-2">
      <div class="flex gap-1">
        <?php foreach ($nav as $key=>[$label,$icon]): ?>
          <a href="?section=<?= $key ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-1.5 text-xs <?= $section===$key ? 'bg-neon text-neon-foreground font-semibold' : 'text-foreground/80' ?>"><span class="admin-nav-icon"><?= $icon ?></span> <?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <main class="flex-1 p-5">
      <?php if ($flash): ?><div class="mb-4 rounded-lg border border-neon/40 bg-neon/10 p-3 text-sm text-neon"><?= e($flash) ?></div><?php endif; ?>
      <?php include __DIR__ . '/sections/' . $section . '.php'; ?>
    </main>
  </div>
</div>
</body></html>
