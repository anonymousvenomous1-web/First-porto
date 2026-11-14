<?php
require_once __DIR__ . '/guard.php';
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tbl_penerbit WHERE idpenerbit = '$id'");
$data = mysqli_fetch_assoc($query);
?>

<header class="page-heading">
  <p class="eyebrow">Pembaruan referensi</p>
  <h1>Edit Data Penerbit</h1>
  <p>Perbarui informasi nama dan kota penerbit pustaka.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/penerbit/penerbitedit_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="idpenerbit" value="<?= $data['idpenerbit']; ?>">
    <table>
        <tr>
            <td><label for="nama-penerbit">Nama Penerbit</label></td>
            <td><input id="nama-penerbit" type="text" name="namapenerbit" value="<?= htmlspecialchars($data['namapenerbit'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="kota-penerbit">Kota</label></td>
            <td><input id="kota-penerbit" type="text" name="kota" value="<?= htmlspecialchars($data['kota'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="update" value="Simpan Perubahan">
                <a class="button button-secondary" href="index.php?halaman=penerbit">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
