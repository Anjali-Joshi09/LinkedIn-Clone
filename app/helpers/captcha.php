<?php

function captcha_generate(): string {
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';

    for ($i = 0; $i < 6; $i++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    $_SESSION['captcha_code'] = $code;
    $_SESSION['captcha_created_at'] = time();

    return $code;
}

function captcha_code(): string {
    if (
        empty($_SESSION['captcha_code']) ||
        empty($_SESSION['captcha_created_at']) ||
        (time() - (int) $_SESSION['captcha_created_at']) > 600
    ) {
        return captcha_generate();
    }

    return (string) $_SESSION['captcha_code'];
}

function captcha_verify(string $input): bool {
    $expected = $_SESSION['captcha_code'] ?? '';
    $createdAt = (int) ($_SESSION['captcha_created_at'] ?? 0);

    unset($_SESSION['captcha_code'], $_SESSION['captcha_created_at']);

    if ($expected === '' || $createdAt === 0 || (time() - $createdAt) > 600) {
        return false;
    }

    return hash_equals(strtolower((string) $expected), strtolower(trim($input)));
}
