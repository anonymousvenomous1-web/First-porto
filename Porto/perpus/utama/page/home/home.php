<?php 
require_once __DIR__ . '/guard.php'; 
$role = $_SESSION['role'] ?? 'siswa';
$roleLabels = [
    'admin' => 'Administrator',
    'petugas' => 'Petugas Perpustakaan',
    'siswa' => 'Siswa'
];
$roleLabel = $roleLabels[$role] ?? ucfirst($role);
$displayName = $_SESSION['display_name'] ?? $_SESSION['username'];
?>

<section class="welcome-panel" aria-labelledby="page-title">
  <div>
    <p class="eyebrow">Ruang kerja <?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></p>
    <h1 id="page-title">Halo, <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>.</h1>
    <?php if ($role === 'siswa'): ?>
      <p>Jelajahi katalog buku, pantau batas waktu pengembalian, dan ajukan peminjaman buku baru secara mandiri.</p>
      <div class="form-actions">
        <a class="button button-primary" href="index.php?halaman=buku">Jelajahi Buku</a>
        <a class="button button-secondary" href="index.php?halaman=peminjaman">Status Peminjaman</a>
      </div>
    <?php elseif ($role === 'petugas'): ?>
      <p>Kelola sirkulasi buku, verifikasi transaksi peminjaman siswa, dan pantau stok buku perpustakaan.</p>
      <div class="form-actions">
        <a class="button button-primary" href="index.php?halaman=peminjaman">Kelola Peminjaman</a>
        <a class="button button-secondary" href="index.php?halaman=buku">Katalog Buku</a>
      </div>
    <?php else: ?>
      <p>Akses kontrol penuh: kelola katalog buku, penerbit, penulis, data anggota siswa, petugas, dan peminjaman.</p>
      <div class="form-actions">
        <a class="button button-primary" href="index.php?halaman=buku">Katalog Buku</a>
        <a class="button button-secondary" href="index.php?halaman=petugas">Kelola Petugas</a>
      </div>
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
      <p class="eyebrow">Informasi akun</p>
      <h2 id="profile-title">Profil & Akses Pengguna</h2>
    </div>
  </div>
  <dl class="profile-details">
    <div>
      <dt>Nama Lengkap</dt>
      <dd><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></dd>
    </div>
    <div>
      <dt>Username</dt>
      <dd><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></dd>
    </div>
    <div>
      <dt>Peran Akun</dt>
      <dd><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></dd>
    </div>
    <div>
      <dt>Hak Akses</dt>
      <dd><?= $role === 'siswa' ? 'Mode Baca & Pengajuan Pinjaman' : 'Pengelolaan Penuh & Verifikasi' ?></dd>
    </div>
  </dl>
</section>
