<?php
require_once __DIR__ . '/guard.php';
date_default_timezone_set('Asia/Jakarta');
$isStudent = ($_SESSION['role'] ?? '') === 'siswa';
$q_petugas = $isStudent ? false : mysqli_query($conn, 'SELECT idpetugas, namapetugas FROM tbl_petugas ORDER BY namapetugas');
$q_siswa = $isStudent ? false : mysqli_query($conn, 'SELECT idsiswa, namasiswa FROM tbl_siswa ORDER BY namasiswa');
$q_buku = mysqli_query($conn, 'SELECT idbuku, judul, stok FROM tbl_buku WHERE stok > 0 ORDER BY judul');
$error = isset($_SESSION['peminjaman_error']) && is_string($_SESSION['peminjaman_error'])
    ? $_SESSION['peminjaman_error']
    : '';
unset($_SESSION['peminjaman_error']);
$hasPetugas = $isStudent || mysqli_num_rows($q_petugas) > 0;
$hasSiswa = $isStudent || mysqli_num_rows($q_siswa) > 0;
$hasBuku = mysqli_num_rows($q_buku) > 0;
?>

<header class="page-heading">
  <p class="eyebrow">Formulir peminjaman</p>
  <h1>Tambah Peminjaman Baru</h1>
  <p>Pilih buku yang diinginkan beserta batas waktu peminjaman.</p>
</header>

<?php if ($error !== ''): ?>
    <div class="notice notice-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!$hasBuku): ?>
    <div class="empty-state">
      <p style="margin:0;">Belum ada buku dengan stok tersedia. Petugas perlu menambah stok sebelum peminjaman dibuat.</p>
    </div>
<?php elseif (!$hasSiswa): ?>
    <div class="empty-state">
      <p style="margin:0;">Belum ada data Siswa. Tambahkan Siswa sebelum membuat peminjaman.</p>
    </div>
<?php elseif (!$hasPetugas): ?>
    <div class="empty-state">
      <p style="margin:0;">Belum ada data Petugas untuk melayani peminjaman.</p>
    </div>
<?php else: ?>
<div class="form-panel">
  <form method="POST" action="page/peminjaman/peminjamantambah_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <?php if ($isStudent): ?>
            <tr>
                <td><label>Peminjam</label></td>
                <td>
                  <strong><?= htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <span class="role-badge role-badge-siswa" style="margin-left:8px;">Akun Anda</span>
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <td><label for="select-petugas">Petugas yang Melayani</label></td>
                <td>
                    <select id="select-petugas" name="idpetugas" required>
                        <option value="">-- Pilih Petugas --</option>
                        <?php while ($p = mysqli_fetch_assoc($q_petugas)): ?>
                            <option value="<?= (int) $p['idpetugas'] ?>" <?= ($_SESSION['role'] === 'petugas' && (int) $_SESSION['user_id'] === (int) $p['idpetugas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['namapetugas'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="select-siswa">Nama Siswa</label></td>
                <td>
                    <select id="select-siswa" name="idsiswa" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = mysqli_fetch_assoc($q_siswa)): ?>
                            <option value="<?= (int) $s['idsiswa'] ?>"><?= htmlspecialchars($s['namasiswa'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td><label for="select-buku">Buku yang Dipinjam</label></td>
            <td>
                <select id="select-buku" name="idbuku" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while ($b = mysqli_fetch_assoc($q_buku)): ?>
                        <option value="<?= (int) $b['idbuku'] ?>"><?= htmlspecialchars($b['judul'], ENT_QUOTES, 'UTF-8') ?> (Tersedia: <?= (int) $b['stok'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="input-batas">Batas Pengembalian</label></td>
            <td>
              <input id="input-batas" type="date" name="batas_peminjaman" min="<?= date('Y-m-d') ?>" required>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Konfirmasi Peminjaman">
                <a class="button button-secondary" href="index.php?halaman=peminjaman">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
<?php endif; ?>
