<?php
require_once __DIR__ . '/guard.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Data Petugas tidak valid.');
}
$statement = $conn->prepare('SELECT * FROM tbl_petugas WHERE idpetugas = ?');
$statement->bind_param('i', $id);
$statement->execute();
$data = $statement->get_result()->fetch_assoc();
if (!$data) {
    http_response_code(404);
    exit('Data Petugas tidak ditemukan.');
}
?>

<header class="page-heading">
  <p class="eyebrow">Pembaruan pengelola</p>
  <h1>Edit Data Petugas</h1>
  <p>Perbarui informasi identitas, nomor telepon, atau kata sandi petugas.</p>
</header>

<div class="form-panel">
  <form method="POST" action="page/petugas/petugasedit_aksi.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="idpetugas" value="<?= (int) $data['idpetugas'] ?>">
    
    <table>
        <tr>
            <td><label for="petugas-nama">Nama Petugas</label></td>
            <td><input id="petugas-nama" type="text" name="namapetugas" value="<?= htmlspecialchars($data['namapetugas'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
        </tr>
        <tr>
            <td><label for="petugas-hp">No. HP</label></td>
            <td><input id="petugas-hp" type="tel" name="hppetugas" value="<?= htmlspecialchars($data['hppetugas'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="20" required></td>
        </tr>
        <tr>
            <td><label for="petugas-username">Username Akun</label></td>
            <td><input id="petugas-username" type="text" name="username" value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" minlength="3" maxlength="50" pattern="[A-Za-z0-9._\-]{3,50}" autocomplete="username"></td>
        </tr>
        <tr>
            <td><label for="petugas-password">Kata Sandi Baru</label></td>
            <td>
              <div class="password-field">
                <input id="petugas-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password">
              </div>
              <small style="display:block; margin-top:6px; color: var(--muted);">Kosongkan username untuk menonaktifkan akun. Kata sandi kosong mempertahankan kata sandi lama.</small>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
              <div class="form-actions" style="margin-top: 10px;">
                <input class="button button-primary" type="submit" name="update" value="Simpan Perubahan">
                <a class="button button-secondary" href="index.php?halaman=petugas">Batal</a>
              </div>
            </td>
        </tr>
    </table>
  </form>
</div>
