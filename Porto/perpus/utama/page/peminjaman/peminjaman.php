<?php
require_once __DIR__ . '/guard.php';
$isStudent = ($_SESSION['role'] ?? '') === 'siswa';
$query = "SELECT tbl_pinjam.idpinjam, tbl_petugas.namapetugas, tbl_siswa.namasiswa,
                 tbl_pinjam.waktupinjam, tbl_pinjam.batas_peminjaman,
                 GROUP_CONCAT(tbl_buku.judul SEPARATOR ', ') AS judul
          FROM tbl_pinjam
          LEFT JOIN tbl_petugas ON tbl_pinjam.idpetugas = tbl_petugas.idpetugas
          INNER JOIN tbl_siswa ON tbl_pinjam.idsiswa = tbl_siswa.idsiswa
          INNER JOIN tbl_pinjamdetail ON tbl_pinjam.idpinjam = tbl_pinjamdetail.idpinjam
          INNER JOIN tbl_buku ON tbl_pinjamdetail.idbuku = tbl_buku.idbuku";

if ($isStudent) {
    $query .= ' WHERE tbl_pinjam.idsiswa = ?';
}
$query .= ' GROUP BY tbl_pinjam.idpinjam ORDER BY tbl_pinjam.waktupinjam DESC';

$statement = $conn->prepare($query);
if ($isStudent) {
    $studentId = (int) $_SESSION['user_id'];
    $statement->bind_param('i', $studentId);
}
$statement->execute();
$data = $statement->get_result();
?>

<header class="page-heading">
  <p class="eyebrow">Sirkulasi & peminjaman</p>
  <h1>Data Peminjaman</h1>
  <p><?= $isStudent ? 'Riwayat peminjaman buku untuk akun Anda beserta batas waktu pengembalian.' : 'Kelola catatan transaksi sirkulasi buku perpustakaan.' ?></p>
</header>

<div class="table-toolbar">
  <div></div>
  <?php if (auth_can_create_peminjaman()): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=peminjamantambah">+ Ajukan Peminjaman</a>
    </div>
  <?php endif; ?>
</div>

<?php if ($data->num_rows === 0): ?>
  <div class="empty-state">
    <p style="margin:0;"><?= $isStudent ? 'Anda belum memiliki peminjaman aktif. Pilih buku dari katalog untuk mengajukan pinjaman.' : 'Belum ada transaksi peminjaman buku tercatat di sistem.' ?></p>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Petugas</th>
                <th>Nama Siswa</th>
                <th>Buku yang Dipinjam</th>
                <th>Waktu Pinjam</th>
                <th>Batas Pengembalian</th>
                <?php if (auth_can_manage('peminjaman')): ?>
                    <th class="no-sort">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['idpinjam'] ?></td>
                    <td><?= htmlspecialchars($row['namapetugas'] ?? 'Pinjaman mandiri', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><strong><?= htmlspecialchars($row['namasiswa'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['waktupinjam'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <?php if ($row['batas_peminjaman'] === null): ?>
                        <span class="genre-pill">Belum ditentukan</span>
                      <?php else: ?>
                        <span class="rental-status rental-status-approved"><?= htmlspecialchars($row['batas_peminjaman'], ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endif; ?>
                    </td>
                    <?php if (auth_can_manage('peminjaman')): ?>
                        <td class="jangan-putus">
                            <form class="table-action-form" method="post" action="page/peminjaman/peminjamanhapus.php" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus transaksi ini? Stok buku akan dikembalikan.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $row['idpinjam'] ?>">
                                <button type="submit">Hapus / Selesai</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  </div>
<?php endif; ?>
