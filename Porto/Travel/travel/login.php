<?php
require_once __DIR__ . '/auth.php';
auth_start_session();

if (auth_is_authenticated()) {
    header('Location: index.php');
    exit;
}

$form = isset($_POST['form']) && is_string($_POST['form'])
    ? $_POST['form']
    : (isset($_GET['form']) && is_string($_GET['form']) ? $_GET['form'] : 'login');
$form = $form === 'register' ? 'register' : 'login';
$role = isset($_POST['role']) && is_string($_POST['role'])
    ? $_POST['role']
    : (isset($_GET['role']) && is_string($_GET['role']) ? $_GET['role'] : '');
$allowedRoles = $form === 'register' ? ['driver', 'penumpang'] : ['admin', 'driver', 'penumpang'];
$role = in_array($role, $allowedRoles, true) ? $role : '';
$error = '';
$username = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
$value = static function (string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if (!auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Formulir sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($form === 'register') {
        $usernameLimit = $role === 'driver' ? 25 : 50;
        $passwordConfirmation = isset($_POST['password_confirmation']) && is_string($_POST['password_confirmation'])
            ? $_POST['password_confirmation']
            : '';
        $passwordCharacters = preg_match_all('/./us', $password, $passwordMatches);

        if ($role === '') {
            $error = 'Pilih jenis akun terlebih dahulu.';
        } elseif (!preg_match('/^[A-Za-z0-9._-]{3,' . $usernameLimit . '}$/D', $username)) {
            $error = 'Username harus berisi 3 sampai ' . $usernameLimit . ' huruf, angka, titik, garis bawah, atau tanda hubung.';
        } elseif ($passwordCharacters === false || $passwordCharacters < 6 || strlen($password) > 72) {
            $error = 'Kata sandi harus berisi minimal 6 karakter dan maksimal 72 byte.';
        } elseif ($password !== $passwordConfirmation) {
            $error = 'Konfirmasi kata sandi tidak sama.';
        } elseif ($role === 'driver'
            && ($value('nomorhp') === ''
                || strlen($value('nomorhp')) > 20
                || $value('nomorsim') === ''
                || strlen($value('nomorsim')) > 16
                || $value('nomorktp') === ''
                || strlen($value('nomorktp')) > 16
                || $value('tanggal_lahir') === ''
                || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value('tanggal_lahir')))) {
            $error = 'Lengkapi data driver sesuai batas kolom pada basis data.';
        } elseif ($role === 'penumpang'
            && ($value('nomorhp') === '' || strlen($value('nomorhp')) > 25)) {
            $error = 'Masukkan nomor HP penumpang maksimal 25 karakter.';
        } else {
            try {
                require __DIR__ . '/connection.php';

                if ($role === 'driver') {
                    $check = $conn->prepare('SELECT iddriver FROM tbl_driver WHERE usernamedriver = ? LIMIT 1');
                } else {
                    $check = $conn->prepare('SELECT idpenumpang FROM tbl__penumpang WHERE usernamepenumpang = ? LIMIT 1');
                }
                $check->bind_param('s', $username);
                $check->execute();

                if ($check->get_result()->fetch_assoc()) {
                    $error = 'Username sudah dipakai untuk jenis akun ini.';
                } elseif ($role === 'driver') {
                    $phone = $value('nomorhp');
                    $sim = $value('nomorsim');
                    $ktp = $value('nomorktp');
                    $birthDate = $value('tanggal_lahir');
                    $insert = $conn->prepare(
                        'INSERT INTO tbl_driver
                            (usernamedriver, passworddriver, nomorhp, nomorsim, nomorktp, tanggal_lahir)
                         VALUES (?, ?, ?, ?, ?, ?)'
                    );
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $insert->bind_param('ssssss', $username, $passwordHash, $phone, $sim, $ktp, $birthDate);
                    $insert->execute();
                    auth_sign_in((int) $conn->insert_id, $username, 'driver');
                    header('Location: index.php');
                    exit;
                } else {
                    $phone = $value('nomorhp');
                    $insert = $conn->prepare(
                        'INSERT INTO tbl__penumpang (usernamepenumpang, passwordpenumpang, nomorhp)
                         VALUES (?, ?, ?)'
                    );
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $insert->bind_param('sss', $username, $passwordHash, $phone);
                    $insert->execute();
                    auth_sign_in((int) $conn->insert_id, $username, 'penumpang');
                    header('Location: index.php');
                    exit;
                }
            } catch (mysqli_sql_exception $exception) {
                error_log('Pendaftaran akun travel gagal: ' . $exception->getMessage());
                $error = 'Pendaftaran belum dapat diproses. Periksa koneksi basis data lalu coba lagi.';
            }
        }
    } elseif ($role === '' || $username === '' || $password === '') {
        $error = 'Pilih jenis akun, lalu masukkan username dan kata sandi.';
    } elseif (($passwordCharacters = preg_match_all('/./us', $password, $passwordMatches)) === false
        || $passwordCharacters > 72 || strlen($password) > 72) {
        $error = 'Kata sandi melebihi batas kolom basis data.';
    } else {
        try {
            require __DIR__ . '/connection.php';
            if ($role === 'admin') {
                $statement = $conn->prepare(
                    'SELECT idadmin AS user_id, username, password_hash AS stored_password
                     FROM tbl_admin WHERE username = ? LIMIT 1'
                );
            } elseif ($role === 'driver') {
                $statement = $conn->prepare(
                    'SELECT iddriver AS user_id, usernamedriver AS username, passworddriver AS stored_password
                     FROM tbl_driver WHERE usernamedriver = ? LIMIT 1'
                );
            } else {
                $statement = $conn->prepare(
                    'SELECT idpenumpang AS user_id, usernamepenumpang AS username, passwordpenumpang AS stored_password
                     FROM tbl__penumpang WHERE usernamepenumpang = ? LIMIT 1'
                );
            }
            $statement->bind_param('s', $username);
            $statement->execute();
            $user = $statement->get_result()->fetch_assoc();

            $storedPassword = $user ? (string) $user['stored_password'] : '';
            $passwordMatches = $user && password_verify($password, $storedPassword);
            $legacyPasswordMatches = $user && !$passwordMatches && hash_equals($storedPassword, $password);

            if ($user && ($passwordMatches || $legacyPasswordMatches)) {
                if ($legacyPasswordMatches && $role !== 'admin') {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    if ($role === 'driver') {
                        $upgrade = $conn->prepare(
                            'UPDATE tbl_driver SET passworddriver = ? WHERE iddriver = ?'
                        );
                    } else {
                        $upgrade = $conn->prepare(
                            'UPDATE tbl__penumpang SET passwordpenumpang = ? WHERE idpenumpang = ?'
                        );
                    }
                    $userId = (int) $user['user_id'];
                    $upgrade->bind_param('si', $passwordHash, $userId);
                    $upgrade->execute();
                }
                auth_sign_in((int) $user['user_id'], (string) $user['username'], $role);
                header('Location: index.php');
                exit;
            }
            $error = 'Username atau kata sandi tidak sesuai.';
        } catch (mysqli_sql_exception $exception) {
            error_log('Login travel gagal: ' . $exception->getMessage());
            $error = 'Login belum dapat diproses. Periksa koneksi basis data lalu coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $form === 'register' ? 'Daftar akun' : 'Masuk' ?> | T-RavelGuard.</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/auth.css" />
  </head>
  <body class="auth-body">
    <main class="auth-shell">
      <section class="auth-card" aria-labelledby="auth-title">
        <a class="auth-brand" href="login.php">T-RavelGuard.</a>
        <p class="auth-kicker">Manajemen carter travel</p>
        <h1 id="auth-title"><?= $form === 'register' ? 'Buat akun' : 'Masuk ke akun' ?></h1>
        <p class="auth-intro">
          <?= $form === 'register'
              ? 'Pilih peran untuk membuat akun driver atau penumpang.'
              : 'Pilih peran akun Anda untuk melihat catatan carter yang sesuai.' ?>
        </p>

        <?php if ($error !== ''): ?>
          <p class="auth-message auth-error" role="alert"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if ($form === 'register' && $role === ''): ?>
          <div class="auth-role-choices">
            <a href="login.php?form=register&amp;role=driver">Daftar sebagai driver</a>
            <a href="login.php?form=register&amp;role=penumpang">Daftar sebagai penumpang</a>
          </div>
        <?php elseif ($form === 'register'): ?>
          <nav class="auth-role-switch" aria-label="Pilih jenis akun">
            <a href="login.php?form=register&amp;role=driver" <?= $role === 'driver' ? 'aria-current="page"' : '' ?>>Driver</a>
            <a href="login.php?form=register&amp;role=penumpang" <?= $role === 'penumpang' ? 'aria-current="page"' : '' ?>>Penumpang</a>
          </nav>
          <form class="auth-form" method="post" action="login.php?form=register&amp;role=<?= h($role) ?>" autocomplete="on">
            <input type="hidden" name="form" value="register" />
            <input type="hidden" name="role" value="<?= h($role) ?>" />
            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />

            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?= h($username) ?>" minlength="3" maxlength="<?= $role === 'driver' ? '25' : '50' ?>" pattern="[A-Za-z0-9._\-]+" autocomplete="username" required autofocus />

            <?php if ($role === 'driver'): ?>
              <label for="nomorhp">Nomor HP</label>
              <input id="nomorhp" name="nomorhp" type="tel" value="<?= h($value('nomorhp')) ?>" maxlength="20" autocomplete="tel" required />
              <label for="nomorsim">Nomor SIM</label>
              <input id="nomorsim" name="nomorsim" value="<?= h($value('nomorsim')) ?>" maxlength="16" required />
              <label for="nomorktp">Nomor KTP</label>
              <input id="nomorktp" name="nomorktp" value="<?= h($value('nomorktp')) ?>" maxlength="16" required />
              <label for="tanggal_lahir">Tanggal lahir</label>
              <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="<?= h($value('tanggal_lahir')) ?>" required />
            <?php else: ?>
              <label for="nomorhp">Nomor HP</label>
              <input id="nomorhp" name="nomorhp" type="tel" value="<?= h($value('nomorhp')) ?>" maxlength="25" autocomplete="tel" required />
            <?php endif; ?>

            <label for="password">Kata sandi (minimal 6 karakter)</label>
            <input id="password" name="password" type="password" minlength="6" maxlength="72" autocomplete="new-password" required />
            <label for="password_confirmation">Ulangi kata sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="6" maxlength="72" autocomplete="new-password" required />
            <button class="auth-submit" type="submit">Buat akun <?= $role === 'driver' ? 'driver' : 'penumpang' ?></button>
          </form>
        <?php else: ?>
          <form class="auth-form" method="post" action="login.php" autocomplete="on">
            <input type="hidden" name="form" value="login" />
            <input type="hidden" name="csrf_token" value="<?= h(auth_csrf_token()) ?>" />
            <label for="role">Masuk sebagai</label>
            <select id="role" name="role" required>
              <option value="">Pilih peran</option>
              <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
              <option value="driver" <?= $role === 'driver' ? 'selected' : '' ?>>Driver</option>
              <option value="penumpang" <?= $role === 'penumpang' ? 'selected' : '' ?>>Penumpang</option>
            </select>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?= h($username) ?>" maxlength="50" autocomplete="username" required autofocus />
            <label for="password">Kata sandi</label>
            <input id="password" name="password" type="password" maxlength="72" autocomplete="current-password" required />
            <button class="auth-submit" type="submit">Masuk</button>
          </form>
        <?php endif; ?>

        <p class="auth-switch">
          <?= $form === 'register' ? 'Sudah punya akun?' : 'Belum punya akun?' ?>
          <a href="login.php?form=<?= $form === 'register' ? 'login' : 'register' ?>"><?= $form === 'register' ? 'Masuk' : 'Daftar' ?></a>
        </p>
      </section>
    </main>
    <script src="password-reveal.js" defer></script>
    <script src="morph.js" defer></script>
  </body>
</html>
