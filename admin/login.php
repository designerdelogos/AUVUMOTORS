<?php
require_once __DIR__ . '/../includes/auth.php';
if (admin_user()) redirect('admin/dashboard.php');
$hasAdmin = admin_count() > 0;
$mode = $_GET['mode'] ?? ($hasAdmin ? 'login' : 'signup');
$error = null;

// ---- Rate limit simples por IP (por sessão + janela em memória de sessão) ----
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? ['count' => 0, 'until' => 0];
$rl = &$_SESSION['login_attempts'];
$now = time();
$locked = $rl['until'] > $now;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        $error = 'Sessão expirada. Recarregue a página.';
    } elseif ($locked) {
        $error = 'Muitas tentativas. Aguarde ' . ($rl['until'] - $now) . 's e tente novamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? 'Administrador');
        if ($mode === 'signup' && !$hasAdmin) {
            $r = admin_signup($name, $email, $password);
        } else {
            $r = admin_login($email, $password);
        }
        if ($r['ok']) {
            $rl = ['count' => 0, 'until' => 0];
            redirect('admin/dashboard.php');
        }
        // Falha: incrementa e trava após 5 tentativas por 60s
        $rl['count']++;
        if ($rl['count'] >= 5) {
            $rl['until'] = $now + 60;
            $rl['count'] = 0;
        }
        // Delay para dificultar brute-force
        usleep(400000);
        $error = $r['error'];
    }
}
?>
<!doctype html>
<html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Painel — Auto Store</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260901-slideshow">
<link rel="icon" type="image/webp" href="<?= asset('favicon2.webp') ?>?v=20260904">
</head>
<body class="min-h-screen bg-background text-foreground font-sans antialiased">
<div class="relative min-h-screen overflow-hidden">
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute left-1/2 top-1/2 h-[720px] w-[720px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,oklch(0_0_0/0.08),transparent_60%)]"></div>
  </div>
  <div class="relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center px-5 py-10">
    <a href="<?= url('index.php') ?>" class="mb-6 inline-flex items-center">
      <img src="<?= asset('logo_auvumotors.webp') ?>" alt="Auvu Motors" class="h-11">
    </a>
    <div class="w-full rounded-2xl border border-border bg-card/80 p-7 shadow-2xl backdrop-blur-sm">
      <div class="mb-6 text-center">
        <h1 class="text-xl font-bold"><?= $mode==='login' ? 'Acessar painel' : 'Criar conta de administrador' ?></h1>
        <p class="mt-1.5 text-sm text-muted-foreground">
          <?= $mode==='login' ? 'Entre com suas credenciais para gerenciar o estoque.' : 'O primeiro cadastro se torna administrador automaticamente.' ?>
        </p>
      </div>
      <?php if ($error): ?><div class="mb-4 rounded-lg border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive"><?= e($error) ?></div><?php endif; ?>
      <form method="post" class="space-y-4">
        <?= csrf_field() ?>
        <?php if ($mode==='signup' && !$hasAdmin): ?>
          <label class="block">
            <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">Nome</div>
            <input name="name" required class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon" placeholder="Seu nome">
          </label>
        <?php endif; ?>
        <label class="block">
          <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">E-mail</div>
          <input name="email" type="email" required class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon" placeholder="voce@autostore.com">
        </label>
        <label class="block">
          <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">Senha</div>
          <input name="password" type="password" required class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon" placeholder="••••••••">
        </label>
        <button class="w-full rounded-lg bg-neon py-3 text-sm font-bold text-neon-foreground hover:brightness-110"><?= $mode==='login' ? 'Entrar' : 'Criar conta e entrar' ?></button>
        <?php if ($hasAdmin): ?>
          <?php if ($mode==='login'): ?>
            <a href="<?= url('admin/recover.php') ?>" class="block text-center text-xs text-muted-foreground hover:text-neon">Esqueci minha senha</a>
          <?php else: ?>
            <a href="?mode=login" class="block text-center text-xs text-muted-foreground hover:text-neon">Já tem conta? Entrar</a>
          <?php endif; ?>
        <?php endif; ?>
      </form>
    </div>
    <a href="<?= url('index.php') ?>" class="mt-4 text-[11px] text-muted-foreground hover:text-neon">← Voltar para a loja</a>
  </div>
</div>
</body></html>
