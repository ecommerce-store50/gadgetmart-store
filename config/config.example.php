<?php
// Copy this file to config.php and fill in your InfinityFree MySQL credentials.
define('DB_HOST', 'sqlXXX.infinityfree.com');
define('DB_NAME', 'if0_XXXXXXXX_gadgetmart');
define('DB_USER', 'if0_XXXXXXXX');
define('DB_PASS', 'your-password');
define('BASE_URL', '/');

date_default_timezone_set('Asia/Dhaka');

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function setting(string $key, string $fallback = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try { foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) $cache[$row['setting_key']] = $row['setting_value']; }
        catch (Throwable $ignored) {}
    }
    return $cache[$key] ?? $fallback;
}
