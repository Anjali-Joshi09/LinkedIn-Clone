<?php

function e(mixed $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $letters = '';
    foreach (array_slice($parts ?: [], 0, 2) as $part) {
        $letters .= strtoupper(substr($part, 0, 1));
    }
    return $letters ?: 'LI';
}

function time_ago(?string $date): string {
    if (!$date) return 'just now';
    $diff = time() - strtotime($date);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm';
    if ($diff < 86400) return floor($diff / 3600) . 'h';
    if ($diff < 604800) return floor($diff / 86400) . 'd';
    return date('M j', strtotime($date));
}

function money_range(array $job): string {
    if (empty($job['salary_min']) && empty($job['salary_max'])) return 'Salary not disclosed';
    $cur = $job['salary_currency'] ?? 'USD';
    return $cur . ' ' . number_format((float) $job['salary_min']) . ' - ' . number_format((float) $job['salary_max']);
}

