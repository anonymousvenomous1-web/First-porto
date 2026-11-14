<?php
require_once __DIR__ . '/guard.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Siswa tidak valid.');
}
$statement = $conn->prepare('SELECT * FROM tbl_siswa WHERE idsiswa = ?');
$statement->bind_param('i', $id);
$statement->execute();
$data = $statement->get_result()->fetch_assoc();
if (!$data) {
    http_response_code(404);
    exit('Data Siswa tidak ditemukan.');
}
?>

<header class="page-heading">
  <p class="eyebrow">Pembaruan anggota</p>
  <h1>Edit Data Siswa</h1>
  <p>Perbarui identitas siswa, kelas, nomor kontak, serta kata sandi akun.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/siswa/siswaedit_aksi.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="idsiswa" value="<?= (int) $data['idsiswa'] ?>">
    
    <table>
        <tr>
            <td><label for="siswa-foto">Foto Siswa</label></td>
            <td>
                <?php if (!empty($data['foto']) && file_exists(__DIR__ . '/../../img/siswa/' . $data['foto'])): ?>
                    <div class="img-preview-box">
                        <img class="table-img-siswa" src="img/siswa/<?= htmlspecialchars($data['foto'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto Siswa" style="width: 70px; height: 90px; object-fit: cover; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <input id="siswa-foto" type="file" name="foto" accept="image/*">
                <small style="display:block; margin-top:6px; color: var(--muted);">Biarkan kosong jika tidak ingin mengubah foto (Maks. 2MB).</small>
            </td>
        </tr>
        <tr>
            <td><label for="siswa-nis">NIS</label></td>
            <td><input id="siswa-nis" type="text" name="nis" value="<?= htmlspecialchars($data['nis'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="siswa-nisn">NISN</label></td>
            <td><input id="siswa-nisn" type="text" name="nisn" value="<?= htmlspecialchars($data['nisn'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="siswa-nama">Nama Siswa</label></td>
            <td><input id="siswa-nama" type="text" name="namasiswa" value="<?= htmlspecialchars($data['namasiswa'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="siswa-alamat">Alamat</label></td>
            <td><input id="siswa-alamat" type="text" name="alamat" value="<?= htmlspecialchars($data['alamat'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="siswa-kelas">Kelas</label></td>
            <td><input id="siswa-kelas" type="text" name="kelas" value="<?= htmlspecialchars($data['kelas'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="siswa-hp">No. HP</label></td>
            <td><input id="siswa-hp" type="tel" name="hpsiswa" value="<?= htmlspecialchars($data['hpsiswa'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="20" required></td>
        </tr>
        <tr>
            <td><label for="siswa-username">Username Akun</label></td>
            <td><input id="siswa-username" type="text" name="username" value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" minlength="3" maxlength="50" pattern="[A-Za-z0-9._\-]{3,50}" autocomplete="username"></td>
        </tr>
        <tr>
            <td><label for="siswa-password">Kata Sandi Baru</label></td>
            <td>
              <div class="password-field">
                <input id="siswa-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password">
              </div>
              <small style="display:block; margin-top:6px; color: var(--muted);">Kosongkan username untuk menonaktifkan akun. Kata sandi kosong mempertahankan kata sandi lama.</small>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="update" value="Simpan Perubahan">
                <a class="button button-secondary" href="index.php?halaman=siswa">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
