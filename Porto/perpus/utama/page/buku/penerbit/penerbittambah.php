<?php require_once __DIR__ . '/guard.php'; ?>

<header class="page-heading">
  <p class="eyebrow">Formulir penerbit</p>
  <h1>Tambah Data Penerbit</h1>
  <p>Masukkan nama penerbit dan kota asal penerbit.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/penerbit/penerbittambah_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <tr>
            <td><label for="nama-penerbit">Nama Penerbit</label></td>
            <td><input id="nama-penerbit" type="text" name="namapenerbit" placeholder="Masukkan Nama Penerbit" required></td>
        </tr>
        <tr>
            <td><label for="kota-penerbit">Kota</label></td>
            <td><input id="kota-penerbit" type="text" name="kota" placeholder="Masukkan Kota Penerbit" required></td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Simpan Penerbit">
                <a class="button button-secondary" href="index.php?halaman=penerbit">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
