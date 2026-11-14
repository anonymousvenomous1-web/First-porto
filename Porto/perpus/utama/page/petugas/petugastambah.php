<?php require_once __DIR__ . '/guard.php'; ?>

<header class="page-heading">
  <p class="eyebrow">Formulir petugas</p>
  <h1>Tambah Data Petugas</h1>
  <p>Daftarkan petugas perpustakaan baru beserta hak akses sistem informasi.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/petugas/petugastambah_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <table>
        <tr>
            <td><label for="petugas-nama">Nama Lengkap</label></td>
            <td><input id="petugas-nama" type="text" name="namapetugas" placeholder="Masukkan Nama Petugas" required></td>
        </tr>
        <tr>
            <td><label for="petugas-hp">No. HP</label></td>
            <td><input id="petugas-hp" type="tel" name="hppetugas" placeholder="Contoh: 08123456789" maxlength="20" required></td>
        </tr>
        <tr>
            <td><label for="petugas-username">Username Akun</label></td>
            <td><input id="petugas-username" type="text" name="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9._\-]{3,50}" autocomplete="username" placeholder="Username untuk masuk sistem" required></td>
        </tr>
        <tr>
            <td><label for="petugas-password">Kata Sandi</label></td>
            <td>
              <div class="password-field">
                <input id="petugas-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Minimal 8 karakter" required>
              </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="simpan" value="Simpan Petugas">
                <a class="button button-secondary" href="index.php?halaman=petugas">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
