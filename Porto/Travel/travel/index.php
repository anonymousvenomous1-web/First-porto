<?php
require_once __DIR__ . '/auth.php';
auth_start_session();
require_authenticated_user();

try {
    require_once __DIR__ . '/connection.php';
} catch (mysqli_sql_exception $exception) {
    error_log('Koneksi database travel gagal: ' . $exception->getMessage());
    http_response_code(503);
    exit('Basis data travel tidak dapat dihubungi. Periksa konfigurasi lalu coba lagi.');
}

function travel_ensure_driver_schema(mysqli $conn): void
{
    $columns = [];
    $columnCheck = $conn->query('SHOW COLUMNS FROM tbl_driver');
    if ($columnCheck !== false) {
        while ($column = $columnCheck->fetch_assoc()) {
            $columns[] = (string) $column['Field'];
        }
    }

    if (in_array('skorkelayakan', $columns, true)) {
        $conn->query('ALTER TABLE tbl_driver DROP COLUMN skorkelayakan');
    }
    if (!in_array('tanggal_lahir', $columns, true)) {
        $conn->query('ALTER TABLE tbl_driver ADD COLUMN tanggal_lahir DATE NULL AFTER nomorktp');
    }
    if (in_array('usia', $columns, true)) {
        $conn->query('ALTER TABLE tbl_driver DROP COLUMN usia');
    }
}

function travel_ensure_car_category_columns(mysqli $conn): void
{
    $columns = [];
    $columnCheck = $conn->query('SHOW COLUMNS FROM tbl_mobil');
    if ($columnCheck !== false) {
        while ($column = $columnCheck->fetch_assoc()) {
            $columns[] = (string) $column['Field'];
        }
    }

    if (!in_array('kategori', $columns, true)) {
        $conn->query("ALTER TABLE tbl_mobil ADD COLUMN kategori ENUM('Economy', 'Reguler', 'Luxury') NOT NULL DEFAULT 'Economy' AFTER namamobil");
    }
    if (!in_array('bid_harga', $columns, true)) {
        $conn->query('ALTER TABLE tbl_mobil ADD COLUMN bid_harga DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER kategori');
    }
}

function travel_ensure_media_columns(mysqli $conn): void
{
    $tableColumns = [
        'tbl_mobil' => ['gambar'],
        'tbl_driver' => ['gambar'],
        'tbl__penumpang' => ['gambar'],
    ];

    foreach ($tableColumns as $tableName => $columns) {
        $existingColumns = [];
        $columnCheck = $conn->query('SHOW COLUMNS FROM ' . $tableName);
        if ($columnCheck !== false) {
            while ($column = $columnCheck->fetch_assoc()) {
                $existingColumns[] = (string) $column['Field'];
            }
        }

        foreach ($columns as $columnName) {
            if (in_array($columnName, $existingColumns, true)) {
                continue;
            }

            if ($tableName === 'tbl_mobil') {
                $conn->query('ALTER TABLE tbl_mobil ADD COLUMN gambar VARCHAR(255) NULL AFTER nomorplat');
            } elseif ($tableName === 'tbl_driver') {
                $conn->query('ALTER TABLE tbl_driver ADD COLUMN gambar VARCHAR(255) NULL AFTER tanggal_lahir');
            } else {
                $conn->query('ALTER TABLE tbl__penumpang ADD COLUMN gambar VARCHAR(255) NULL AFTER nomorhp');
            }
        }
    }
}

function travel_normalize_image_url(mixed $value): ?string
{
    $stringValue = is_string($value) ? trim($value) : '';
    if ($stringValue === '') {
        return null;
    }

    $validatedUrl = filter_var($stringValue, FILTER_VALIDATE_URL);
    if ($validatedUrl === false) {
        return null;
    }

    $scheme = strtolower((string) parse_url($validatedUrl, PHP_URL_SCHEME));
    if (!in_array($scheme, ['http', 'https'], true)) {
        return null;
    }

    return substr($validatedUrl, 0, 255);
}

function travel_render_image_tag(?string $url, string $alt = 'Gambar', string $className = 'thumb-image'): string
{
    $normalizedUrl = travel_normalize_image_url($url);
    if ($normalizedUrl === null) {
        return '';
    }

    return '<img class="' . h($className) . '" src="' . h($normalizedUrl) . '" alt="' . h($alt) . '" loading="lazy" referrerpolicy="no-referrer" />';
}

function travel_car_category_options(): array
{
    return [
        'Economy' => 'Economy',
        'Reguler' => 'Reguler',
        'Luxury' => 'Luxury',
    ];
}

function travel_car_category_label(?string $category): string
{
    $normalizedCategory = trim((string) $category);
    if ($normalizedCategory === '') {
        return 'Belum ditentukan';
    }
    $categories = travel_car_category_options();
    return $categories[$normalizedCategory] ?? $normalizedCategory;
}

function travel_format_currency(mixed $value): string
{
    $numericValue = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($numericValue === false) {
        return 'Rp 0';
    }
    return 'Rp ' . number_format((float) $numericValue, 0, ',', '.');
}

function travel_redirect(string $page): never
{
    header('Location: index.php?halaman=' . rawurlencode($page));
    exit;
}

function travel_flash_redirect(string $page, string $type, string $message): never
{
    $_SESSION['travel_flash'] = ['type' => $type, 'message' => $message];
    travel_redirect($page);
}

function travel_positive_id(mixed $value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT);
    return $id !== false && $id > 0 ? $id : null;
}

function travel_format_datetime(string $value): string
{
    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $month = $months[(int) date('n', $timestamp) - 1];
    return date('d', $timestamp) . ' ' . $month . ' ' . date('Y, H:i', $timestamp);
}

travel_ensure_driver_schema($conn);
travel_ensure_car_category_columns($conn);
travel_ensure_media_columns($conn);

$role = $_SESSION['role'];
$isAdmin = $role === 'admin';
$canManage = $isAdmin;
$canDecide = $role === 'driver' || $isAdmin;
$page = isset($_GET['halaman']) && is_string($_GET['halaman']) ? $_GET['halaman'] : 'home';
if (!in_array($page, ['home', 'mobil', 'carter', 'kelola'], true)
    || ($page === 'kelola' && !$isAdmin)) {
    http_response_code(404);
    $page = 'home';
}
$flash = isset($_SESSION['travel_flash']) && is_array($_SESSION['travel_flash'])
    ? $_SESSION['travel_flash']
    : null;
unset($_SESSION['travel_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        travel_flash_redirect('home', 'error', 'Formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }

    $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : '';
    $transactionStarted = false;
    try {
        if ($action === 'create_rental') {
            if ($role !== 'penumpang' && !$isAdmin) {
                http_response_code(403);
                exit('Hanya penumpang yang dapat membuat catatan carter.');
            }

            $driverId = travel_positive_id($_POST['iddriver'] ?? null);
            $carId = travel_positive_id($_POST['idmobil'] ?? null);
            $passengerId = $isAdmin
                ? travel_positive_id($_POST['idpenumpang'] ?? null)
                : (int) $_SESSION['user_id'];
            $rentalInput = isset($_POST['waktusewa']) && is_string($_POST['waktusewa'])
                ? $_POST['waktusewa']
                : '';
            $rentalTime = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $rentalInput);
            $dateErrors = DateTimeImmutable::getLastErrors();

            if ($driverId === null || $carId === null || $passengerId === null || $rentalTime === false
                || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                || $rentalTime->format('Y-m-d\TH:i') !== $rentalInput) {
                travel_flash_redirect('carter', 'error', 'Pilih driver, mobil, dan waktu sewa yang valid.');
            }

            $driverCheck = $conn->prepare('SELECT iddriver FROM tbl_driver WHERE iddriver = ?');
            $driverCheck->bind_param('i', $driverId);
            $driverCheck->execute();
            $driverExists = $driverCheck->get_result()->fetch_assoc() !== null;
            $driverCheck->close();
            $carCheck = $conn->prepare('SELECT idmobil FROM tbl_mobil WHERE idmobil = ?');
            $carCheck->bind_param('i', $carId);
            $carCheck->execute();
            $carExists = $carCheck->get_result()->fetch_assoc() !== null;
            $carCheck->close();
            if (!$driverExists || !$carExists) {
                travel_flash_redirect('carter', 'error', 'Driver atau mobil sudah tidak tersedia. Muat ulang daftar lalu pilih lagi.');
            }

            $passengerCheck = $conn->prepare('SELECT idpenumpang FROM tbl__penumpang WHERE idpenumpang = ?');
            $passengerCheck->bind_param('i', $passengerId);
            $passengerCheck->execute();
            if ($passengerCheck->get_result()->fetch_assoc() === null) {
                travel_flash_redirect('carter', 'error', 'Penumpang tidak ditemukan. Muat ulang daftar lalu coba lagi.');
            }

            $rentalDate = $rentalTime->format('Y-m-d H:i:s');
            $conflictCheck = $conn->prepare(
                'SELECT s.idsewa
                 FROM tbl_sewa s
                 JOIN tbl_sewadetail sd ON sd.idsewa = s.idsewa
                 WHERE sd.idmobil = ? AND s.status <> ? AND DATE(s.waktusewa) = DATE(?)
                 LIMIT 1'
            );
            $cancelledStatus = 'cancelled';
            $conflictCheck->bind_param('iss', $carId, $cancelledStatus, $rentalDate);
            $conflictCheck->execute();
            if ($conflictCheck->get_result()->fetch_assoc() !== null) {
                travel_flash_redirect('carter', 'error', 'Mobil yang dipilih sudah dipesan pada hari yang sama. Pilih mobil lain atau ubah tanggal sewa.');
            }

            $conn->begin_transaction();
            $transactionStarted = true;
            $rentalStatement = $conn->prepare(
                'INSERT INTO tbl_sewa (iddriver, idpenumpang, waktusewa, status)
                 VALUES (?, ?, ?, \'pending\')'
            );
            $rentalDate = $rentalTime->format('Y-m-d H:i:s');
            $rentalStatement->bind_param('iis', $driverId, $passengerId, $rentalDate);
            $rentalStatement->execute();
            $rentalId = (int) $conn->insert_id;

            $detailStatement = $conn->prepare(
                'INSERT INTO tbl_sewadetail (idsewa, idmobil) VALUES (?, ?)'
            );
            $detailStatement->bind_param('ii', $rentalId, $carId);
            $detailStatement->execute();
            $notifyDriver = $conn->prepare(
                'INSERT INTO tbl_notifikasi
                    (peran_penerima, idpenerima, idsewa, jenis, pesan)
                 VALUES (\'driver\', ?, ?, \'permintaan\', ?)'
            );
            $requestMessage = 'Ada permintaan carter baru yang menunggu keputusan Anda.';
            $notifyDriver->bind_param('iis', $driverId, $rentalId, $requestMessage);
            $notifyDriver->execute();
            $conn->commit();
            $transactionStarted = false;
            travel_flash_redirect('carter', 'success', 'Permintaan carter terkirim dan menunggu persetujuan driver.');
        }

        if (in_array($action, ['create_car', 'update_car', 'delete_car'], true)) {
            if (!$canManage) {
                http_response_code(403);
                exit('Akses tulis armada hanya tersedia untuk admin.');
            }

            if ($action === 'delete_car') {
                $carId = travel_positive_id($_POST['idmobil'] ?? null);
                if ($carId === null) {
                    travel_flash_redirect('mobil', 'error', 'Mobil yang dipilih tidak valid.');
                }
                $delete = $conn->prepare('DELETE FROM tbl_mobil WHERE idmobil = ?');
                $delete->bind_param('i', $carId);
                $delete->execute();
                if ($delete->affected_rows !== 1) {
                    travel_flash_redirect('mobil', 'error', 'Mobil tidak ditemukan atau sudah dihapus.');
                }
                travel_flash_redirect('mobil', 'success', 'Data mobil berhasil dihapus.');
            }

            $brand = isset($_POST['merkmobil']) && is_string($_POST['merkmobil']) ? trim($_POST['merkmobil']) : '';
            $model = isset($_POST['namamobil']) && is_string($_POST['namamobil']) ? trim($_POST['namamobil']) : '';
            $category = isset($_POST['kategori']) && is_string($_POST['kategori']) ? trim($_POST['kategori']) : '';
            $bidValue = isset($_POST['bid_harga']) && is_string($_POST['bid_harga']) ? trim($_POST['bid_harga']) : '';
            $plate = isset($_POST['nomorplat']) && is_string($_POST['nomorplat']) ? trim($_POST['nomorplat']) : '';
            $imageUrl = travel_normalize_image_url($_POST['gambar'] ?? null);
            $imageUrl = $imageUrl ?? '';
            $normalizedBid = null;
            if (preg_match('/^\d{1,7}$/', $bidValue) === 1) {
                $normalizedBid = (int) $bidValue;
            }
            if ($brand === '' || strlen($brand) > 20 || $model === '' || strlen($model) > 50
                || !isset(travel_car_category_options()[$category])
                || $normalizedBid === null || $normalizedBid <= 0 || $normalizedBid > 9999999
                || $plate === '' || strlen($plate) > 12) {
                travel_flash_redirect('mobil', 'error', 'Lengkapi data mobil, kategori, dan bid harga sesuai format yang valid.');
            }

            if ($action === 'create_car') {
                $insert = $conn->prepare(
                    'INSERT INTO tbl_mobil (merkmobil, namamobil, kategori, bid_harga, nomorplat, gambar) VALUES (?, ?, ?, ?, ?, ?)'
                );
                $insert->bind_param('sssdss', $brand, $model, $category, $normalizedBid, $plate, $imageUrl);
                $insert->execute();
                travel_flash_redirect('mobil', 'success', 'Mobil berhasil ditambahkan ke daftar armada.');
            }

            $carId = travel_positive_id($_POST['idmobil'] ?? null);
            if ($carId === null) {
                travel_flash_redirect('mobil', 'error', 'Mobil yang dipilih tidak valid.');
            }
            $existingCar = $conn->prepare('SELECT idmobil FROM tbl_mobil WHERE idmobil = ?');
            $existingCar->bind_param('i', $carId);
            $existingCar->execute();
            $carExists = $existingCar->get_result()->fetch_assoc() !== null;
            $existingCar->close();
            if (!$carExists) {
                travel_flash_redirect('mobil', 'error', 'Mobil tidak ditemukan atau sudah dihapus.');
            }
            $update = $conn->prepare(
                'UPDATE tbl_mobil SET merkmobil = ?, namamobil = ?, kategori = ?, bid_harga = ?, nomorplat = ?, gambar = ?
                 WHERE idmobil = ?'
            );
            $update->bind_param('sssdssi', $brand, $model, $category, $normalizedBid, $plate, $imageUrl, $carId);
            $update->execute();
            travel_flash_redirect('mobil', 'success', 'Data mobil berhasil diperbarui.');
        }

        if (in_array($action, ['approve_rental', 'cancel_rental'], true)) {
            if ($action === 'approve_rental' && !$canDecide) {
                http_response_code(403);
                exit('Hanya driver atau admin yang dapat menyetujui carter.');
            }
            $rentalId = travel_positive_id($_POST['idsewa'] ?? null);
            if ($rentalId === null) {
                travel_flash_redirect('carter', 'error', 'Permintaan carter tidak valid.');
            }

            $rentalLookup = $conn->prepare(
                'SELECT iddriver, idpenumpang, status FROM tbl_sewa WHERE idsewa = ?'
            );
            $rentalLookup->bind_param('i', $rentalId);
            $rentalLookup->execute();
            $rentalToChange = $rentalLookup->get_result()->fetch_assoc();
            if (!$rentalToChange) {
                travel_flash_redirect('carter', 'error', 'Permintaan carter tidak ditemukan.');
            }

            $passengerOwnsRequest = $role === 'penumpang'
                && (int) $rentalToChange['idpenumpang'] === (int) $_SESSION['user_id'];
            $driverOwnsRequest = $role === 'driver'
                && (int) $rentalToChange['iddriver'] === (int) $_SESSION['user_id'];
            if (!$isAdmin && !$driverOwnsRequest && !$passengerOwnsRequest) {
                http_response_code(403);
                exit('Anda tidak memiliki akses untuk mengubah permintaan carter ini.');
            }

            $nextStatus = $action === 'approve_rental' ? 'approved' : 'cancelled';
            $oldStatus = (string) $rentalToChange['status'];
            if ($oldStatus === 'cancelled' || ($nextStatus === 'approved' && $oldStatus !== 'pending')
                || ($driverOwnsRequest && $oldStatus !== 'pending')) {
                travel_flash_redirect('carter', 'error', 'Status permintaan sudah berubah dan tidak dapat diproses lagi.');
            }

            $conn->begin_transaction();
            $transactionStarted = true;
            if ($driverOwnsRequest) {
                $statusUpdate = $conn->prepare(
                    'UPDATE tbl_sewa SET status = ?
                     WHERE idsewa = ? AND status = ? AND iddriver = ?'
                );
                $actorId = (int) $_SESSION['user_id'];
                $statusUpdate->bind_param('sisi', $nextStatus, $rentalId, $oldStatus, $actorId);
            } elseif ($passengerOwnsRequest) {
                $statusUpdate = $conn->prepare(
                    'UPDATE tbl_sewa SET status = ?
                     WHERE idsewa = ? AND status = ? AND idpenumpang = ?'
                );
                $actorId = (int) $_SESSION['user_id'];
                $statusUpdate->bind_param('sisi', $nextStatus, $rentalId, $oldStatus, $actorId);
            } else {
                $statusUpdate = $conn->prepare(
                    'UPDATE tbl_sewa SET status = ? WHERE idsewa = ? AND status = ?'
                );
                $statusUpdate->bind_param('sis', $nextStatus, $rentalId, $oldStatus);
            }
            $statusUpdate->execute();
            if ($statusUpdate->affected_rows !== 1) {
                $conn->rollback();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'error', 'Status permintaan baru saja berubah. Muat ulang halaman.');
            }

            if ($passengerOwnsRequest) {
                $notify = $conn->prepare(
                    'INSERT INTO tbl_notifikasi
                        (peran_penerima, idpenerima, idsewa, jenis, pesan)
                     VALUES (\'driver\', ?, ?, \'dibatalkan\', ?)'
                );
                $driverId = (int) $rentalToChange['iddriver'];
                $message = 'Penumpang membatalkan permintaan carter.';
                $notify->bind_param('iis', $driverId, $rentalId, $message);
            } else {
                $notify = $conn->prepare(
                    'INSERT INTO tbl_notifikasi
                        (peran_penerima, idpenerima, idsewa, jenis, pesan)
                     VALUES (\'penumpang\', ?, ?, ?, ?)'
                );
                $passengerId = (int) $rentalToChange['idpenumpang'];
                $notificationType = $nextStatus === 'approved' ? 'disetujui' : 'dibatalkan';
                $message = $nextStatus === 'approved'
                    ? 'Permintaan carter Anda telah disetujui.'
                    : 'Permintaan carter Anda telah dibatalkan.';
                $notify->bind_param('iiss', $passengerId, $rentalId, $notificationType, $message);
            }
            $notify->execute();
            $conn->commit();
            $transactionStarted = false;
            travel_flash_redirect(
                'carter',
                'success',
                $nextStatus === 'approved' ? 'Permintaan carter disetujui.' : 'Permintaan carter dibatalkan.'
            );
        }

        if ($action === 'update_rental' || $action === 'delete_rental') {
            if (!$isAdmin) {
                http_response_code(403);
                exit('Hanya admin yang dapat mengelola semua data carter.');
            }
            $rentalId = travel_positive_id($_POST['idsewa'] ?? null);
            if ($rentalId === null) {
                travel_flash_redirect('carter', 'error', 'Permintaan carter tidak valid.');
            }

            $conn->begin_transaction();
            $transactionStarted = true;
            $rentalLookup = $conn->prepare(
                'SELECT iddriver, idpenumpang, status FROM tbl_sewa WHERE idsewa = ? FOR UPDATE'
            );
            $rentalLookup->bind_param('i', $rentalId);
            $rentalLookup->execute();
            $rentalToUpdate = $rentalLookup->get_result()->fetch_assoc();
            if (!$rentalToUpdate) {
                $conn->rollback();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'error', 'Permintaan carter tidak ditemukan.');
            }

            if ($action === 'delete_rental') {
                $deleteDetails = $conn->prepare('DELETE FROM tbl_sewadetail WHERE idsewa = ?');
                $deleteDetails->bind_param('i', $rentalId);
                $deleteDetails->execute();
                $deleteRental = $conn->prepare('DELETE FROM tbl_sewa WHERE idsewa = ?');
                $deleteRental->bind_param('i', $rentalId);
                $deleteRental->execute();
                $conn->commit();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'success', 'Catatan carter berhasil dihapus.');
            }

            $driverId = travel_positive_id($_POST['iddriver'] ?? null);
            $passengerId = travel_positive_id($_POST['idpenumpang'] ?? null);
            $carId = travel_positive_id($_POST['idmobil'] ?? null);
            $rentalInput = isset($_POST['waktusewa']) && is_string($_POST['waktusewa'])
                ? $_POST['waktusewa']
                : '';
            $nextStatus = isset($_POST['status']) && is_string($_POST['status']) ? $_POST['status'] : '';
            $rentalTime = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $rentalInput);
            $dateErrors = DateTimeImmutable::getLastErrors();
            if ($driverId === null || $passengerId === null || $carId === null
                || !in_array($nextStatus, ['pending', 'approved', 'cancelled'], true)
                || $rentalTime === false
                || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                || $rentalTime->format('Y-m-d\TH:i') !== $rentalInput) {
                $conn->rollback();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'error', 'Data permintaan carter tidak valid.');
            }

            $driverCheck = $conn->prepare('SELECT iddriver FROM tbl_driver WHERE iddriver = ?');
            $driverCheck->bind_param('i', $driverId);
            $driverCheck->execute();
            $driverExists = $driverCheck->get_result()->fetch_assoc() !== null;
            $passengerCheck = $conn->prepare('SELECT idpenumpang FROM tbl__penumpang WHERE idpenumpang = ?');
            $passengerCheck->bind_param('i', $passengerId);
            $passengerCheck->execute();
            $passengerExists = $passengerCheck->get_result()->fetch_assoc() !== null;
            $carCheck = $conn->prepare('SELECT idmobil FROM tbl_mobil WHERE idmobil = ?');
            $carCheck->bind_param('i', $carId);
            $carCheck->execute();
            $carExists = $carCheck->get_result()->fetch_assoc() !== null;
            if (!$driverExists || !$passengerExists || !$carExists) {
                $conn->rollback();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'error', 'Driver, penumpang, atau mobil tidak ditemukan.');
            }

            $oldDriverId = (int) $rentalToUpdate['iddriver'];
            $oldPassengerId = (int) $rentalToUpdate['idpenumpang'];
            $oldStatus = (string) $rentalToUpdate['status'];
            $rentalDate = $rentalTime->format('Y-m-d H:i:s');
            $conflictCheck = $conn->prepare(
                'SELECT s.idsewa
                 FROM tbl_sewa s
                 JOIN tbl_sewadetail sd ON sd.idsewa = s.idsewa
                 WHERE sd.idmobil = ? AND s.status <> ? AND DATE(s.waktusewa) = DATE(?) AND s.idsewa <> ?
                 LIMIT 1'
            );
            $cancelledStatus = 'cancelled';
            $conflictCheck->bind_param('issi', $carId, $cancelledStatus, $rentalDate, $rentalId);
            $conflictCheck->execute();
            if ($conflictCheck->get_result()->fetch_assoc() !== null) {
                $conn->rollback();
                $transactionStarted = false;
                travel_flash_redirect('carter', 'error', 'Mobil yang dipilih sudah dipesan pada hari yang sama. Pilih mobil lain atau ubah tanggal sewa.');
            }
            $updateRental = $conn->prepare(
                'UPDATE tbl_sewa
                 SET iddriver = ?, idpenumpang = ?, waktusewa = ?, status = ?
                 WHERE idsewa = ?'
            );
            $updateRental->bind_param('iissi', $driverId, $passengerId, $rentalDate, $nextStatus, $rentalId);
            $updateRental->execute();

            $detailLookup = $conn->prepare('SELECT idsewadetail FROM tbl_sewadetail WHERE idsewa = ? LIMIT 1');
            $detailLookup->bind_param('i', $rentalId);
            $detailLookup->execute();
            $rentalDetail = $detailLookup->get_result()->fetch_assoc();
            if ($rentalDetail) {
                $detailId = (int) $rentalDetail['idsewadetail'];
                $updateDetail = $conn->prepare(
                    'UPDATE tbl_sewadetail SET idmobil = ? WHERE idsewadetail = ?'
                );
                $updateDetail->bind_param('ii', $carId, $detailId);
                $updateDetail->execute();
            } else {
                $insertDetail = $conn->prepare(
                    'INSERT INTO tbl_sewadetail (idsewa, idmobil) VALUES (?, ?)'
                );
                $insertDetail->bind_param('ii', $rentalId, $carId);
                $insertDetail->execute();
            }

            if ($nextStatus !== $oldStatus && in_array($nextStatus, ['approved', 'cancelled'], true)) {
                $notifyPassenger = $conn->prepare(
                    'INSERT INTO tbl_notifikasi
                        (peran_penerima, idpenerima, idsewa, jenis, pesan)
                     VALUES (\'penumpang\', ?, ?, ?, ?)'
                );
                $notificationType = $nextStatus === 'approved' ? 'disetujui' : 'dibatalkan';
                $message = $nextStatus === 'approved'
                    ? 'Permintaan carter Anda telah disetujui.'
                    : 'Permintaan carter Anda telah dibatalkan oleh admin.';
                $notifyPassenger->bind_param('iiss', $passengerId, $rentalId, $notificationType, $message);
                $notifyPassenger->execute();
            }
            if ($driverId !== $oldDriverId && $nextStatus === 'pending') {
                $notifyNewDriver = $conn->prepare(
                    'INSERT INTO tbl_notifikasi
                        (peran_penerima, idpenerima, idsewa, jenis, pesan)
                     VALUES (\'driver\', ?, ?, \'permintaan\', ?)'
                );
                $message = 'Permintaan carter baru ditugaskan kepada Anda.';
                $notifyNewDriver->bind_param('iis', $driverId, $rentalId, $message);
                $notifyNewDriver->execute();
            }
            if ($passengerId !== $oldPassengerId && $nextStatus !== 'cancelled') {
                $notifyOldPassenger = $conn->prepare(
                    'INSERT INTO tbl_notifikasi
                        (peran_penerima, idpenerima, idsewa, jenis, pesan)
                     VALUES (\'penumpang\', ?, ?, \'dibatalkan\', ?)'
                );
                $message = 'Permintaan carter Anda telah dialihkan oleh admin.';
                $notifyOldPassenger->bind_param('iis', $oldPassengerId, $rentalId, $message);
                $notifyOldPassenger->execute();
            }

            $conn->commit();
            $transactionStarted = false;
            travel_flash_redirect('carter', 'success', 'Data carter berhasil diperbarui.');
        }

        if (in_array($action, ['create_driver', 'update_driver', 'delete_driver'], true)) {
            if (!$isAdmin) {
                http_response_code(403);
                exit('Akses pengelolaan akun hanya tersedia untuk admin.');
            }

            if ($action === 'delete_driver') {
                $driverId = travel_positive_id($_POST['iddriver'] ?? null);
                if ($driverId === null) {
                    travel_flash_redirect('kelola', 'error', 'Akun driver tidak valid.');
                }
                $delete = $conn->prepare('DELETE FROM tbl_driver WHERE iddriver = ?');
                $delete->bind_param('i', $driverId);
                $delete->execute();
                travel_flash_redirect('kelola', 'success', 'Akun driver berhasil dihapus.');
            }

            $driverId = $action === 'update_driver' ? travel_positive_id($_POST['iddriver'] ?? null) : null;
            $driverUsername = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
            $driverPassword = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
            $driverPhone = isset($_POST['nomorhp']) && is_string($_POST['nomorhp']) ? trim($_POST['nomorhp']) : '';
            $driverSim = isset($_POST['nomorsim']) && is_string($_POST['nomorsim']) ? trim($_POST['nomorsim']) : '';
            $driverKtp = isset($_POST['nomorktp']) && is_string($_POST['nomorktp']) ? trim($_POST['nomorktp']) : '';
            $driverBirthDate = isset($_POST['tanggal_lahir']) && is_string($_POST['tanggal_lahir']) ? trim($_POST['tanggal_lahir']) : '';
            $driverBirthDateObject = DateTimeImmutable::createFromFormat('!Y-m-d', $driverBirthDate);
            if (!preg_match('/^[A-Za-z0-9._-]{3,25}$/D', $driverUsername)
                || strlen($driverPassword) > 72
                || ($driverPassword !== '' && strlen($driverPassword) < 6)
                || ($action === 'create_driver' && strlen($driverPassword) < 6)
                || $driverPhone === '' || strlen($driverPhone) > 20
                || $driverSim === '' || strlen($driverSim) > 16
                || $driverKtp === '' || strlen($driverKtp) > 16
                || $driverBirthDate === ''
                || $driverBirthDateObject === false
                || $driverBirthDateObject->format('Y-m-d') !== $driverBirthDate) {
                travel_flash_redirect('kelola', 'error', 'Lengkapi data driver dengan format yang valid.');
            }

            if ($action === 'create_driver') {
                $passwordHash = password_hash($driverPassword, PASSWORD_DEFAULT);
                $insert = $conn->prepare(
                    'INSERT INTO tbl_driver
                        (usernamedriver, passworddriver, nomorhp, nomorsim, nomorktp, tanggal_lahir)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $insert->bind_param(
                    'ssssss',
                    $driverUsername,
                    $passwordHash,
                    $driverPhone,
                    $driverSim,
                    $driverKtp,
                    $driverBirthDate
                );
                $insert->execute();
            } else {
                if ($driverId === null) {
                    travel_flash_redirect('kelola', 'error', 'Akun driver tidak valid.');
                }
                if ($driverPassword !== '') {
                    $passwordHash = password_hash($driverPassword, PASSWORD_DEFAULT);
                    $update = $conn->prepare(
                        'UPDATE tbl_driver SET usernamedriver = ?, passworddriver = ?, nomorhp = ?,
                            nomorsim = ?, nomorktp = ?, tanggal_lahir = ? WHERE iddriver = ?'
                    );
                    $update->bind_param(
                        'ssssssi',
                        $driverUsername,
                        $passwordHash,
                        $driverPhone,
                        $driverSim,
                        $driverKtp,
                        $driverBirthDate,
                        $driverId
                    );
                } else {
                    $update = $conn->prepare(
                        'UPDATE tbl_driver SET usernamedriver = ?, nomorhp = ?, nomorsim = ?,
                            nomorktp = ?, tanggal_lahir = ? WHERE iddriver = ?'
                    );
                    $update->bind_param(
                        'sssssi',
                        $driverUsername,
                        $driverPhone,
                        $driverSim,
                        $driverKtp,
                        $driverBirthDate,
                        $driverId
                    );
                }
                $update->execute();
            }
            travel_flash_redirect('kelola', 'success', 'Data driver berhasil disimpan.');
        }

        if (in_array($action, ['create_passenger', 'update_passenger', 'delete_passenger'], true)) {
            if (!$isAdmin) {
                http_response_code(403);
                exit('Akses pengelolaan akun hanya tersedia untuk admin.');
            }

            if ($action === 'delete_passenger') {
                $passengerId = travel_positive_id($_POST['idpenumpang'] ?? null);
                if ($passengerId === null) {
                    travel_flash_redirect('kelola', 'error', 'Akun penumpang tidak valid.');
                }
                $delete = $conn->prepare('DELETE FROM tbl__penumpang WHERE idpenumpang = ?');
                $delete->bind_param('i', $passengerId);
                $delete->execute();
                travel_flash_redirect('kelola', 'success', 'Akun penumpang berhasil dihapus.');
            }

            $passengerId = $action === 'update_passenger' ? travel_positive_id($_POST['idpenumpang'] ?? null) : null;
            $passengerUsername = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
            $passengerPassword = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
            $passengerPhone = isset($_POST['nomorhp']) && is_string($_POST['nomorhp']) ? trim($_POST['nomorhp']) : '';
            if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/D', $passengerUsername)
                || strlen($passengerPassword) > 72
                || ($passengerPassword !== '' && strlen($passengerPassword) < 6)
                || ($action === 'create_passenger' && strlen($passengerPassword) < 6)
                || $passengerPhone === '' || strlen($passengerPhone) > 25) {
                travel_flash_redirect('kelola', 'error', 'Lengkapi data penumpang dengan format yang valid.');
            }

            if ($action === 'create_passenger') {
                $passwordHash = password_hash($passengerPassword, PASSWORD_DEFAULT);
                $insert = $conn->prepare(
                    'INSERT INTO tbl__penumpang (usernamepenumpang, passwordpenumpang, nomorhp)
                     VALUES (?, ?, ?)'
                );
                $insert->bind_param('sss', $passengerUsername, $passwordHash, $passengerPhone);
                $insert->execute();
            } else {
                if ($passengerId === null) {
                    travel_flash_redirect('kelola', 'error', 'Akun penumpang tidak valid.');
                }
                if ($passengerPassword !== '') {
                    $passwordHash = password_hash($passengerPassword, PASSWORD_DEFAULT);
                    $update = $conn->prepare(
                        'UPDATE tbl__penumpang
                         SET usernamepenumpang = ?, passwordpenumpang = ?, nomorhp = ?
                         WHERE idpenumpang = ?'
                    );
                    $update->bind_param('sssi', $passengerUsername, $passwordHash, $passengerPhone, $passengerId);
                } else {
                    $update = $conn->prepare(
                        'UPDATE tbl__penumpang SET usernamepenumpang = ?, nomorhp = ?
                         WHERE idpenumpang = ?'
                    );
                    $update->bind_param('ssi', $passengerUsername, $passengerPhone, $passengerId);
                }
                $update->execute();
            }
            travel_flash_redirect('kelola', 'success', 'Data penumpang berhasil disimpan.');
        }

        travel_flash_redirect('home', 'error', 'Aksi tidak dikenali. Muat ulang halaman lalu coba lagi.');
    } catch (mysqli_sql_exception $exception) {
        error_log('Perubahan data travel gagal: ' . $exception->getMessage());
        if ($transactionStarted) {
            try {
                $conn->rollback();
            } catch (mysqli_sql_exception $rollbackException) {
                error_log('Rollback perubahan data travel gagal: ' . $rollbackException->getMessage());
            }
        }
        if ((int) $exception->getCode() === 1451 && $action === 'delete_car') {
            travel_flash_redirect('mobil', 'error', 'Mobil tidak dapat dihapus karena sudah tercatat pada carter.');
        }
        if ((int) $exception->getCode() === 1451
            && in_array($action, ['delete_driver', 'delete_passenger'], true)) {
            travel_flash_redirect('kelola', 'error', 'Akun tidak dapat dihapus karena masih memiliki riwayat carter.');
        }
        $errorPage = str_contains($action, 'driver') || str_contains($action, 'passenger')
            ? 'kelola'
            : (str_contains($action, 'rental') ? 'carter' : 'mobil');
        travel_flash_redirect($errorPage, 'error', 'Perubahan belum tersimpan. Periksa data lalu coba lagi.');
    }
}

if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
    http_response_code(405);
    exit('Metode permintaan tidak diizinkan.');
}

$profile = null;
$cars = [];
$drivers = [];
$managementDrivers = [];
$managementPassengers = [];
$rentals = [];
$dataError = '';
$editCar = null;

try {
    if ($role === 'admin') {
        $profileStatement = $conn->prepare(
            'SELECT username FROM tbl_admin WHERE idadmin = ?'
        );
    } elseif ($role === 'driver') {
        $profileStatement = $conn->prepare(
            'SELECT usernamedriver AS username, nomorhp, nomorsim, nomorktp, tanggal_lahir, gambar
             FROM tbl_driver WHERE iddriver = ?'
        );
    } else {
        $profileStatement = $conn->prepare(
            'SELECT usernamepenumpang AS username, nomorhp, gambar
             FROM tbl__penumpang WHERE idpenumpang = ?'
        );
    }
    $userId = (int) $_SESSION['user_id'];
    $profileStatement->bind_param('i', $userId);
    $profileStatement->execute();
    $profile = $profileStatement->get_result()->fetch_assoc();
    if (!$profile) {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: login.php');
        exit;
    }

    $carResult = $conn->query(
        'SELECT idmobil, merkmobil, namamobil, kategori, bid_harga, nomorplat, gambar
         FROM tbl_mobil ORDER BY merkmobil, namamobil, idmobil'
    );
    $cars = $carResult->fetch_all(MYSQLI_ASSOC);

    if ($role === 'penumpang' || $isAdmin) {
        $driverResult = $conn->query(
            'SELECT iddriver, usernamedriver, nomorhp
             FROM tbl_driver ORDER BY usernamedriver'
        );
        $drivers = $driverResult->fetch_all(MYSQLI_ASSOC);
    }

    if ($isAdmin) {
        $managementDrivers = $conn->query(
            'SELECT iddriver, usernamedriver, nomorhp, nomorsim, nomorktp, tanggal_lahir
             FROM tbl_driver ORDER BY usernamedriver'
        )->fetch_all(MYSQLI_ASSOC);
        $managementPassengers = $conn->query(
            'SELECT idpenumpang, usernamepenumpang, nomorhp
             FROM tbl__penumpang ORDER BY usernamepenumpang'
        )->fetch_all(MYSQLI_ASSOC);
    }

    if ($page === 'carter') {
        if ($role === 'admin') {
            $rentalStatement = $conn->prepare(
                'SELECT s.idsewa, s.iddriver, s.idpenumpang, s.status, s.waktusewa, sd.idmobil,
                        p.usernamepenumpang, p.nomorhp AS nomorhp_penumpang,
                        d.usernamedriver, d.nomorhp AS nomorhp_driver,
                        m.merkmobil, m.namamobil, m.kategori, m.bid_harga, m.nomorplat
                 FROM tbl_sewa s
                 JOIN tbl__penumpang p ON p.idpenumpang = s.idpenumpang
                 JOIN tbl_driver d ON d.iddriver = s.iddriver
                 LEFT JOIN tbl_sewadetail sd ON sd.idsewa = s.idsewa
                 LEFT JOIN tbl_mobil m ON m.idmobil = sd.idmobil
                 ORDER BY s.waktusewa DESC, s.idsewa DESC'
            );
            $rentalStatement->execute();
            $rentals = $rentalStatement->get_result()->fetch_all(MYSQLI_ASSOC);
        } elseif ($role === 'driver') {
            $rentalStatement = $conn->prepare(
                'SELECT s.idsewa, s.iddriver, s.idpenumpang, s.status, s.waktusewa, sd.idmobil,
                        p.usernamepenumpang, p.nomorhp AS nomorhp_penumpang,
                        d.usernamedriver, m.merkmobil, m.namamobil, m.kategori, m.bid_harga, m.nomorplat
                 FROM tbl_sewa s
                 JOIN tbl__penumpang p ON p.idpenumpang = s.idpenumpang
                 JOIN tbl_driver d ON d.iddriver = s.iddriver
                 LEFT JOIN tbl_sewadetail sd ON sd.idsewa = s.idsewa
                 LEFT JOIN tbl_mobil m ON m.idmobil = sd.idmobil
                 WHERE s.iddriver = ?
                 ORDER BY s.waktusewa DESC, s.idsewa DESC'
            );
        } else {
            $rentalStatement = $conn->prepare(
                'SELECT s.idsewa, s.iddriver, s.idpenumpang, s.status, s.waktusewa, sd.idmobil,
                        p.usernamepenumpang, d.usernamedriver, d.nomorhp AS nomorhp_driver,
                        m.merkmobil, m.namamobil, m.kategori, m.bid_harga, m.nomorplat
                 FROM tbl_sewa s
                 JOIN tbl__penumpang p ON p.idpenumpang = s.idpenumpang
                 JOIN tbl_driver d ON d.iddriver = s.iddriver
                 LEFT JOIN tbl_sewadetail sd ON sd.idsewa = s.idsewa
                 LEFT JOIN tbl_mobil m ON m.idmobil = sd.idmobil
                 WHERE s.idpenumpang = ?
                 ORDER BY s.waktusewa DESC, s.idsewa DESC'
            );
        }
        if ($role !== 'admin') {
            $rentalStatement->bind_param('i', $userId);
            $rentalStatement->execute();
            $rentals = $rentalStatement->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }

    if ($isAdmin && $page === 'mobil' && isset($_GET['edit'])) {
        $editId = travel_positive_id($_GET['edit']);
        foreach ($cars as $car) {
            if ((int) $car['idmobil'] === $editId) {
                $editCar = $car;
                break;
            }
        }
    }
} catch (mysqli_sql_exception $exception) {
    error_log('Pembacaan data travel gagal: ' . $exception->getMessage());
    $dataError = 'Data belum dapat dimuat. Periksa koneksi dan struktur basis data travel, lalu muat ulang halaman.';
}

$displayPage = $page === 'home'
    ? 'Beranda'
    : ($page === 'mobil' ? 'Armada' : ($page === 'kelola' ? 'Kelola akun' : 'Catatan carter'));
$roleLabel = match ($role) {
    'admin' => 'Admin',
    'driver' => 'Driver',
    default => 'Penumpang',
};
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= h($displayPage) ?> | T-RavelGuard.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body data-theme="light">
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <div class="app-shell">
      <header class="topbar">
        <a class="brand" href="index.php" aria-label="T-RavelGuard., beranda">
          <span>T-RavelGuard.</span>
        </a>
        <div class="account-tools">
          <span class="account-label"><?= h($roleLabel) ?> · <?= h($_SESSION['username']) ?></span>
          <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false">
            <span class="theme-toggle-track">
              <span class="theme-toggle-thumb"></span>
            </span>
            <span class="theme-toggle-text">Light</span>
          </button>
          <form method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
            <button class="button button-quiet" type="submit">Keluar</button>
          </form>
        </div>
      </header>

      <nav class="main-nav" aria-label="Navigasi utama">
        <a href="index.php" <?= $page === 'home' ? 'aria-current="page"' : '' ?>>Beranda</a>
        <a href="index.php?halaman=mobil" <?= $page === 'mobil' ? 'aria-current="page"' : '' ?>>Armada</a>
        <a href="index.php?halaman=carter" <?= $page === 'carter' ? 'aria-current="page"' : '' ?>>
          <?= $role === 'driver' ? 'Carter masuk' : ($isAdmin ? 'Semua carter' : 'Carter saya') ?>
        </a>
        <?php if ($isAdmin): ?>
          <a href="index.php?halaman=kelola" <?= $page === 'kelola' ? 'aria-current="page"' : '' ?>>Kelola akun</a>
        <?php endif; ?>
      </nav>

      <main id="main-content" class="page-content">
        <p id="page-loading" class="loading-state" role="status" hidden>Memuat data halaman...</p>
        <section
          class="notification-region"
          aria-label="Notifikasi"
          aria-live="polite"
          aria-relevant="additions"
          data-csrf-token="<?= h(auth_csrf_token()) ?>"
        >
          <ol id="notification-list"></ol>
          <p id="notification-empty" class="notification-empty" role="status">Memeriksa notifikasi...</p>
          <p id="notification-error" class="notice notice-error" role="alert" hidden>
            Notifikasi belum dapat dimuat. Sistem akan mencoba lagi.
          </p>
        </section>
        <?php if ($flash && isset($flash['message'], $flash['type'])): ?>
          <p class="notice notice-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="<?= $flash['type'] === 'success' ? 'status' : 'alert' ?>"><?= h($flash['message']) ?></p>
        <?php endif; ?>
        <?php if ($dataError !== ''): ?>
          <p class="notice notice-error" role="alert"><?= h($dataError) ?></p>
        <?php endif; ?>

        <?php if ($page === 'home'): ?>
          <section class="welcome-panel" aria-labelledby="page-title">
            <div>
              <p class="eyebrow">Ruang kerja <?= h($roleLabel) ?></p>
              <h1 id="page-title">Halo, <?= h($_SESSION['username']) ?>.</h1>
              <?php if ($isAdmin): ?>
                <p>Kelola akun, armada, dan semua permintaan carter dari satu tempat.</p>
                <a class="button button-primary" href="index.php?halaman=kelola">Kelola akun</a>
              <?php elseif ($role === 'driver'): ?>
                <p>Lihat armada dan putuskan permintaan carter yang ditujukan kepada Anda.</p>
                <a class="button button-primary" href="index.php?halaman=carter">Lihat carter masuk</a>
              <?php else: ?>
                <p>Buat catatan carter dengan memilih driver, mobil, dan waktu sewa.</p>
                <a class="button button-primary" href="index.php?halaman=carter">Buat catatan carter</a>
              <?php endif; ?>
            </div>
            <div class="route-mark" aria-hidden="true">
              <span class="route-point"></span>
              <span class="route-line"></span>
              <span class="route-point route-point-end"></span>
            </div>
          </section>

          <section class="profile-panel" aria-labelledby="profile-title">
            <div class="section-heading">
              <div>
                <p class="eyebrow">Data akun</p>
                <h2 id="profile-title">Profil <?= h($roleLabel) ?></h2>
              </div>
            </div>
            <?php if ($profile): ?>
              <dl class="profile-details">
                <div><dt>Username</dt><dd><?= h($profile['username']) ?></dd></div>
                <?php if (isset($profile['nomorhp'])): ?>
                  <div><dt>Nomor HP</dt><dd><?= h($profile['nomorhp']) ?></dd></div>
                <?php endif; ?>
                <?php if ($role === 'driver'): ?>
                  <div><dt>Nomor SIM</dt><dd><?= h($profile['nomorsim']) ?></dd></div>
                  <div><dt>Nomor KTP</dt><dd><?= h($profile['nomorktp']) ?></dd></div>
                  <div><dt>Tanggal lahir</dt><dd><?= h($profile['tanggal_lahir']) ?></dd></div>
                  <?php if (isset($profile['gambar']) && $profile['gambar'] !== ''): ?>
                    <div class="profile-image-block"><dt>Foto</dt><dd><?= travel_render_image_tag($profile['gambar'], 'Foto profil', 'profile-photo') ?></dd></div>
                  <?php endif; ?>
                <?php endif; ?>
              </dl>
            <?php endif; ?>
          </section>

        <?php elseif ($page === 'mobil'): ?>
          <header class="page-heading">
            <p class="eyebrow">Daftar kendaraan</p>
            <h1>Armada</h1>
            <p>Data mobil yang dapat dipilih saat membuat catatan carter.</p>
          </header>

          <?php if ($canManage): ?>
            <section id="fleet-form" class="form-panel" aria-labelledby="fleet-form-title">
              <div class="section-heading">
                <div>
                  <p class="eyebrow"><?= $editCar ? 'Perbarui data' : 'Tambahkan kendaraan' ?></p>
                  <h2 id="fleet-form-title"><?= $editCar ? 'Ubah data mobil' : 'Masukkan mobil ke daftar' ?></h2>
                </div>
              </div>
              <p class="supporting-copy">Daftar kendaraan bersama yang tersedia untuk permintaan carter.</p>
              <form class="data-form" method="post" data-pending-form>
                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                <input type="hidden" name="action" value="<?= $editCar ? 'update_car' : 'create_car' ?>" />
                <?php if ($editCar): ?>
                  <input type="hidden" name="idmobil" value="<?= h($editCar['idmobil']) ?>" />
                <?php endif; ?>
                <div class="form-grid">
                  <label for="merkmobil">Merek mobil
                    <input id="merkmobil" name="merkmobil" maxlength="20" value="<?= h($editCar['merkmobil'] ?? '') ?>" required />
                  </label>
                  <label for="namamobil">Nama / model mobil
                    <input id="namamobil" name="namamobil" maxlength="50" value="<?= h($editCar['namamobil'] ?? '') ?>" required />
                  </label>
                  <label for="kategori">Kategori mobil
                    <select id="kategori" name="kategori" required>
                      <option value="">Pilih kategori</option>
                      <?php foreach (travel_car_category_options() as $categoryValue => $categoryLabel): ?>
                        <option value="<?= h($categoryValue) ?>" <?= (($editCar['kategori'] ?? '') === $categoryValue) ? 'selected' : '' ?>><?= h($categoryLabel) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </label>
                  <label for="bid_harga">Aproksimasi biaya
                    <input
                      id="bid_harga"
                      name="bid_harga"
                      type="text"
                      inputmode="numeric"
                      pattern="[0-9]{1,7}"
                      maxlength="7"
                      value="<?= h((string) ($editCar['bid_harga'] ?? 0)) ?>"
                      required
                    />
                  </label>
                  <label for="nomorplat">Nomor plat
                    <input id="nomorplat" name="nomorplat" maxlength="12" value="<?= h($editCar['nomorplat'] ?? '') ?>" required />
                  </label>
                  <label for="gambar">URL gambar mobil (Opsional)
                    <input id="gambar" name="gambar" type="url" maxlength="255" value="<?= h($editCar['gambar'] ?? '') ?>" placeholder="https://example.com/foto-mobil.jpg" />
                  </label>
                </div>
                <div class="form-actions">
                  <button class="button button-primary" type="submit"><?= $editCar ? 'Simpan perubahan' : 'Tambahkan mobil' ?></button>
                  <?php if ($editCar): ?><a class="button button-secondary" href="index.php?halaman=mobil">Batal</a><?php endif; ?>
                  <span class="form-pending" role="status" hidden>Menyimpan data mobil...</span>
                </div>
              </form>
            </section>
          <?php endif; ?>

          <section class="list-section" aria-labelledby="fleet-list-title">
            <div class="section-heading">
              <div>
                <p class="eyebrow">Kendaraan tercatat</p>
                <h2 id="fleet-list-title">Daftar mobil</h2>
              </div>
            </div>
            <?php if (!$dataError && !$cars): ?>
              <p class="empty-state">Belum ada kendaraan di daftar armada.</p>
            <?php elseif (!$dataError): ?>
              <div class="table-wrap">
                <table class="data-table" aria-label="Daftar mobil admin">
                  <thead>
                    <tr>
                      <th>Mobil</th>
                      <th>Kategori</th>
                      <th>Bid harga</th>
                      <th>Plat</th>
                      <?php if ($canManage): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($cars as $car): ?>
                      <tr>
                        <td>
                          <?= travel_render_image_tag($car['gambar'] ?? null, h($car['merkmobil'] . ' ' . $car['namamobil']), 'car-thumb') ?>
                          <div class="vehicle-details">
                            <strong><?= h($car['merkmobil']) ?> <?= h($car['namamobil']) ?></strong>
                          </div>
                        </td>
                        <td><?= h(travel_car_category_label((string) ($car['kategori'] ?? ''))) ?></td>
                        <td><?= h(travel_format_currency((string) ($car['bid_harga'] ?? 0))) ?></td>
                        <td><?= h($car['nomorplat']) ?></td>
                        <?php if ($canManage): ?>
                          <td>
                            <div class="record-actions compact-actions">
                              <a class="button button-secondary" href="index.php?halaman=mobil&amp;edit=<?= h($car['idmobil']) ?>#fleet-form">Ubah</a>
                              <form method="post" data-pending-form data-confirm="Hapus mobil ini dari daftar armada?">
                                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                                <input type="hidden" name="action" value="delete_car" />
                                <input type="hidden" name="idmobil" value="<?= h($car['idmobil']) ?>" />
                                <button class="button button-danger" type="submit">Hapus</button>
                                <span class="form-pending" role="status" hidden>Menghapus data mobil...</span>
                              </form>
                            </div>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </section>

        <?php elseif ($page === 'kelola'): ?>
          <header class="page-heading">
            <p class="eyebrow">Data akun</p>
            <h1>Kelola akun</h1>
            <p>Admin dapat melihat, menambah, mengubah, dan menghapus akun driver serta penumpang.</p>
          </header>

          <?php if ($dataError === ''): ?>
            <section class="form-panel" aria-labelledby="driver-management-title">
              <div class="section-heading">
                <div>
                  <p class="eyebrow">Akun layanan</p>
                  <h2 id="driver-management-title">Driver</h2>
                </div>
              </div>
              <details class="account-editor">
                <summary>Tambah akun driver</summary>
                <form class="data-form" method="post" data-pending-form>
                  <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                  <input type="hidden" name="action" value="create_driver" />
                  <div class="form-grid">
                    <label>Username<input name="username" minlength="3" maxlength="25" required /></label>
                    <label>Kata sandi<input name="password" type="password" minlength="6" maxlength="72" required /></label>
                    <label>Nomor HP<input name="nomorhp" maxlength="20" required /></label>
                    <label>Nomor SIM<input name="nomorsim" maxlength="16" required /></label>
                    <label>Nomor KTP<input name="nomorktp" maxlength="16" required /></label>
                    <label>Tanggal lahir<input name="tanggal_lahir" type="date" required /></label>
                  </div>
                  <div class="form-actions">
                    <button class="button button-primary" type="submit">Tambah driver</button>
                    <span class="form-pending" role="status" hidden>Menyimpan akun...</span>
                  </div>
                </form>
              </details>
              <?php if (!$managementDrivers): ?>
                <p class="empty-state">Belum ada akun driver.</p>
              <?php else: ?>
                <div class="table-wrap">
                  <table class="data-table" aria-label="Daftar akun driver">
                    <thead>
                      <tr>
                        <th>Username</th>
                        <th>Nomor HP</th>
                        <th>Nomor SIM</th>
                        <th>Nomor KTP</th>
                        <th>Tanggal lahir</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($managementDrivers as $driver): ?>
                        <tr>
                          <td><?= h($driver['usernamedriver']) ?></td>
                          <td><?= h($driver['nomorhp']) ?></td>
                          <td><?= h($driver['nomorsim']) ?></td>
                          <td><?= h($driver['nomorktp']) ?></td>
                          <td><?= h($driver['tanggal_lahir']) ?></td>
                          <td>
                            <details class="account-editor">
                              <summary><?= h($driver['usernamedriver']) ?></summary>
                              <form class="data-form" method="post" data-pending-form>
                                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                                <input type="hidden" name="action" value="update_driver" />
                                <input type="hidden" name="iddriver" value="<?= h($driver['iddriver']) ?>" />
                                <div class="form-grid">
                                  <label>Username<input name="username" value="<?= h($driver['usernamedriver']) ?>" minlength="3" maxlength="25" required /></label>
                                  <label>Kata sandi baru (opsional)<input name="password" type="password" minlength="6" maxlength="72" autocomplete="new-password" /></label>
                                  <label>Nomor HP<input name="nomorhp" value="<?= h($driver['nomorhp']) ?>" maxlength="20" required /></label>
                                  <label>Nomor SIM<input name="nomorsim" value="<?= h($driver['nomorsim']) ?>" maxlength="16" required /></label>
                                  <label>Nomor KTP<input name="nomorktp" value="<?= h($driver['nomorktp']) ?>" maxlength="16" required /></label>
                                  <label>Tanggal lahir<input name="tanggal_lahir" type="date" value="<?= h($driver['tanggal_lahir']) ?>" required /></label>
                                </div>
                                <div class="form-actions">
                                  <button class="button button-primary" type="submit">Simpan perubahan</button>
                                  <span class="form-pending" role="status" hidden>Menyimpan perubahan...</span>
                                </div>
                              </form>
                              <form method="post" data-pending-form data-confirm="Hapus akun driver ini? Permintaan carter terkait dapat mencegah penghapusan.">
                                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                                <input type="hidden" name="action" value="delete_driver" />
                                <input type="hidden" name="iddriver" value="<?= h($driver['iddriver']) ?>" />
                                <button class="button button-danger" type="submit">Hapus akun driver</button>
                              </form>
                            </details>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </section>

            <section class="form-panel" aria-labelledby="passenger-management-title">
              <div class="section-heading">
                <div>
                  <p class="eyebrow">Akun layanan</p>
                  <h2 id="passenger-management-title">Penumpang</h2>
                </div>
              </div>
              <details class="account-editor">
                <summary>Tambah akun penumpang</summary>
                <form class="data-form" method="post" data-pending-form>
                  <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                  <input type="hidden" name="action" value="create_passenger" />
                  <div class="form-grid">
                    <label>Username<input name="username" minlength="3" maxlength="50" required /></label>
                    <label>Kata sandi<input name="password" type="password" minlength="6" maxlength="72" required /></label>
                    <label>Nomor HP<input name="nomorhp" maxlength="25" required /></label>
                  </div>
                  <div class="form-actions">
                    <button class="button button-primary" type="submit">Tambah penumpang</button>
                    <span class="form-pending" role="status" hidden>Menyimpan akun...</span>
                  </div>
                </form>
              </details>
              <?php if (!$managementPassengers): ?>
                <p class="empty-state">Belum ada akun penumpang.</p>
              <?php else: ?>
                <div class="table-wrap">
                  <table class="data-table" aria-label="Daftar akun penumpang">
                    <thead>
                      <tr>
                        <th>Username</th>
                        <th>Nomor HP</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($managementPassengers as $passenger): ?>
                        <tr>
                          <td><?= h($passenger['usernamepenumpang']) ?></td>
                          <td><?= h($passenger['nomorhp']) ?></td>
                          <td>
                            <details class="account-editor">
                              <summary><?= h($passenger['usernamepenumpang']) ?></summary>
                              <form class="data-form" method="post" data-pending-form>
                                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                                <input type="hidden" name="action" value="update_passenger" />
                                <input type="hidden" name="idpenumpang" value="<?= h($passenger['idpenumpang']) ?>" />
                                <div class="form-grid">
                                  <label>Username<input name="username" value="<?= h($passenger['usernamepenumpang']) ?>" minlength="3" maxlength="50" required /></label>
                                  <label>Kata sandi baru (opsional)<input name="password" type="password" minlength="6" maxlength="72" autocomplete="new-password" /></label>
                                  <label>Nomor HP<input name="nomorhp" value="<?= h($passenger['nomorhp']) ?>" maxlength="25" required /></label>
                                </div>
                                <div class="form-actions">
                                  <button class="button button-primary" type="submit">Simpan perubahan</button>
                                  <span class="form-pending" role="status" hidden>Menyimpan perubahan...</span>
                                </div>
                              </form>
                              <form method="post" data-pending-form data-confirm="Hapus akun penumpang ini? Permintaan carter terkait dapat mencegah penghapusan.">
                                <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                                <input type="hidden" name="action" value="delete_passenger" />
                                <input type="hidden" name="idpenumpang" value="<?= h($passenger['idpenumpang']) ?>" />
                                <button class="button button-danger" type="submit">Hapus akun penumpang</button>
                              </form>
                            </details>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </section>
          <?php endif; ?>

        <?php else: ?>
          <header class="page-heading">
            <p class="eyebrow"><?= $role === 'driver' ? 'Jadwal yang tercatat' : ($isAdmin ? 'Semua permintaan' : 'Pemesanan Anda') ?></p>
            <h1><?= $role === 'driver' ? 'Carter masuk' : ($isAdmin ? 'Semua carter' : 'Carter saya') ?></h1>
            <p>Status permintaan diperbarui saat driver menyetujui atau membatalkan carter.</p>
          </header>

          <?php if ($role === 'penumpang' || $isAdmin): ?>
            <section class="form-panel" aria-labelledby="rental-form-title">
              <div class="section-heading">
                <div>
                  <p class="eyebrow"><?= $isAdmin ? 'Admin' : 'Penumpang' ?></p>
                  <h2 id="rental-form-title">Buat catatan carter</h2>
                </div>
              </div>
              <?php if (!$dataError && (!$drivers || !$cars || ($isAdmin && !$managementPassengers))): ?>
                <p class="empty-state">Carter belum dapat dibuat karena data penumpang, driver, atau armada belum tersedia.</p>
              <?php elseif (!$dataError): ?>
                <form class="data-form" method="post" data-pending-form>
                  <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                  <input type="hidden" name="action" value="create_rental" />
                  <div class="form-grid">
                    <?php if ($isAdmin): ?>
                      <label for="idpenumpang">Penumpang
                        <select id="idpenumpang" name="idpenumpang" required>
                          <option value="">Pilih penumpang</option>
                          <?php foreach ($managementPassengers as $passenger): ?>
                            <option value="<?= h($passenger['idpenumpang']) ?>"><?= h($passenger['usernamepenumpang']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                    <?php endif; ?>
                    <label for="iddriver">Driver
                      <select id="iddriver" name="iddriver" required>
                        <option value="">Pilih driver</option>
                        <?php foreach ($drivers as $driver): ?>
                          <option value="<?= h($driver['iddriver']) ?>"><?= h($driver['usernamedriver']) ?> · <?= h($driver['nomorhp']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </label>
                    <label for="idmobil">Mobil
                      <select id="idmobil" name="idmobil" class="rental-car-select" required>
                        <option value="">Pilih mobil</option>
                        <?php foreach ($cars as $car): ?>
                          <option value="<?= h($car['idmobil']) ?>" data-kategori="<?= h((string) ($car['kategori'] ?? '')) ?>" data-bid-harga="<?= h((string) ($car['bid_harga'] ?? 0)) ?>"><?= h($car['merkmobil']) ?> <?= h($car['namamobil']) ?> · <?= h(travel_car_category_label((string) ($car['kategori'] ?? ''))) ?> · <?= h($car['nomorplat']) ?> · <?= h(travel_format_currency((string) ($car['bid_harga'] ?? 0))) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </label>
                    <label for="waktusewa">Waktu sewa
                      <div class="datetime-field">
                        <input id="waktusewa" name="waktusewa" type="datetime-local" required />
                        <button class="datetime-picker-trigger" type="button" aria-label="Pilih tanggal dan waktu sewa">
                          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M7 2.5v2.2M17 2.5v2.2M3.5 8.2h17M5 5.2h14a1.8 1.8 0 0 1 1.8 1.8v11a1.8 1.8 0 0 1-1.8 1.8H5A1.8 1.8 0 0 1 3.2 18V7a1.8 1.8 0 0 1 1.8-1.8Z"/>
                          </svg>
                        </button>
                      </div>
                    </label>
                  </div>
                  <div class="fleet-summary" id="rental-car-summary" aria-live="polite">
                    <strong>Detail mobil:</strong>
                    <span>Belum ada mobil dipilih.</span>
                  </div>
                  <div class="form-actions">
                    <button class="button button-primary" type="submit">Kirim permintaan carter</button>
                    <span class="form-pending" role="status" hidden>Mengirim permintaan carter...</span>
                  </div>
                </form>
              <?php endif; ?>
            </section>
          <?php endif; ?>

          <section class="list-section" aria-labelledby="rental-list-title">
            <div class="section-heading">
              <div>
                <p class="eyebrow">Riwayat tersimpan</p>
                <h2 id="rental-list-title">
                  <?= $role === 'driver' ? 'Carter yang memilih Anda' : ($isAdmin ? 'Semua permintaan carter' : 'Catatan carter Anda') ?>
                </h2>
              </div>
            </div>
            <?php if (!$dataError && !$rentals): ?>
              <p class="empty-state">
                <?= $role === 'driver'
                    ? 'Belum ada penumpang yang mengirim permintaan carter untuk akun Anda.'
                    : ($isAdmin ? 'Belum ada permintaan carter.' : 'Anda belum membuat permintaan carter. Formulir di atas akan menyimpannya di sini.') ?>
              </p>
            <?php elseif (!$dataError): ?>
              <div class="record-list">
                <?php foreach ($rentals as $rental): ?>
                  <article class="record-card" id="sewa-<?= h($rental['idsewa']) ?>">
                    <div class="record-main">
                      <h3><?= h(trim((string) ($rental['merkmobil'] ?? '') . ' ' . (string) ($rental['namamobil'] ?? '')) ?: 'Mobil tidak tercatat') ?></h3>
                      <dl class="record-details">
                        <div><dt>Waktu sewa</dt><dd><time datetime="<?= h(str_replace(' ', 'T', (string) $rental['waktusewa'])) ?>"><?= h(travel_format_datetime((string) $rental['waktusewa'])) ?></time></dd></div>
                        <?php if ($role === 'driver'): ?>
                          <div><dt>Penumpang</dt><dd><?= h($rental['usernamepenumpang']) ?></dd></div>
                          <div><dt>Nomor HP penumpang</dt><dd><?= h($rental['nomorhp_penumpang']) ?></dd></div>
                        <?php elseif ($role === 'penumpang'): ?>
                          <div><dt>Driver</dt><dd><?= h($rental['usernamedriver']) ?></dd></div>
                          <div><dt>Nomor HP driver</dt><dd><?= h($rental['nomorhp_driver']) ?></dd></div>
                        <?php endif; ?>
                        <div><dt>Kategori</dt><dd><?= h(travel_car_category_label((string) ($rental['kategori'] ?? ''))) ?></dd></div>
                        <div><dt>Bid harga</dt><dd><?= h(travel_format_currency((string) ($rental['bid_harga'] ?? 0))) ?></dd></div>
                        <div><dt>Nomor plat</dt><dd><?= h($rental['nomorplat'] ?? 'Belum tercatat') ?></dd></div>
                        <?php if ($isAdmin): ?>
                          <div><dt>Penumpang</dt><dd><?= h($rental['usernamepenumpang']) ?> · <?= h($rental['nomorhp_penumpang']) ?></dd></div>
                          <div><dt>Driver</dt><dd><?= h($rental['usernamedriver']) ?> · <?= h($rental['nomorhp_driver']) ?></dd></div>
                        <?php endif; ?>
                      </dl>
                      <p class="rental-status rental-status-<?= h($rental['status']) ?>">
                        Status: <?= $rental['status'] === 'pending'
                            ? 'Menunggu persetujuan'
                            : ($rental['status'] === 'approved' ? 'Disetujui' : 'Dibatalkan') ?>
                      </p>
                    </div>
                    <?php if ($isAdmin
                        || ($canDecide && $rental['status'] === 'pending')
                        || ($isAdmin && $rental['status'] !== 'cancelled')
                        || ($role === 'penumpang' && in_array($rental['status'], ['pending', 'approved'], true))): ?>
                      <div class="record-actions">
                        <?php if ($canDecide && $rental['status'] === 'pending'): ?>
                          <form method="post" data-pending-form>
                            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                            <input type="hidden" name="action" value="approve_rental" />
                            <input type="hidden" name="idsewa" value="<?= h($rental['idsewa']) ?>" />
                            <button class="button button-primary" type="submit">Setujui</button>
                          </form>
                        <?php endif; ?>
                        <?php if (($isAdmin && $rental['status'] !== 'cancelled')
                            || ($role === 'driver' && $rental['status'] === 'pending')
                            || ($role === 'penumpang' && in_array($rental['status'], ['pending', 'approved'], true))): ?>
                          <form method="post" data-pending-form data-confirm="Batalkan permintaan carter ini?">
                            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                            <input type="hidden" name="action" value="cancel_rental" />
                            <input type="hidden" name="idsewa" value="<?= h($rental['idsewa']) ?>" />
                            <button class="button button-danger" type="submit">
                              <?= $role === 'penumpang' ? 'Batalkan permintaan' : 'Batalkan carter' ?>
                            </button>
                          </form>
                        <?php endif; ?>
                        <?php if ($isAdmin): ?>
                          <details class="account-editor rental-editor">
                            <summary>Ubah detail carter</summary>
                            <form class="data-form" method="post" data-pending-form>
                              <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                              <input type="hidden" name="action" value="update_rental" />
                              <input type="hidden" name="idsewa" value="<?= h($rental['idsewa']) ?>" />
                              <div class="form-grid">
                                <label for="passenger-<?= h($rental['idsewa']) ?>">Penumpang
                                  <select id="passenger-<?= h($rental['idsewa']) ?>" name="idpenumpang" required>
                                    <?php foreach ($managementPassengers as $passenger): ?>
                                      <option value="<?= h($passenger['idpenumpang']) ?>" <?= (int) $rental['idpenumpang'] === (int) $passenger['idpenumpang'] ? 'selected' : '' ?>>
                                        <?= h($passenger['usernamepenumpang']) ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </label>
                                <label for="driver-<?= h($rental['idsewa']) ?>">Driver
                                  <select id="driver-<?= h($rental['idsewa']) ?>" name="iddriver" required>
                                    <?php foreach ($drivers as $driver): ?>
                                      <option value="<?= h($driver['iddriver']) ?>" <?= (int) $rental['iddriver'] === (int) $driver['iddriver'] ? 'selected' : '' ?>>
                                        <?= h($driver['usernamedriver']) ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </label>
                                <label for="car-<?= h($rental['idsewa']) ?>">Mobil
                                  <select id="car-<?= h($rental['idsewa']) ?>" name="idmobil" class="rental-car-select" required>
                                    <?php foreach ($cars as $car): ?>
                                      <option value="<?= h($car['idmobil']) ?>" data-kategori="<?= h((string) ($car['kategori'] ?? '')) ?>" data-bid-harga="<?= h((string) ($car['bid_harga'] ?? 0)) ?>" <?= (int) ($rental['idmobil'] ?? 0) === (int) $car['idmobil'] ? 'selected' : '' ?>>
                                        <?= h($car['merkmobil'] . ' ' . $car['namamobil'] . ' · ' . travel_car_category_label((string) ($car['kategori'] ?? '')) . ' · ' . $car['nomorplat'] . ' · ' . travel_format_currency((string) ($car['bid_harga'] ?? 0))) ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </label>
                                <label for="time-<?= h($rental['idsewa']) ?>">Waktu sewa
                                  <div class="datetime-field">
                                    <input id="time-<?= h($rental['idsewa']) ?>" name="waktusewa" type="datetime-local" value="<?= h(substr((string) $rental['waktusewa'], 0, 16)) ?>" required />
                                    <button class="datetime-picker-trigger" type="button" aria-label="Pilih tanggal dan waktu sewa">
                                      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M7 2.5v2.2M17 2.5v2.2M3.5 8.2h17M5 5.2h14a1.8 1.8 0 0 1 1.8 1.8v11a1.8 1.8 0 0 1-1.8 1.8H5A1.8 1.8 0 0 1 3.2 18V7a1.8 1.8 0 0 1 1.8-1.8Z"/>
                                      </svg>
                                    </button>
                                  </div>
                                </label>
                                <label for="status-<?= h($rental['idsewa']) ?>">Status
                                  <select id="status-<?= h($rental['idsewa']) ?>" name="status" required>
                                    <option value="pending" <?= $rental['status'] === 'pending' ? 'selected' : '' ?>>Menunggu persetujuan</option>
                                    <option value="approved" <?= $rental['status'] === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="cancelled" <?= $rental['status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                  </select>
                                </label>
                              </div>
                              <div class="form-actions">
                                <button class="button button-primary" type="submit">Simpan perubahan</button>
                                <span class="form-pending" role="status" hidden>Menyimpan perubahan...</span>
                              </div>
                            </form>
                          </details>
                          <form method="post" data-pending-form data-confirm="Hapus catatan carter ini secara permanen?">
                            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
                            <input type="hidden" name="action" value="delete_rental" />
                            <input type="hidden" name="idsewa" value="<?= h($rental['idsewa']) ?>" />
                            <button class="button button-danger" type="submit">Hapus catatan</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </article>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>
      </main>

    </div>
    <script src="password-reveal.js" defer></script>
    <script src="morph.js" defer></script>
    <script>
      let pollingNotifications = false;

      async function pollNotifications() {
        if (pollingNotifications) return;
        const notificationRegion = document.querySelector('.notification-region');
        if (!notificationRegion || !notificationRegion.dataset.csrfToken) return;
        const notificationList = document.querySelector('#notification-list');
        const notificationEmpty = document.querySelector('#notification-empty');
        const notificationError = document.querySelector('#notification-error');
        if (!notificationList || !notificationEmpty || !notificationError) return;

        pollingNotifications = true;
        try {
          const response = await fetch('notifikasi.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams({
              csrf_token: notificationRegion.dataset.csrfToken
            })
          });
          if (!response.ok) throw new Error(`Notifikasi gagal dimuat (${response.status}).`);
          const result = await response.json();
          if (!result.success || !Array.isArray(result.notifications)) {
            throw new Error('Respons notifikasi tidak valid.');
          }

          notificationError.hidden = true;
          if (result.notifications.length > 0) notificationEmpty.hidden = true;
          else if (notificationList.children.length === 0) {
            notificationEmpty.textContent = 'Belum ada notifikasi baru.';
            notificationEmpty.hidden = false;
          }
          for (const notification of result.notifications) {
            const item = document.createElement('li');
            item.className = 'notification-item';
            const message = document.createElement('p');
            message.textContent = notification.message;
            const link = document.createElement('a');
            link.href = `index.php?halaman=carter#sewa-${encodeURIComponent(notification.rental_id)}`;
            link.textContent = 'Buka carter';
            const close = document.createElement('button');
            close.className = 'button button-quiet';
            close.type = 'button';
            close.textContent = 'Tutup';
            close.addEventListener('click', () => {
              item.remove();
              if (notificationList.children.length === 0) notificationEmpty.hidden = false;
            });
            item.append(message, link, close);
            notificationList.append(item);
          }
        } catch (error) {
          console.error(error);
          notificationEmpty.hidden = true;
          notificationError.hidden = false;
        } finally {
          pollingNotifications = false;
        }
      }

      pollNotifications();
      window.setInterval(pollNotifications, 5000);
      window.addEventListener('travel:pagechanged', () => pollNotifications());

      document.querySelectorAll('[data-pending-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
          if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
            return;
          }
          const pending = form.querySelector('.form-pending');
          const submit = form.querySelector('button[type="submit"]');
          if (pending) pending.hidden = false;
          if (submit) submit.disabled = true;
          form.setAttribute('aria-busy', 'true');
        });
      });

      const themeToggle = document.querySelector('.theme-toggle');
      const themeText = document.querySelector('.theme-toggle-text');
      const storedTheme = localStorage.getItem('travel-theme');
      const initialTheme = storedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      const applyTheme = (theme) => {
        const nextTheme = theme === 'dark' ? 'dark' : 'light';
        document.body.setAttribute('data-theme', nextTheme);
        localStorage.setItem('travel-theme', nextTheme);
        if (themeToggle) {
          const isDark = nextTheme === 'dark';
          themeToggle.setAttribute('aria-pressed', String(isDark));
          themeToggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        }
        if (themeText) {
          themeText.textContent = nextTheme === 'dark' ? 'Dark' : 'Light';
        }
      };

      applyTheme(initialTheme);
      themeToggle?.addEventListener('click', () => {
        const nextTheme = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
      });

      const updateSelectedRentalCar = (selectElement) => {
        if (!selectElement) return;
        const selected = selectElement.options[selectElement.selectedIndex];
        const summary = document.getElementById('rental-car-summary');
        if (!summary) return;
        if (!selected || !selected.value) {
          summary.innerHTML = '<strong>Detail mobil:</strong> <span>Belum ada mobil dipilih.</span>';
          return;
        }
        const category = selected.dataset.kategori || 'Belum ditentukan';
        const bid = Number(selected.dataset.bidHarga || 0);
        const formattedBid = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          maximumFractionDigits: 0,
        }).format(bid);
        summary.innerHTML = `<strong>Detail mobil:</strong> <span>${selected.text || 'Mobil terpilih'} · Kategori: ${category} · Bid: ${formattedBid}</span>`;
      };

      document.querySelectorAll('.rental-car-select').forEach((select) => {
        updateSelectedRentalCar(select);
        select.addEventListener('change', () => updateSelectedRentalCar(select));
      });

      document.querySelectorAll('#bid_harga').forEach((input) => {
        const sanitizeBid = () => {
          input.value = input.value.replace(/\D/g, '').slice(0, 7);
          if (input.value && Number(input.value) > 9999999) {
            input.value = '9999999';
          }
        };
        input.addEventListener('input', sanitizeBid);
        input.addEventListener('blur', sanitizeBid);
      });

      document.querySelectorAll('.datetime-picker-trigger').forEach((button) => {
        const field = button.closest('.datetime-field')?.querySelector('input[type="datetime-local"]');
        if (!field) return;
        button.addEventListener('click', () => {
          if (typeof field.showPicker === 'function') {
            field.showPicker();
            return;
          }
          field.focus();
        });
      });

      window.addEventListener('pointermove', (event) => {
        const x = (event.clientX / window.innerWidth) * 100;
        const y = (event.clientY / window.innerHeight) * 100;
        document.documentElement.style.setProperty('--page-x', `${x}%`);
        document.documentElement.style.setProperty('--page-y', `${y}%`);
      });
    </script>
  </body>
</html>
