<?php
// config/app.php
// Credentials are loaded from .env — never hardcode secrets here.

// ── Load .env ────────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2));
        if ($key && !isset($_ENV[$key])) {
            $_ENV[$key] = $val;
            putenv("$key=$val");
        }
    }
}

define('APP_NAME',    'LinkedIn Admin');
define('APP_URL',     rtrim($_ENV['APP_URL'] ?? 'http://localhost/Linkedin/public', '/'));
define('APP_VERSION', '1.0.0');

// Session
define('SESSION_NAME',     'linkedin_admin_session');
define('SESSION_LIFETIME', 3600); // 1 hour 

// Paths
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('VIEW_PATH',   APP_PATH  . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// ── SMTP Settings ─────────────────────────────────────────────
define('SMTP_HOST',           $_ENV['SMTP_HOST']           ?? 'smtp.gmail.com');
define('SMTP_PORT',     (int) ($_ENV['SMTP_PORT']           ?? 587));
define('SMTP_USER',           $_ENV['SMTP_USER']           ?? '');
define('SMTP_PASS',           $_ENV['SMTP_PASS']           ?? '');
define('SMTP_FROM_EMAIL',     $_ENV['SMTP_FROM_EMAIL']     ?? '');
define('SMTP_FROM_NAME',      $_ENV['SMTP_FROM_NAME']      ?? 'LinkedIn Clone');
define('SMTP_REPLY_TO_EMAIL', $_ENV['SMTP_REPLY_TO_EMAIL'] ?? '');
define('SMTP_REPLY_TO_NAME',  $_ENV['SMTP_REPLY_TO_NAME']  ?? SMTP_FROM_NAME);