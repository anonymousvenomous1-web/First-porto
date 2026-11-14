<?php
require_once __DIR__ . '/guard.php';
$data = mysqli_query($conn, "SELECT * FROM tbl_siswa");
?>

<header class="page-heading">
  <p class="eyebrow">Anggota perpustakaan</p>
  <h1>Data Siswa</h1>
  <p>Daftar anggota siswa yang terdaftar untuk layanan dan peminjaman buku perpustakaan.</p>
</header>

<div class="table-toolbar">
  <div></div>
  <?php if (auth_can_manage('siswa')): ?>
    <div>
      <a class="button button-primary" href="index.php?halaman=siswatambah">+ Tambah Siswa</a>
    </div>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="no-sort">Foto</th>
            <th>NIS</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Alamat</th>
            <th>Kelas</th>
            <th>No. HP</th>
            <?php if (auth_can_manage('siswa')): ?>
                <th class="no-sort">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= (int) $row['idsiswa'] ?></td>
                <td>
                    <?php if (!empty($row['foto']) && file_exists(__DIR__ . '/../../img/siswa/' . $row['foto'])): ?>
                        <a href="img/siswa/<?= htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" title="Lihat foto asli">
                            <img class="table-img-siswa" src="img/siswa/<?= htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto <?= htmlspecialchars($row['namasiswa'], ENT_QUOTES, 'UTF-8') ?>">
                        </a>
                    <?php else: ?>
                        <span class="no-img-text">Tidak ada</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['nis'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['nisn'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><strong><?= htmlspecialchars($row['namasiswa'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><span class="genre-pill"><?= htmlspecialchars($row['username'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
                <td><?= htmlspecialchars($row['alamat'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['kelas'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['hpsiswa'], ENT_QUOTES, 'UTF-8') ?></td>
                <?php if (auth_can_manage('siswa')): ?>
                    <td class="jangan-putus">
                        <a class="button button-quiet" href="index.php?halaman=siswaedit&id=<?= (int) $row['idsiswa'] ?>">Edit</a>
                        <form class="table-action-form" method="post" action="page/siswa/siswahapus.php" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['idsiswa'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
  </table>
</div>
