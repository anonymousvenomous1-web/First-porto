<?php
require_once __DIR__ . '/guard.php';
$data = mysqli_query($conn, "SELECT * FROM tbl_penerbit");
?>

<header class="page-heading">
  <p class="eyebrow">Data referensi</p>
  <h1>Data Penerbit</h1>
  <p>Daftar badan penerbit pustaka yang tercatat dalam sistem perpustakaan.</p>
</header>

<div class="table-toolbar">
  <nav class="related-data" aria-label="Navigasi katalog">
    <a href="index.php?halaman=buku">← Kembali ke Buku</a>
    <a href="index.php?halaman=penulis">Data Penulis</a>
  </nav>

  <?php if (auth_can_manage('penerbit')): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=penerbittambah">+ Tambah Penerbit</a>
    </div>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Penerbit</th>
            <th>Kota</th>
            <?php if (auth_can_manage('penerbit')): ?>
                <th class="no-sort">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= (int) $row['idpenerbit'] ?></td>
                <td><strong><?= htmlspecialchars($row['namapenerbit'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><?= htmlspecialchars($row['kota'], ENT_QUOTES, 'UTF-8') ?></td>
                <?php if (auth_can_manage('penerbit')): ?>
                    <td class="jangan-putus">
                        <a class="button button-quiet" href="index.php?halaman=penerbitedit&id=<?= (int) $row['idpenerbit'] ?>">Edit</a>
                        <form class="table-action-form" method="post" action="page/buku/penerbit/penerbithapus.php" onsubmit="return confirm('Yakin hapus data ini?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['idpenerbit'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
