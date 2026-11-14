<?php
require_once __DIR__ . '/guard.php';
$query = "SELECT tbl_buku.*, tbl_penulis.namapenulis, tbl_penerbit.namapenerbit 
          FROM tbl_buku
          INNER JOIN tbl_penulis ON tbl_buku.idpenulis = tbl_penulis.idpenulis
          INNER JOIN tbl_penerbit ON tbl_buku.idpenerbit = tbl_penerbit.idpenerbit
          ORDER BY tbl_buku.idbuku ASC";

$data = mysqli_query($conn, $query);
?>

<header class="page-heading">
  <p class="eyebrow">Katalog pustaka</p>
  <h1>Data Buku</h1>
  <p>Koleksi pustaka yang tersedia dalam sistem informasi perpustakaan.</p>
</header>

<div class="table-toolbar">
  <nav class="related-data" aria-label="Data terkait buku">
    <a href="index.php?halaman=penulis">Data Penulis</a>
    <a href="index.php?halaman=penerbit">Data Penerbit</a>
  </nav>

  <?php if (auth_can_manage('buku')): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=bukutambah">+ Tambah Buku</a>
    </div>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="no-sort">Gambar</th>
            <th>Judul</th>
            <th>Stok</th>
            <th>Kategori</th>
            <th>Nama Penulis</th>
            <th>Nama Penerbit</th>
            <?php if (auth_can_manage('buku')): ?>
                <th class="no-sort">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= (int) $row['idbuku'] ?></td>
                <td>
                    <?php if (!empty($row['gambar']) && file_exists(__DIR__ . '/../../img/buku/' . $row['gambar'])): ?>
                        <a href="img/buku/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" title="Lihat gambar asli">
                            <img class="table-img-buku" src="img/buku/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover <?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?>">
                        </a>
                    <?php else: ?>
                        <span class="no-img-text">Tidak ada</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><?= (int) $row['stok'] ?></td>
                <td><span class="genre-pill"><?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8') ?></span></td>
                <td><?= htmlspecialchars($row['namapenulis'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['namapenerbit'], ENT_QUOTES, 'UTF-8') ?></td>
                <?php if (auth_can_manage('buku')): ?>
                    <td class="jangan-putus">
                        <a class="button button-quiet" href="index.php?halaman=bukuedit&id=<?= (int) $row['idbuku'] ?>">Edit</a>
                        <form class="table-action-form" method="post" action="page/buku/bukuhapus.php" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['idbuku'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
