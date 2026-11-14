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
$error = '';
$username = '';
$value = static function (string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($value('username'));
    $password = $value('password');

    if (!auth_verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Formulir sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif ($form === 'register') {
        $role = $value('role');
        $confirmation = $value('password_confirmation');
        $passwordCharacters = preg_match_all('/./us', $password, $matches);

        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/D', $username)) {
            $error = 'Username harus terdiri dari 3 sampai 50 huruf, angka, titik, garis bawah, atau tanda hubung.';
        } elseif (strcasecmp($username, AUTH_DEFAULT_ADMIN_USERNAME) === 0) {
            $error = 'Username tersebut sudah digunakan. Silakan pilih username lain.';
        } elseif ($passwordCharacters === false || $passwordCharacters < 8 || strlen($password) > 72) {
            $error = 'Kata sandi harus memiliki minimal 8 karakter dan maksimal 72 byte UTF-8.';
        } elseif ($password !== $confirmation) {
            $error = 'Konfirmasi kata sandi tidak sama.';
        } elseif (!in_array($role, ['petugas', 'siswa'], true)) {
            $error = 'Pilih peran Petugas atau Siswa.';
        } elseif ($role === 'petugas'
            && (trim($value('namapetugas')) === '' || trim($value('hppetugas')) === '')) {
            $error = 'Lengkapi nama dan nomor HP Petugas.';
        } elseif ($role === 'siswa'
            && (trim($value('nis')) === ''
                || trim($value('nisn')) === ''
                || trim($value('namasiswa')) === ''
                || trim($value('alamat')) === ''
                || trim($value('kelas')) === ''
                || trim($value('hpsiswa')) === '')) {
            $error = 'Lengkapi seluruh data Siswa.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            if ($passwordHash === false) {
                error_log('Gagal membuat hash kata sandi untuk pendaftaran perpustakaan.');
                $error = 'Akun belum dapat dibuat. Silakan coba lagi nanti.';
            } else {
                try {
                    require __DIR__ . '/connection.php';
                    $lockName = 'perpustakaan_' . substr(hash('sha256', strtolower($username)), 0, 48);
                    $lockStatement = $conn->prepare('SELECT GET_LOCK(?, 5) AS lock_acquired');
                    $lockStatement->bind_param('s', $lockName);
                    $lockStatement->execute();
                    $lockAcquired = (int) $lockStatement->get_result()->fetch_assoc()['lock_acquired'] === 1;

                    if (!$lockAcquired) {
                        $error = 'Pendaftaran sedang sibuk. Silakan coba lagi.';
                    } else {
                        $checkStatement = $conn->prepare(
                            'SELECT username FROM tbl_petugas WHERE username = ?
                             UNION ALL
                             SELECT username FROM tbl_siswa WHERE username = ?
                             LIMIT 1'
                        );
                        $checkStatement->bind_param('ss', $username, $username);
                        $checkStatement->execute();

                        if ($checkStatement->get_result()->fetch_assoc()) {
                            $error = 'Username tersebut sudah digunakan. Silakan pilih username lain.';
                        } else {
                            $conn->begin_transaction();
                            if ($role === 'petugas') {
                                $name = trim($value('namapetugas'));
                                $phone = trim($value('hppetugas'));
                                $statement = $conn->prepare(
                                    'INSERT INTO tbl_petugas (namapetugas, hppetugas, username, password_hash)
                                     VALUES (?, ?, ?, ?)'
                                );
                                $statement->bind_param('ssss', $name, $phone, $username, $passwordHash);
                            } else {
                                $nis = trim($value('nis'));
                                $nisn = trim($value('nisn'));
                                $name = trim($value('namasiswa'));
                                $address = trim($value('alamat'));
                                $class = trim($value('kelas'));
                                $phone = trim($value('hpsiswa'));
                                $statement = $conn->prepare(
                                    'INSERT INTO tbl_siswa
                                        (nis, nisn, namasiswa, alamat, kelas, hpsiswa, username, password_hash)
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                                );
                                $statement->bind_param(
                                    'ssssssss',
                                    $nis,
                                    $nisn,
                                    $name,
                                    $address,
                                    $class,
                                    $phone,
                                    $username,
                                    $passwordHash
                                );
                            }
                            $statement->execute();
                            $userId = (int) $conn->insert_id;
                            $conn->commit();

                            $releaseStatement = $conn->prepare('SELECT RELEASE_LOCK(?)');
                            $releaseStatement->bind_param('s', $lockName);
                            $releaseStatement->execute();
                            auth_sign_in($userId, $username, $role, $name);
                            header('Location: index.php');
                            exit;
                        }

                        $releaseStatement = $conn->prepare('SELECT RELEASE_LOCK(?)');
                        $releaseStatement->bind_param('s', $lockName);
                        $releaseStatement->execute();
                    }
                } catch (mysqli_sql_exception $exception) {
                    if ((int) $exception->getCode() === 1062) {
                        $error = 'Username tersebut sudah digunakan. Silakan pilih username lain.';
                    } else {
                        error_log('Pendaftaran perpustakaan gagal: ' . $exception->getMessage());
                        $error = 'Pendaftaran sedang tidak tersedia. Silakan coba lagi nanti.';
                    }
                }
            }
        }
    } elseif ($username === '' || $password === '') {
        $error = 'Masukkan username dan kata sandi.';
    } elseif (strlen($password) > 72) {
        $error = 'Username atau kata sandi tidak sesuai.';
    } else {
        try {
            if (strcasecmp($username, AUTH_DEFAULT_ADMIN_USERNAME) === 0) {
                if (password_verify($password, AUTH_DEFAULT_ADMIN_PASSWORD_HASH)) {
                    auth_sign_in(1, AUTH_DEFAULT_ADMIN_USERNAME, 'admin');
                    header('Location: index.php');
                    exit;
                }
            } else {
                require __DIR__ . '/connection.php';
                $statement = $conn->prepare(
                    "SELECT idpetugas AS user_id, namapetugas AS display_name, username, password_hash, 'petugas' AS role
                     FROM tbl_petugas WHERE username = ?
                     UNION ALL
                     SELECT idsiswa AS user_id, namasiswa AS display_name, username, password_hash, 'siswa' AS role
                     FROM tbl_siswa WHERE username = ?
                     LIMIT 1"
                );
                $statement->bind_param('ss', $username, $username);
                $statement->execute();
                $user = $statement->get_result()->fetch_assoc();

                if ($user && is_string($user['password_hash'])
                    && password_verify($password, $user['password_hash'])) {
                    auth_sign_in(
                        (int) $user['user_id'],
                        $user['username'],
                        $user['role'],
                        $user['display_name']
                    );
                    header('Location: index.php');
                    exit;
                }
            }

            $error = 'Username atau kata sandi tidak sesuai.';
        } catch (mysqli_sql_exception $exception) {
            error_log('Login perpustakaan gagal: ' . $exception->getMessage());
            $error = 'Login sedang tidak tersedia. Silakan coba lagi nanti.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $form === 'register' ? 'Daftar akun' : 'Masuk' ?> | PustakaGuard.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/auth.css" />
    <link rel="stylesheet" href="css/select2.min.css" />
  </head>
  <body class="auth-body">
    <main class="auth-shell">
      <section class="auth-card" aria-labelledby="auth-title">
        <a class="auth-brand" href="login.php" aria-label="PustakaGuard., halaman masuk">
          <span>PustakaGuard</span>
        </a>
        <p class="auth-kicker">Sistem Informasi Perpustakaan</p>
        <h1 id="auth-title"><?= $form === 'register' ? 'Buat akun' : 'Selamat datang kembali' ?></h1>
        <p class="auth-intro">
          <?= $form === 'register'
              ? 'Daftar sebagai petugas atau siswa untuk menggunakan perpustakaan.'
              : 'Masuk untuk melanjutkan ke layanan perpustakaan.' ?>
        </p>

        <?php if ($error !== ''): ?>
          <p class="auth-message auth-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form class="auth-form" method="post" action="login.php?form=<?= $form ?>" autocomplete="on">
          <input type="hidden" name="form" value="<?= $form ?>" />
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>" />

          <?php if ($form === 'register'): ?>
            <label for="role">Daftar sebagai</label>
            <select id="role" name="role" required autofocus>
              <option value="">Pilih peran</option>
              <option value="petugas" <?= $value('role') === 'petugas' ? 'selected' : '' ?>>Petugas</option>
              <option value="siswa" <?= $value('role') === 'siswa' ? 'selected' : '' ?>>Siswa</option>
            </select>

            <div class="auth-registration-fields" data-registration-fields hidden>
              <label for="username">Username</label>
              <input
                id="username"
                name="username"
                type="text"
                value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                minlength="3"
                maxlength="50"
                pattern="[A-Za-z0-9._\-]{3,50}"
                autocomplete="username"
              />

              <div class="auth-profile-fields" data-role-fields="petugas" hidden>
                <label for="namapetugas">Nama Petugas</label>
                <input id="namapetugas" name="namapetugas" value="<?= htmlspecialchars($value('namapetugas'), ENT_QUOTES, 'UTF-8') ?>" maxlength="50" />
                <label for="hppetugas">No. HP</label>
                <input id="hppetugas" name="hppetugas" value="<?= htmlspecialchars($value('hppetugas'), ENT_QUOTES, 'UTF-8') ?>" maxlength="20" inputmode="tel" />
              </div>

              <div class="auth-profile-fields" data-role-fields="siswa" hidden>
                <label for="nis">NIS</label>
                <input id="nis" name="nis" value="<?= htmlspecialchars($value('nis'), ENT_QUOTES, 'UTF-8') ?>" maxlength="10" />
                <label for="nisn">NISN</label>
                <input id="nisn" name="nisn" value="<?= htmlspecialchars($value('nisn'), ENT_QUOTES, 'UTF-8') ?>" maxlength="20" />
                <label for="namasiswa">Nama Siswa</label>
                <input id="namasiswa" name="namasiswa" value="<?= htmlspecialchars($value('namasiswa'), ENT_QUOTES, 'UTF-8') ?>" maxlength="50" />
                <label for="alamat">Alamat</label>
                <input id="alamat" name="alamat" value="<?= htmlspecialchars($value('alamat'), ENT_QUOTES, 'UTF-8') ?>" maxlength="100" />
                <label for="kelas">Kelas</label>
                <input id="kelas" name="kelas" value="<?= htmlspecialchars($value('kelas'), ENT_QUOTES, 'UTF-8') ?>" maxlength="20" />
                <label for="hpsiswa">No. HP</label>
                <input id="hpsiswa" name="hpsiswa" value="<?= htmlspecialchars($value('hpsiswa'), ENT_QUOTES, 'UTF-8') ?>" maxlength="20" inputmode="tel" />
              </div>

              <label for="password">Kata sandi</label>
              <div class="password-field">
                <input
                  id="password"
                  name="password"
                  type="password"
                  minlength="8"
                  maxlength="72"
                  autocomplete="new-password"
                />
                <button class="password-visibility" type="button" aria-label="Tahan untuk menampilkan kata sandi" aria-pressed="false" aria-controls="password" title="Tahan untuk menampilkan kata sandi">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>

              <p class="auth-note">Data akun dan profil disimpan langsung pada tabel Petugas atau Siswa sesuai peran.</p>
              <label for="password_confirmation">Ulangi kata sandi</label>
              <div class="password-field">
                <input
                  id="password_confirmation"
                  name="password_confirmation"
                  type="password"
                  minlength="8"
                  maxlength="72"
                  autocomplete="new-password"
                />
                <button class="password-visibility" type="button" aria-label="Tahan untuk menampilkan ulang kata sandi" aria-pressed="false" aria-controls="password_confirmation" title="Tahan untuk menampilkan ulang kata sandi">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              <p class="auth-note">Admin tidak dapat dipilih melalui pendaftaran.</p>

              <button class="auth-submit" type="submit">Buat akun</button>
            </div>
          <?php else: ?>
            <label for="username">Username</label>
            <input
              id="username"
              name="username"
              type="text"
              value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
              minlength="3"
              maxlength="50"
              pattern="[A-Za-z0-9._\-]{3,50}"
              autocomplete="username"
              required
              autofocus
            />

            <label for="password">Kata sandi</label>
            <div class="password-field">
              <input
                id="password"
                name="password"
                type="password"
                minlength="8"
                maxlength="72"
                autocomplete="current-password"
                required
              />
              <button class="password-visibility" type="button" aria-label="Tahan untuk menampilkan kata sandi" aria-pressed="false" aria-controls="password" title="Tahan untuk menampilkan kata sandi">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>

            <button class="auth-submit" type="submit">Masuk</button>
          <?php endif; ?>
        </form>

        <p class="auth-switch">
          <?= $form === 'register' ? 'Sudah memiliki akun?' : 'Belum memiliki akun?' ?>
          <a href="login.php?form=<?= $form === 'register' ? 'login' : 'register' ?>">
            <?= $form === 'register' ? 'Masuk' : 'Daftar' ?>
          </a>
        </p>
        <p class="auth-security">Berhati-hatilah dengan kata sandi, jangan sampai lupa.</p>
      </section>
    </main>
    <script src="js/jquery.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="morph.js" defer></script>
    <script>
      $('.auth-form select').select2();

      const roleSelect = document.querySelector('#role');
      if (roleSelect) {
        const registrationFields = document.querySelector('[data-registration-fields]');
        const updateProfileFields = () => {
          const hasRole = roleSelect.value !== '';
          registrationFields.hidden = !hasRole;
          registrationFields.querySelectorAll(':scope > input').forEach((input) => {
            input.disabled = !hasRole;
            input.required = hasRole;
          });
          document.querySelectorAll('[data-role-fields]').forEach((group) => {
            const isActive = hasRole && group.dataset.roleFields === roleSelect.value;
            group.hidden = !isActive;
            group.querySelectorAll('input').forEach((input) => {
              input.disabled = !isActive;
              input.required = isActive;
            });
          });
        };
        $(roleSelect).on('change', updateProfileFields);
        updateProfileFields();
      }

      document.querySelectorAll('.password-visibility').forEach((button) => {
        const passwordInput = document.getElementById(button.getAttribute('aria-controls'));
        let activePointerId = null;
        let activeKey = null;

        const setVisible = (visible) => {
          passwordInput.type = visible ? 'text' : 'password';
          button.setAttribute('aria-pressed', String(visible));
          button.setAttribute(
            'aria-label',
            visible
              ? button.getAttribute('aria-controls') === 'password_confirmation'
                ? 'Lepas untuk menyembunyikan ulang kata sandi'
                : 'Lepas untuk menyembunyikan kata sandi'
              : button.getAttribute('aria-controls') === 'password_confirmation'
                ? 'Tahan untuk menampilkan ulang kata sandi'
                : 'Tahan untuk menampilkan kata sandi'
          );
        };

        button.addEventListener('pointerdown', (event) => {
          if (event.button !== 0 && event.pointerType === 'mouse') {
            return;
          }
          event.preventDefault();
          activePointerId = event.pointerId;
          button.setPointerCapture(event.pointerId);
          setVisible(true);
        });
        button.addEventListener('pointerup', (event) => {
          if (event.pointerId === activePointerId) {
            activePointerId = null;
            setVisible(false);
          }
        });
        button.addEventListener('pointercancel', () => {
          activePointerId = null;
          setVisible(false);
        });
        button.addEventListener('lostpointercapture', () => {
          activePointerId = null;
          setVisible(false);
        });
        button.addEventListener('pointerleave', (event) => {
          if (event.pointerType === 'mouse' && activePointerId !== null) {
            activePointerId = null;
            setVisible(false);
          }
        });
        button.addEventListener('keydown', (event) => {
          if ((event.key === ' ' || event.key === 'Enter') && !event.repeat) {
            event.preventDefault();
            activeKey = event.key;
            setVisible(true);
          }
        });
        button.addEventListener('keyup', (event) => {
          if (event.key === activeKey) {
            activeKey = null;
            setVisible(false);
          }
        });
        button.addEventListener('blur', () => {
          activePointerId = null;
          activeKey = null;
          setVisible(false);
        });
      });

      document.querySelector('.auth-form').addEventListener('submit', (event) => {
        const submitButton = event.currentTarget.querySelector('.auth-submit');
        submitButton.disabled = true;
        submitButton.textContent = 'Memproses...';
      });
    </script>
  </body>
</html>
