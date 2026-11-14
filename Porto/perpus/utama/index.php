<?php
require_once __DIR__ . '/auth.php';
auth_start_session();
require_authenticated_user();
try {
    require_once __DIR__ . '/connection.php';
} catch (mysqli_sql_exception $exception) {
    error_log('Koneksi database perpustakaan gagal: ' . $exception->getMessage());
    http_response_code(503);
    exit('Database perpustakaan tidak dapat dihubungi. Silakan coba lagi nanti.');
}

$role = $_SESSION['role'];
$requestedPage = isset($_GET['halaman']) && is_string($_GET['halaman'])
    ? $_GET['halaman']
    : 'home';
$studentReadOnlyPages = ['home', 'buku', 'penulis', 'penerbit', 'siswa', 'petugas', 'peminjaman', 'peminjamantambah'];
$accessDenied = $role === 'siswa' && !in_array($requestedPage, $studentReadOnlyPages, true);
$halaman = $accessDenied ? 'home' : $requestedPage;

if ($accessDenied) {
    http_response_code(403);
}

$roleLabels = [
    'admin' => 'Administrator',
    'petugas' => 'Petugas',
    'siswa' => 'Siswa'
];
$roleLabel = $roleLabels[$role] ?? ucfirst($role);
$displayName = $_SESSION['display_name'] ?? $_SESSION['username'];
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kikii's Aetherium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/select2.min.css" />
    <link rel="stylesheet" href="css/jquery.dataTables.min.css" />
  </head>
  <body data-theme="light">
    <a class="skip-link" href="#main-content">Lewati ke konten</a>
    <div class="app-shell">
      <header class="topbar">
        <a class="brand" href="index.php?halaman=home" aria-label="PustakaGuard., beranda">
          <span>Kikii's Aetherium</span>
        </a>
        <div class="account-tools">
          <span class="account-label"><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></span>
          <button class="theme-toggle" type="button" aria-label="Beralih ke mode gelap" aria-pressed="false">
            <span class="theme-toggle-track">
              <span class="theme-toggle-thumb"></span>
            </span>
            <span class="theme-toggle-text">Light</span>
          </button>
          <form class="logout-form" method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>" />
            <button class="button button-quiet" type="submit">Keluar</button>
          </form>
        </div>
      </header>

      <nav class="main-nav" aria-label="Navigasi utama">
        <a href="index.php?halaman=home" <?= $halaman === 'home' ? 'aria-current="page"' : '' ?>>Beranda</a>
        <a href="index.php?halaman=buku" <?= in_array($halaman, ['buku', 'bukutambah', 'bukuedit', 'penulis', 'penulistambah', 'penulisedit', 'penerbit', 'penerbittambah', 'penerbitedit'], true) ? 'aria-current="page"' : '' ?>>Buku</a>
        <a href="index.php?halaman=siswa" <?= in_array($halaman, ['siswa', 'siswatambah', 'siswaedit'], true) ? 'aria-current="page"' : '' ?>>Siswa</a>
        <a href="index.php?halaman=petugas" <?= in_array($halaman, ['petugas', 'petugastambah', 'petugasedit'], true) ? 'aria-current="page"' : '' ?>>Petugas</a>
        <a href="index.php?halaman=peminjaman" <?= in_array($halaman, ['peminjaman', 'peminjamantambah'], true) ? 'aria-current="page"' : '' ?>>Peminjaman</a>
      </nav>
      
      <main id="main-content" class="page-content">
        <div class="content">
          <aside class="konten-kiri" aria-label="Informasi Pengguna">
            <div class="sidebar-user-card">
              <div class="user-avatar-badge" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($displayName, 0, 1)), ENT_QUOTES, 'UTF-8') ?></div>
              <div>
                <h3>Info Akun</h3>
                <p class="user-greeting">Halo, <strong><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></strong></p>
                <span class="role-badge role-badge-<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>
            <p class="sidebar-desc"><?= $role === 'siswa' ? 'Jelajahi koleksi dan ajukan peminjaman mandiri untuk akun Anda.' : 'Gunakan menu sesuai hak akses untuk mengelola data perpustakaan.' ?></p>
            
            <div class="sidebar-section">
              <h3>Koleksi Favorit</h3>
              <ul class="genre-tag-list">
                <li><span class="genre-pill">Novel &amp; Sastra</span></li>
                <li><span class="genre-pill">Buku Pelajaran</span></li>
                <li><span class="genre-pill">Biografi Tokoh</span></li>
                <li><span class="genre-pill">Komik Edukasi</span></li>
              </ul>
            </div>

            <?php if ($role !== 'siswa'): ?>
              <div class="sidebar-section quick-links">
                <h3>Pintas Cepat</h3>
                <div class="quick-link-buttons">
                  <a class="button button-quiet" href="index.php?halaman=bukutambah">+ Tambah Buku</a>
                  <a class="button button-quiet" href="index.php?halaman=peminjamantambah">+ Pinjam Buku</a>
                </div>
              </div>
            <?php endif; ?>
          </aside>

          <div class="konten-utama">
            <?php if ($accessDenied): ?>
              <p class="access-notice notice notice-error" role="alert">Halaman tersebut hanya dapat dibuka oleh Admin atau Petugas. Anda dapat melihat data perpustakaan dalam mode baca.</p>
            <?php endif; ?>
          <?php 
          switch ($halaman) { 
            case 'home': 
                include 'page/home/home.php'; 
                break; 
            
            case 'buku': 
                include 'page/buku/buku.php'; 
                break; 
            case 'bukutambah': 
                include 'page/buku/bukutambah.php'; 
                break;
            case 'bukuedit': 
                include 'page/buku/bukuedit.php'; 
                break;

            case 'penulis': 
                include 'page/buku/penulis/penulis.php'; 
                break;
            case 'penulisedit': 
                include 'page/buku/penulis/penulisedit.php'; 
                break;
            case 'penulistambah': 
                include 'page/buku/penulis/penulistambah.php'; 
                break;

            case 'penerbit': 
                include 'page/buku/penerbit/penerbit.php'; 
                break;
            case 'penerbitedit': 
                include 'page/buku/penerbit/penerbitedit.php'; 
                break;
            case 'penerbittambah': 
                include 'page/buku/penerbit/penerbittambah.php'; 
                break;
              
            case 'siswa': 
                include 'page/siswa/siswa.php'; 
                break; 
            case 'siswatambah': 
                include 'page/siswa/siswatambah.php'; 
                break; 
            case 'siswaedit': 
                include 'page/siswa/siswaedit.php'; 
                break; 
            
            case 'petugas': 
                include 'page/petugas/petugas.php'; 
                break; 
            case 'petugastambah': 
                include 'page/petugas/petugastambah.php'; 
                break;
            case 'petugasedit': 
                include 'page/petugas/petugasedit.php'; 
                break;
            
            case 'peminjaman': 
                include 'page/peminjaman/peminjaman.php'; 
                break; 
            case 'peminjamantambah': 
                include 'page/peminjaman/peminjamantambah.php'; 
                break;

            default: 
                echo "<h2>404 - Waduh, halamannya nggak ketemu!</h2>"; 
                break; 
          } 
          ?>
          </div>
        </div>
      </main>

      <footer class="footer">
        <p>&copy; 2026 - PustakaGuard | Sistem Informasi Perpustakaan Terpadu</p>
      </footer>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="password-reveal.js" defer></script>
    <script src="morph.js" defer></script>
  </body>
</html>