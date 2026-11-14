<?php

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_name('travel_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function auth_is_authenticated(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'])
        && is_int($_SESSION['user_id'])
        && $_SESSION['user_id'] > 0
        && is_string($_SESSION['username'])
        && in_array($_SESSION['role'], ['admin', 'driver', 'penumpang'], true);
}

function require_authenticated_user(): void
{
    if (!auth_is_authenticated()) {
        header('Location: login.php');
        exit;
    }
}

function auth_csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function auth_verify_csrf_token(mixed $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function auth_sign_in(int $userId, string $username, string $role): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    unset($_SESSION['csrf_token']);
}

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function require_management_access(string $module): never
{
    if (($_SESSION['role'] ?? null) !== 'admin') {
        http_response_code(403);
        exit('Akses hanya tersedia untuk admin.');
    }
}
