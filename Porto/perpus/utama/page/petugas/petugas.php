<?php
require_once __DIR__ . '/guard.php';
$query = "SELECT * FROM tbl_petugas";
$data = mysqli_query($conn, $query);
?>

<header class="page-heading">
  <p class="eyebrow">Pengelola sistem</p>
  <h1>Data Petugas</h1>
  <p>Daftar staf petugas perpustakaan yang memiliki wewenang sirkulasi dan pengelolaan data.</p>
</header>

<div class="table-toolbar">
  <div></div>
  <?php if (auth_can_manage('petugas')): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=petugastambah">+ Tambah Petugas</a>
    </div>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Petugas</th>
            <th>Username</th>
            <th>No. HP</th>
            <?php if (auth_can_manage('petugas')): ?>
                <th class="no-sort">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= (int) $row['idpetugas'] ?></td>
                <td><strong><?= htmlspecialchars($row['namapetugas'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><span class="genre-pill"><?= htmlspecialchars($row['username'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
                <td><?= htmlspecialchars($row['hppetugas'], ENT_QUOTES, 'UTF-8') ?></td>
                <?php if (auth_can_manage('petugas')): ?>
                    <td class="jangan-putus">
                        <a class="button button-quiet" href="index.php?halaman=petugasedit&id=<?= (int) $row['idpetugas'] ?>">Edit</a>
                        <form class="table-action-form" method="post" action="page/petugas/petugashapus.php" onsubmit="return confirm('Yakin ingin menghapus data petugas ini?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['idpetugas'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
