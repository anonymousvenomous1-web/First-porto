<?php require_once __DIR__ . '/guard.php'; ?>

<header class="page-heading">
  <p class="eyebrow">Formulir anggota</p>
  <h1>Tambah Data Siswa</h1>
  <p>Daftarkan siswa baru beserta data identitas dan pembuatan akun perpustakaan.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/siswa/siswatambah_aksi.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <tr>
            <td><label for="siswa-foto">Foto Siswa</label></td>
            <td>
                <input id="siswa-foto" type="file" name="foto" accept="image/*">
                <small style="display:block; margin-top:6px; color: var(--muted);">Format: JPG, JPEG, PNG, GIF, WEBP (Maks. 2MB)</small>
            </td>
        </tr>
        <tr>
            <td><label for="siswa-nis">NIS</label></td>
            <td><input id="siswa-nis" type="text" name="nis" placeholder="Masukkan NIS" required></td>
        </tr>
        <tr>
            <td><label for="siswa-nisn">NISN</label></td>
            <td><input id="siswa-nisn" type="text" name="nisn" placeholder="Masukkan NISN" required></td>
        </tr>
        <tr>
            <td><label for="siswa-nama">Nama Lengkap</label></td>
            <td><input id="siswa-nama" type="text" name="namasiswa" placeholder="Masukkan Nama Siswa" required></td>
        </tr>
        <tr>
            <td><label for="siswa-alamat">Alamat</label></td>
            <td><input id="siswa-alamat" type="text" name="alamat" placeholder="Masukkan Alamat Lengkap" required></td>
        </tr>
        <tr>
            <td><label for="siswa-kelas">Kelas</label></td>
            <td><input id="siswa-kelas" type="text" name="kelas" placeholder="Contoh: XI RPL 1" required></td>
        </tr>
        <tr>
            <td><label for="siswa-hp">No. HP</label></td>
            <td><input id="siswa-hp" type="tel" name="hpsiswa" placeholder="Contoh: 081234567890" maxlength="20" required></td>
        </tr>
        <tr>
            <td><label for="siswa-username">Username Akun</label></td>
            <td><input id="siswa-username" type="text" name="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9._\-]{3,50}" autocomplete="username" placeholder="Username untuk masuk sistem" required></td>
        </tr>
        <tr>
            <td><label for="siswa-password">Kata Sandi</label></td>
            <td>
              <div class="password-field">
                <input id="siswa-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Minimal 8 karakter" required>
              </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Simpan Data Siswa">
                <a class="button button-secondary" href="index.php?halaman=siswa">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
