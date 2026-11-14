<?php
require_once __DIR__ . '/guard.php';
$q_penulis = mysqli_query($conn, "SELECT * FROM tbl_penulis");
$q_penerbit = mysqli_query($conn, "SELECT * FROM tbl_penerbit");
?>

<header class="page-heading">
  <p class="eyebrow">Formulir buku</p>
  <h1>Tambah Data Buku</h1>
  <p>Masukkan judul, cover, stok, kategori, penulis, dan penerbit untuk katalog baru.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/buku/bukutambah_aksi.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <tr>
            <td><label for="input-judul">Judul Buku</label></td>
            <td><input id="input-judul" type="text" name="judul" placeholder="Masukkan Judul Buku" required></td>
        </tr>
        <tr>
            <td><label for="input-gambar">Gambar / Cover</label></td>
            <td>
                <input id="input-gambar" type="file" name="gambar" accept="image/*">
                <small style="display:block; margin-top:6px; color: var(--muted);">Format: JPG, JPEG, PNG, GIF, WEBP (Maks. 2MB)</small>
            </td>
        </tr>
        <tr>
            <td><label for="input-stok">Jumlah Stok</label></td>
            <td><input id="input-stok" type="number" name="stok" placeholder="Masukkan Jumlah Stok" required></td>
        </tr>
        <tr>
            <td><label for="input-kategori">Kategori</label></td>
            <td><input id="input-kategori" type="text" name="kategori" placeholder="Contoh: Fiksi, Komik, Sains, dll" required></td>
        </tr>
        <tr>
            <td><label for="select-penulis">Penulis</label></td>
            <td>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                  <div style="flex: 1; min-width: 200px;">
                    <select id="select-penulis" name="idpenulis" required>
                        <option value="">-- Pilih Penulis --</option>
                        <?php while($p = mysqli_fetch_assoc($q_penulis)): ?>
                            <option value="<?= $p['idpenulis']; ?>"><?= htmlspecialchars($p['namapenulis'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                  </div>
                  <a class="button button-secondary" href="index.php?halaman=penulis">Atur Penulis</a>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="select-penerbit">Penerbit</label></td>
            <td>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                  <div style="flex: 1; min-width: 200px;">
                    <select id="select-penerbit" name="idpenerbit" required>
                        <option value="">-- Pilih Penerbit --</option>
                        <?php while($pb = mysqli_fetch_assoc($q_penerbit)): ?>
                            <option value="<?= $pb['idpenerbit']; ?>"><?= htmlspecialchars($pb['namapenerbit'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                  </div>
                  <a class="button button-secondary" href="index.php?halaman=penerbit">Atur Penerbit</a>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Simpan Data Buku">
                <a class="button button-secondary" href="index.php?halaman=buku">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
