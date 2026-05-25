<?php
// config/database.php
// Values are read from .env — never hardcode credentials here.

define('DB_HOST',    $_ENV['DB_HOST']    ?? 'localhost');
define('DB_NAME',    $_ENV['DB_NAME']    ?? 'linkedin_admin');
define('DB_USER',    $_ENV['DB_USER']    ?? 'linkedin_app');
define('DB_PASS',    $_ENV['DB_PASS']    ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');