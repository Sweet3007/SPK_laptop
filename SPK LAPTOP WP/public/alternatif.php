<?php include '../includes/config.php'; include 'menu.php'; ?>
<h2>Tambah Alternatif & Input Nilai</h2>

<form method="post">
    <label><b>Nama Alternatif:</b></label>
    <input type="text" name="nama" required><br><br>

    <?php
    $kriteria = $pdo->query("SELECT * FROM kriteria");
    foreach ($kriteria as $k):
        echo "<label><b>{$k['nama']}</b></label><br>";

        $sub = $pdo->prepare("SELECT * FROM sub_kriteria WHERE id_kriteria = ? ORDER BY id");
        $sub->execute([$k['id']]);

        echo "<select name='sub_kriteria[{$k['id']}]' required>";
        echo "<option value=''>-- Pilih Sub Kriteria --</option>";

        $urut = 1;
        foreach ($sub as $s) {
            echo "<option value='{$s['id']}'>{$s['nama']} (Nilai: {$urut})</option>";
            $urut++;
        }

        echo "</select><br><br>";
    endforeach;
    ?>

    <button name="simpan">Simpan Alternatif & Nilai</button>
</form>

<?php
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];

    // Simpan Alternatif
    $stmt = $pdo->prepare("INSERT INTO alternatif (nama) VALUES (?) RETURNING id");
    $stmt->execute([$nama]);
    $id_alt = $stmt->fetchColumn();

    // Simpan Nilai untuk setiap kriteria
    foreach ($_POST['sub_kriteria'] as $id_kriteria => $id_sub) {
        
        // Hitung nilai berdasar urutan sub_kriteria
        $stmt2 = $pdo->prepare("
            SELECT id FROM sub_kriteria 
            WHERE id_kriteria = ? 
            ORDER BY id
        ");
        $stmt2->execute([$id_kriteria]);

        $nilai = 1;
        foreach ($stmt2 as $row) {
            if ($row['id'] == $id_sub) break;
            $nilai++;
        }

        // Simpan data ke tabel nilai
        $stmt3 = $pdo->prepare("
            INSERT INTO nilai (id_alternatif, id_kriteria, id_sub_kriteria, nilai) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt3->execute([$id_alt, $id_kriteria, $id_sub, $nilai]);
    }

    echo "<p><b>Data Alternatif & Semua Nilai berhasil disimpan!</b></p>";
}
?>
