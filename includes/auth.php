<?php
require_once __DIR__ . '/bootstrap.php';

function admin_user(): ?array {
    if (empty($_SESSION['admin_id'])) return null;
    $r = q('SELECT id,name,email,created_at FROM admins WHERE id=?', [$_SESSION['admin_id']])->fetch();
    return $r ?: null;
}
function require_admin(): array {
    $u = admin_user();
    if (!$u) redirect('admin/login.php');
    return $u;
}
function admin_count(): int {
    return (int) q('SELECT COUNT(*) c FROM admins')->fetch()['c'];
}
function admin_signup(string $name, string $email, string $password): array {
    if (admin_count() > 0) {
        return ['ok' => false, 'error' => 'Já existe um administrador. Use "Entrar".'];
    }
    $id = uid();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    q('INSERT INTO admins (id,name,email,password_hash) VALUES (?,?,?,?)', [$id,$name,$email,$hash]);
    $_SESSION['admin_id'] = $id;
    return ['ok' => true];
}
function admin_create(string $name, string $email, string $password): array {
    $id = uid();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    q('INSERT INTO admins (id,name,email,password_hash) VALUES (?,?,?,?)', [$id,$name,$email,$hash]);
    return ['ok' => true, 'id' => $id];
}
function admin_login(string $email, string $password): array {
    $r = q('SELECT * FROM admins WHERE email=? LIMIT 1', [$email])->fetch();
    if (!$r || !password_verify($password, $r['password_hash'])) {
        return ['ok' => false, 'error' => 'E-mail ou senha inválidos.'];
    }
    $_SESSION['admin_id'] = $r['id'];
    return ['ok' => true];
}
function admin_set_password(string $adminId, string $password): array {
    if (strlen($password) < 8) {
        return ['ok' => false, 'error' => 'Use uma senha com pelo menos 8 caracteres.'];
    }
    q('UPDATE admins SET password_hash=? WHERE id=?', [password_hash($password, PASSWORD_DEFAULT), $adminId]);
    return ['ok' => true];
}
function admin_reset_password_by_email(string $email, string $recoveryKey, string $password): array {
    if (!defined('ADMIN_RECOVERY_KEY') || ADMIN_RECOVERY_KEY === '' || !hash_equals(ADMIN_RECOVERY_KEY, $recoveryKey)) {
        return ['ok' => false, 'error' => 'Chave de recuperação inválida.'];
    }
    $admin = q('SELECT id FROM admins WHERE email=? LIMIT 1', [$email])->fetch();
    if (!$admin) return ['ok' => false, 'error' => 'Administrador não encontrado.'];
    return admin_set_password($admin['id'], $password);
}
function admin_logout(): void {
    unset($_SESSION['admin_id']);
    session_destroy();
}
