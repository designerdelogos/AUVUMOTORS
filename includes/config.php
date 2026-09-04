<?php
// ============================================================
// AUTOSTORE — Configuração
// Ajuste os dados de acesso ao MySQL da Hostinger abaixo.
// ============================================================

define('DB_HOST', getenv('AUTOSTORE_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('AUTOSTORE_DB_NAME') ?: 'u901376787_user_0102');
define('DB_USER', getenv('AUTOSTORE_DB_USER') ?: 'u901376787_admin_0102');
define('DB_PASS', getenv('AUTOSTORE_DB_PASS') !== false ? getenv('AUTOSTORE_DB_PASS') : 'Priquito*02');
define('DB_CHARSET', getenv('AUTOSTORE_DB_CHARSET') ?: 'utf8mb4');

// URL base do site (sem barra no final). Deixe vazio para autodetectar.
define('SITE_URL', '');

// Nome exibido no painel/rodapé
define('SITE_NAME', 'Auto Store');

// Acesso temporário criado pelo install.php se ainda não existir administrador.
define('TEMP_ADMIN_NAME', 'Administrador');
define('TEMP_ADMIN_EMAIL', 'admin@autostore.com');
define('TEMP_ADMIN_PASS', 'AutoStore@2026');

// Chave usada em admin/recover.php para redefinir senha sem depender de e-mail/SMTP.
// Troque antes de publicar em definitivo.
define('ADMIN_RECOVERY_KEY', 'AUTO-RECUPERA-2026');

// Fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Sessões
if (session_status() === PHP_SESSION_NONE) {
    if (getenv('AUTOSTORE_SESSION_PATH')) {
        session_save_path(getenv('AUTOSTORE_SESSION_PATH'));
    }
    session_start();
}
