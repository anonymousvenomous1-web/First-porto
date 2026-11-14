<?php
require_once __DIR__ . '/guard.php';
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM tbl_buku WHERE idbuku = '$id'");
$data = mysqli_fetch_assoc($query);

$q_penulis = mysqli_query($conn, "SELECT * FROM tbl_penulis");
$q_penerbit = mysqli_query($conn, "SELECT * FROM tbl_penerbit");
?>

<header class="page-heading">
  <p class="eyebrow">Pembaruan katalog</p>
  <h1>Edit Data Buku</h1>
  <p>Perbarui judul, stok, kategori, gambar sampul, penulis, atau penerbit buku.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/bukuedit_aksi.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="idbuku" value="<?= (int) $data['idbuku']; ?>">
    
    <table>
        <tr>
            <td><label for="edit-judul">Judul Buku</label></td>
            <td><input id="edit-judul" type="text" name="judul" value="<?= htmlspecialchars($data['judul'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="edit-gambar">Gambar / Cover</label></td>
            <td>
                <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../../img/buku/' . $data['gambar'])): ?>
                    <div class="img-preview-box">
                        <img src="img/buku/<?= htmlspecialchars($data['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="Cover Buku" style="width: 70px; height: 90px; object-fit: cover; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input id="edit-gambar" type="file" name="gambar" accept="image/*">
                <small style="display:block; margin-top:6px; color: var(--muted);">Biarkan kosong jika tidak ingin mengganti gambar (Maks. 2MB).</small>
            </td>
        </tr>
        <tr>
            <td><label for="edit-stok">Jumlah Stok</label></td>
            <td><input id="edit-stok" type="number" name="stok" value="<?= (int) $data['stok']; ?>" required></td>
        </tr>
        <tr>
            <td><label for="edit-kategori">Kategori</label></td>
            <td><input id="edit-kategori" type="text" name="kategori" value="<?= htmlspecialchars($data['kategori'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="edit-penulis">Penulis</label></td>
            <td>
                <select id="edit-penulis" name="idpenulis" required>
                    <?php while($p = mysqli_fetch_assoc($q_penulis)): ?>
                        <option value="<?= $p['idpenulis']; ?>" <?= ($data['idpenulis'] == $p['idpenulis']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($p['namapenulis'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="edit-penerbit">Penerbit</label></td>
            <td>
                <select id="edit-penerbit" name="idpenerbit" required>
                    <?php while($pb = mysqli_fetch_assoc($q_penerbit)): ?>
                        <option value="<?= $pb['idpenerbit']; ?>" <?= ($data['idpenerbit'] == $pb['idpenerbit']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($pb['namapenerbit'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="update" value="Simpan Perubahan">
                <a class="button button-secondary" href="index.php?halaman=buku">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
