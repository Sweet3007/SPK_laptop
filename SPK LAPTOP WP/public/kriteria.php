<?php include '../includes/config.php'; include 'menu.php'; ?>
<h2>Data Kriteria</h2>

<!-- Form Tambah Kriteria -->
<form method="post">
    <input name="nama" placeholder="Nama" required>
    <select name="tipe">
        <option value="benefit">Benefit</option>
        <option value="cost">Cost</option>
    </select>
    <button name="save">Simpan</button>
</form>

<?php
// Proses Simpan Kriteria Baru
if (isset($_POST['save'])) {
    $stmt = $pdo->prepare("INSERT INTO kriteria (nama, bobot, tipe) VALUES (?, 0, ?)");
    $stmt->execute([$_POST['nama'], $_POST['tipe']]);
    header("Location: kriteria.php?atur=1");
    exit;
}

// Proses Update Bobot Massal
if (isset($_POST['update_bobot'])) {
    $total = array_sum($_POST['bobot']);
    if (abs($total - 1) > 0.001) {
        echo "<p style='color:red'><b>Total bobot harus 1! Total saat ini: $total</b></p>";
    } else {
        foreach ($_POST['bobot'] as $id => $bobot) {
            $stmt = $pdo->prepare("UPDATE kriteria SET bobot = ? WHERE id = ?");
            $stmt->execute([$bobot, $id]);
        }
        echo "<p style='color:green'><b>Bobot berhasil diperbarui!</b></p>";
    }
}
?>

<!-- Tabel + Form Edit Bobot -->
<form method="post">
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>Tipe</th>
      <th>Bobot</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $data = $pdo->query("SELECT * FROM kriteria");
    $no = 1;
    foreach ($data as $d): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $d['nama'] ?></td>
      <td><?= ucfirst($d['tipe']) ?></td>
      <td>
        <input type="number" step="0.01" min="0" max="1" class="bobot-input" name="bobot[<?= $d['id'] ?>]" value="<?= $d['bobot'] ?>" required>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Tampilan Total Bobot -->
<p style="margin-top:10px;">
    <b>Total Bobot:</b> <span id="total-bobot">0</span>
</p>

<button name="update_bobot" style="margin-top:10px;">Simpan Bobot</button>
</form>

<script>
// Hitung total bobot real-time
function hitungTotalBobot() {
    let total = 0;
    document.querySelectorAll('.bobot-input').forEach(el => {
        total += parseFloat(el.value) || 0;
    });
    document.getElementById('total-bobot').innerText = total.toFixed(2);
}

// Jalankan saat halaman load & saat input berubah
document.addEventListener('DOMContentLoaded', hitungTotalBobot);
document.querySelectorAll('.bobot-input').forEach(el => {
    el.addEventListener('input', hitungTotalBobot);
});
</script>
