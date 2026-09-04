<?php
require_once __DIR__ . '/config.php';

/**
 * PDO singleton. Usa conexão persistente para reduzir latência em requests
 * consecutivos em hospedagens compartilhadas (Hostinger).
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die('<h1>Erro de conexão</h1><p>Verifique <code>includes/config.php</code>. Depois execute <a href="install.php">install.php</a>.</p><p><small>' . htmlspecialchars($e->getMessage()) . '</small></p>');
        }
    }
    return $pdo;
}

/**
 * Executa uma prepared statement e reaproveita o PDOStatement quando o
 * mesmo SQL é chamado várias vezes na mesma request (ex.: product_card).
 */
function q(string $sql, array $params = []): PDOStatement {
    static $stmts = [];
    if (!isset($stmts[$sql])) $stmts[$sql] = db()->prepare($sql);
    $st = $stmts[$sql];
    $st->execute($params);
    return $st;
}
