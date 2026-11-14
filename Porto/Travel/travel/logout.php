<?php
require_once __DIR__ . '/auth.php';
auth_start_session();
require_authenticated_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Permintaan keluar tidak valid. Kembali ke halaman sebelumnya dan coba lagi.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $cookie['path'],
        'domain' => $cookie['domain'],
        'secure' => $cookie['secure'],
        'httponly' => $cookie['httponly'],
        'samesite' => $cookie['samesite'],
    ]);
}
session_destroy();

header('Location: login.php');
exit;
