<?php
require_once __DIR__ . '/guard.php';
$data = mysqli_query($conn, "SELECT * FROM tbl_penulis");
?>

<header class="page-heading">
  <p class="eyebrow">Data referensi</p>
  <h1>Data Penulis</h1>
  <p>Daftar penulis buku yang terdaftar dalam sistem perpustakaan.</p>
</header>

<div class="table-toolbar">
  <nav class="related-data" aria-label="Navigasi katalog">
    <a href="index.php?halaman=buku">← Kembali ke Buku</a>
    <a href="index.php?halaman=penerbit">Data Penerbit</a>
  </nav>

  <?php if (auth_can_manage('penulis')): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=penulistambah">+ Tambah Penulis</a>
    </div>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Penulis</th>
            <th>No. HP</th>
            <?php if (auth_can_manage('penulis')): ?>
                <th class="no-sort">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= (int) $row['idpenulis'] ?></td>
                <td><strong><?= htmlspecialchars($row['namapenulis'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><?= htmlspecialchars($row['hppenulis'], ENT_QUOTES, 'UTF-8') ?></td>
                <?php if (auth_can_manage('penulis')): ?>
                    <td class="jangan-putus">
                        <a class="button button-quiet" href="index.php?halaman=penulisedit&id=<?= (int) $row['idpenulis'] ?>">Edit</a>
                        <form class="table-action-form" method="post" action="page/buku/penulis/penulishapus.php" onsubmit="return confirm('Yakin hapus data ini?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['idpenulis'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
