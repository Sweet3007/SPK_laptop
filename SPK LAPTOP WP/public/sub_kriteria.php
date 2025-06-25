<?php include '../includes/config.php'; include 'menu.php'; ?>
<h2>Data Sub-Kriteria</h2>

<!-- Form Input Sub-Kriteria -->
<form method="post" style="margin-bottom: 30px;">
    <select name="id_kriteria" required>
        <option value="">--Pilih Kriteria--</option>
        <?php foreach ($pdo->query("SELECT * FROM kriteria") as $k): ?>
            <option value="<?= $k['id'] ?>" <?= isset($_POST['id_kriteria']) && $_POST['id_kriteria'] == $k['id'] ? 'selected' : '' ?>>
                <?= $k['nama'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input name="nama" placeholder="Nama Sub-Kriteria" required>
    <input name="bobot" type="number" step="0.01" placeholder="Bobot Lokal" required>
    <button name="save">Simpan</button>
</form>

<?php
// Proses Simpan
if (isset($_POST['save'])) {
    $stmt = $pdo->prepare("INSERT INTO sub_kriteria (id_kriteria, nama, bobot) VALUES (?,?,?)");
    $stmt->execute([$_POST['id_kriteria'], $_POST['nama'], $_POST['bobot']]);
    header("Location: sub_kriteria.php?id_kriteria=".$_POST['id_kriteria']);
    exit;
}

// Ambil ID Kriteria untuk filter tabel
$id_filter = isset($_GET['id_kriteria']) ? $_GET['id_kriteria'] : '';
?>

<!-- Filter Tabel Sub-Kriteria -->
<form method="get" style="margin-bottom: 15px;">
    <label>Filter Kriteria:</label>
    <select name="id_kriteria" onchange="this.form.submit()">
        <option value="">--Tampilkan Semua--</option>
        <?php foreach ($pdo->query("SELECT * FROM kriteria") as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $id_filter == $k['id'] ? 'selected' : '' ?>>
                <?= $k['nama'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<!-- Tabel Data Sub-Kriteria -->
<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Sub-Kriteria</th>
      <th>Kriteria</th>
      <th>Bobot Lokal</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $no = 1;
    $query = "SELECT sk.*, k.nama AS kriteria FROM sub_kriteria sk JOIN kriteria k ON k.id = sk.id_kriteria";
    if ($id_filter) $query .= " WHERE sk.id_kriteria = $id_filter";

    foreach ($pdo->query($query) as $s): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $s['nama'] ?></td>
      <td><?= $s['kriteria'] ?></td>
      <td><?= $s['bobot'] ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
