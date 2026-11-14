<?php
require_once __DIR__ . '/guard.php';
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tbl_penulis WHERE idpenulis = '$id'");
$data = mysqli_fetch_assoc($query);
?>

<header class="page-heading">
  <p class="eyebrow">Pembaruan referensi</p>
  <h1>Edit Data Penulis</h1>
  <p>Perbarui informasi nama atau kontak penulis buku.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/penulis/penulisedit_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="idpenulis" value="<?= $data['idpenulis']; ?>">
    <table>
        <tr>
            <td><label for="nama-penulis">Nama Penulis</label></td>
            <td><input id="nama-penulis" type="text" name="namapenulis" value="<?= htmlspecialchars($data['namapenulis'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="hp-penulis">Nomor HP</label></td>
            <td><input id="hp-penulis" type="text" name="hppenulis" value="<?= htmlspecialchars($data['hppenulis'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="update" value="Simpan Perubahan">
                <a class="button button-secondary" href="index.php?halaman=penulis">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
