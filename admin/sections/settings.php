<?php
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['new_password_confirm'] ?? '';
    $row = q('SELECT password_hash FROM admins WHERE id=?', [$admin['id']])->fetch();
    if (!$row || !password_verify($current, $row['password_hash'])) {
        $_SESSION['flash'] = 'Senha atual inválida.';
    } elseif ($new !== $confirm) {
        $_SESSION['flash'] = 'As novas senhas não conferem.';
    } else {
        $result = admin_set_password($admin['id'], $new);
        $_SESSION['flash'] = $result['ok'] ? 'Senha alterada com sucesso.' : $result['error'];
    }
    redirect('admin/dashboard.php?section=settings');
}
?>
<form method="post" class="max-w-xl rounded-2xl border border-border bg-card p-6">
  <?= csrf_field() ?>
  <h2 class="text-lg font-bold mb-4">Alterar senha do administrador</h2>
  <div class="mb-5 rounded-xl border border-border bg-secondary/50 p-4">
    <h3 class="text-sm font-bold">Administrador logado</h3>
    <p class="mt-1 text-sm text-muted-foreground"><?= e($admin['name']) ?> — <?= e($admin['email']) ?></p>
  </div>
  <div class="grid gap-4">
    <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Senha atual</div><input type="password" name="current_password" required class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
    <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Nova senha</div><input type="password" name="new_password" required minlength="8" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
    <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Confirmar nova senha</div><input type="password" name="new_password_confirm" required minlength="8" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
  </div>
  <div class="mt-5"><button class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">Alterar senha</button></div>
</form>
