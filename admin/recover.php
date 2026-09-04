<?php
require_once __DIR__ . '/../includes/auth.php';
if (admin_user()) redirect('admin/dashboard.php?section=settings');
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        $error = 'Sessão expirada. Recarregue a página.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $key = trim($_POST['recovery_key'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        if ($password !== $confirm) {
            $error = 'As senhas não conferem.';
        } else {
            $result = admin_reset_password_by_email($email, $key, $password);
            if ($result['ok']) {
                $success = 'Senha redefinida. Você já pode entrar com a nova senha.';
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>
<!doctype html>
<html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Recuperar senha — Auto Store</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260901-slideshow">
<link rel="icon" type="image/webp" href="<?= asset('favicon2.webp') ?>?v=20260904">
</head>
<body class="min-h-screen bg-background text-foreground font-sans antialiased">
<div class="mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center px-5 py-10">
  <a href="<?= url('index.php') ?>" class="mb-6 inline-flex items-center">
    <img src="<?= asset('logo_auvumotors.webp') ?>" alt="Auvu Motors" class="h-11">
  </a>
  <div class="w-full rounded-2xl border border-border bg-card p-7 shadow-2xl">
    <div class="mb-6 text-center">
      <h1 class="text-xl font-bold">Recuperar senha</h1>
      <p class="mt-1.5 text-sm text-muted-foreground">Informe o e-mail do administrador, a chave de recuperação e a nova senha.</p>
    </div>
    <?php if ($error): ?><div class="mb-4 rounded-lg border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="mb-4 rounded-lg border border-border bg-secondary p-3 text-sm"><?= e($success) ?></div><?php endif; ?>
    <form method="post" class="space-y-4">
      <?= csrf_field() ?>
      <label class="block">
        <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">E-mail administrador</div>
        <input name="email" type="email" required class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon" placeholder="admin@autostore.com">
      </label>
      <label class="block">
        <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">Chave de recuperação</div>
        <input name="recovery_key" required class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon" placeholder="Chave definida no config.php">
      </label>
      <label class="block">
        <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">Nova senha</div>
        <input name="password" type="password" required minlength="8" class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon">
      </label>
      <label class="block">
        <div class="mb-1.5 text-[10.5px] font-bold uppercase tracking-widest text-muted-foreground">Confirmar nova senha</div>
        <input name="password_confirm" type="password" required minlength="8" class="w-full rounded-lg border border-border bg-input px-3 py-2.5 text-sm outline-none focus:border-neon">
      </label>
      <button class="w-full rounded-lg bg-neon py-3 text-sm font-bold text-neon-foreground hover:brightness-110">Redefinir senha</button>
    </form>
    <a href="<?= url('admin/login.php') ?>" class="mt-4 block text-center text-xs text-muted-foreground hover:text-neon">Voltar para o login</a>
  </div>
</div>
</body></html>
