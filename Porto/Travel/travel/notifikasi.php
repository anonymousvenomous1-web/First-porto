<?php

require_once __DIR__ . '/auth.php';
auth_start_session();
require_authenticated_user();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'notifications' => []]);
    exit;
}

if (!auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'notifications' => []]);
    exit;
}

$role = (string) $_SESSION['role'];
$userId = (int) $_SESSION['user_id'];
session_write_close();

try {
    require_once __DIR__ . '/connection.php';
    $conn->begin_transaction();
    $lookup = $conn->prepare(
        'SELECT idnotifikasi, idsewa, pesan, dibuat_pada
         FROM tbl_notifikasi
         WHERE peran_penerima = ? AND idpenerima = ? AND dibaca = 0
         ORDER BY idnotifikasi ASC
         LIMIT 20
         FOR UPDATE'
    );
    $lookup->bind_param('si', $role, $userId);
    $lookup->execute();
    $notifications = $lookup->get_result()->fetch_all(MYSQLI_ASSOC);

    if ($notifications) {
        $markRead = $conn->prepare(
            'UPDATE tbl_notifikasi SET dibaca = 1
             WHERE idnotifikasi = ? AND peran_penerima = ? AND idpenerima = ?'
        );
        foreach ($notifications as $notification) {
            $notificationId = (int) $notification['idnotifikasi'];
            $markRead->bind_param('isi', $notificationId, $role, $userId);
            $markRead->execute();
        }
    }
    $conn->commit();

    echo json_encode([
        'success' => true,
        'notifications' => array_map(
            static fn (array $notification): array => [
                'id' => (int) $notification['idnotifikasi'],
                'rental_id' => (int) $notification['idsewa'],
                'message' => (string) $notification['pesan'],
                'created_at' => (string) $notification['dibuat_pada'],
            ],
            $notifications
        ),
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (mysqli_sql_exception $exception) {
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->rollback();
        } catch (mysqli_sql_exception $rollbackException) {
            error_log('Rollback pembacaan notifikasi gagal: ' . $rollbackException->getMessage());
        }
    }
    error_log('Notifikasi travel gagal dimuat: ' . $exception->getMessage());
    http_response_code(503);
    echo json_encode(['success' => false, 'notifications' => []]);
}
