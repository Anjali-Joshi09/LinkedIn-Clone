<?php

require_once dirname(__DIR__) . '/config/app.php';
require_once APP_PATH . '/helpers/captcha.php';

session_name(SESSION_NAME);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['lifetime' => SESSION_LIFETIME, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

$code = isset($_GET['refresh']) ? captcha_generate() : captcha_code();
$chars = str_split($code);
$noise = '';

for ($i = 0; $i < 12; $i++) {
    $x1 = random_int(0, 180);
    $y1 = random_int(0, 56);
    $x2 = random_int(0, 180);
    $y2 = random_int(0, 56);
    $opacity = random_int(12, 28) / 100;
    $noise .= "<line x1=\"{$x1}\" y1=\"{$y1}\" x2=\"{$x2}\" y2=\"{$y2}\" stroke=\"#0a66c2\" stroke-opacity=\"{$opacity}\" stroke-width=\"1\" />";
}

$letters = '';
foreach ($chars as $index => $char) {
    $x = 18 + ($index * 25);
    $y = random_int(33, 42);
    $rotate = random_int(-10, 10);
    $letters .= "<text x=\"{$x}\" y=\"{$y}\" transform=\"rotate({$rotate} {$x} {$y})\">{$char}</text>";
}

header('Content-Type: image/svg+xml');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="180" height="56" viewBox="0 0 180 56" role="img" aria-label="Security code">
  <rect width="180" height="56" rx="10" fill="#f8fafc"/>
  <rect x="0.5" y="0.5" width="179" height="55" rx="9.5" fill="none" stroke="#d9e2ec"/>
  {$noise}
  <g fill="#0f172a" font-family="Arial, Helvetica, sans-serif" font-size="25" font-weight="700" letter-spacing="4">
    {$letters}
  </g>
</svg>
SVG;
