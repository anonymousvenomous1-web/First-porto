<?php

const AUTH_DEFAULT_ADMIN_USERNAME = 'kiki';
const AUTH_DEFAULT_ADMIN_PASSWORD_HASH = '$2y$10$NRDUipGzfC0OAWUHPqzKGOTzCbAk1A1Ehg8dlNFLnvImlsEYguF16';

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_name('perpustakaan_session_v2');
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
        && in_array($_SESSION['role'], ['admin', 'petugas', 'siswa'], true);
}

function require_authenticated_user(): void
{
    if (!auth_is_authenticated()) {
        $applicationPath = realpath(__DIR__);
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        $loginPath = 'login.php';

        if ($applicationPath !== false && $documentRoot !== false
            && str_starts_with(strtolower($applicationPath), strtolower($documentRoot))) {
            $relativePath = substr($applicationPath, strlen($documentRoot));
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            $loginPath = '/' . trim($relativePath, '/') . '/login.php';
        }

        header('Location: ' . $loginPath);
        exit;
    }
}

function require_management_access(string $module): void
{
    require_authenticated_user();

    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $scriptName = basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['SCRIPT_FILENAME'] ?? '');
    $requestedPage = isset($_GET['halaman']) && is_string($_GET['halaman'])
        ? $_GET['halaman']
        : '';
    $isDeleteEndpoint = str_ends_with($scriptName, 'hapus.php');
    $isActionEndpoint = str_ends_with($scriptName, '_aksi.php');
    $isFormRoute = preg_match('/(?:tambah|edit)$/', $requestedPage) === 1
        || preg_match('/(?:tambah|edit)\.php$/', $scriptName) === 1;

    if (!in_array($method, ['GET', 'HEAD', 'POST'], true)) {
        http_response_code(405);
        exit('Metode permintaan tidak diizinkan.');
    }

    if ($isDeleteEndpoint && $method !== 'POST') {
        http_response_code(405);
        exit('Penghapusan harus dikirim melalui formulir.');
    }

    if ($isActionEndpoint && $method !== 'POST') {
        http_response_code(405);
        exit('Perubahan data harus dikirim melalui formulir.');
    }

    $isWriteRequest = $method === 'POST' || $isActionEndpoint || $isDeleteEndpoint;
    if (!$isWriteRequest && !$isFormRoute) {
        return;
    }

    if ($method === 'POST' && !auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        exit('Formulir sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }

    if ($isFormRoute && $method !== 'POST') {
        if (auth_can_manage($module)) {
            return;
        }

        if ($_SESSION['role'] === 'siswa'
            && $module === 'peminjaman'
            && in_array($requestedPage, ['', 'peminjamantambah'], true)) {
            return;
        }
    } elseif (auth_can_manage($module)) {
        return;
    } elseif ($_SESSION['role'] === 'siswa'
        && $module === 'peminjaman'
        && $method === 'POST'
        && $scriptName === 'peminjamantambah_aksi.php') {
        return;
    }

    http_response_code(403);
    exit('Akses ditolak. Peran akun ini tidak dapat mengubah data pada halaman tersebut.');
}

function auth_can_manage(string $module): bool
{
    if (($_SESSION['role'] ?? null) === 'admin') {
        return true;
    }

    return ($_SESSION['role'] ?? null) === 'petugas'
        && in_array($module, ['buku', 'siswa', 'peminjaman'], true);
}

function auth_can_create_peminjaman(): bool
{
    return in_array($_SESSION['role'] ?? null, ['admin', 'petugas', 'siswa'], true);
}

function auth_username_is_valid(string $username): bool
{
    return preg_match('/^[A-Za-z0-9._-]{3,50}$/D', $username) === 1
        && strcasecmp($username, AUTH_DEFAULT_ADMIN_USERNAME) !== 0;
}

function auth_password_is_valid(string $password): bool
{
    $characters = preg_match_all('/./us', $password, $matches);
    return $characters !== false && $characters >= 8 && strlen($password) <= 72;
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

function auth_sign_in(int $userId, string $username, string $role, ?string $displayName = null): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['display_name'] = $displayName !== null && $displayName !== '' ? $displayName : $username;
    $_SESSION['role'] = $role;
    unset($_SESSION['csrf_token']);
}
