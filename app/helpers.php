<?php

declare(strict_types=1);

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}



function input_string(string $key, int $maxLength = 120): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    return mb_substr($value, 0, $maxLength, 'UTF-8');
}

function input_int(string $key): int
{
    $raw = $_POST[$key] ?? 0;
    if ($raw === '') {
        return 0;
    }

    return max(0, (int) $raw);
}

