<?php require_once __DIR__ . '/guard.php'; ?>

<header class="page-heading">
  <p class="eyebrow">Formulir penulis</p>
  <h1>Tambah Data Penulis</h1>
  <p>Masukkan nama lengkap penulis dan nomor telepon yang dapat dihubungi.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/penulis/penulistambah_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <tr>
            <td><label for="nama-penulis">Nama Penulis</label></td>
            <td><input id="nama-penulis" type="text" name="namapenulis" placeholder="Masukkan Nama Penulis" required></td>
        </tr>
        <tr>
            <td><label for="hp-penulis">Nomor HP</label></td>
            <td><input id="hp-penulis" type="text" name="hppenulis" placeholder="Contoh: 0812345678" required></td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Simpan Penulis">
                <a class="button button-secondary" href="index.php?halaman=penulis">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
