<?php include '../includes/config.php'; ?>
<?php include 'menu.php'; ?>

<h2>Simulasi Preferensi Sub-Kriteria (Bobot 1-5)</h2>

<form method="post">
    <p>Pilih salah satu sub-kriteria per kriteria sesuai preferensi Anda:</p>

    <?php
    $kriteria = $pdo->query("SELECT * FROM kriteria ORDER BY id")->fetchAll();

    foreach ($kriteria as $k):
        $sub = $pdo->prepare("SELECT * FROM sub_kriteria WHERE id_kriteria = ? ORDER BY bobot");
        $sub->execute([$k['id']]);
    ?>
    <h4><?= $k['nama'] ?> (<?= ucfirst($k['tipe']) ?>)</h4>
    <select name="preferensi[<?= $k['id'] ?>]" required>
        <option value="">-- Pilih Sub-Kriteria --</option>
        <?php foreach ($sub as $s): ?>
            <option value="<?= $s['bobot'] ?>"><?= $s['nama'] ?> (Bobot <?= $s['bobot'] ?>)</option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <?php endforeach; ?>

    <button name="proses">Hitung Ranking</button>
</form>

<?php
if (isset($_POST['proses'])):

    $inputBobot = $_POST['preferensi'];
    $totalPoin = array_sum($inputBobot);

    if ($totalPoin == 0) {
        echo "<p><b>Total bobot tidak boleh 0!</b></p>";
    } else {
        foreach ($inputBobot as $id_k => $bobot) {
            $bobotFinal[$id_k] = $bobot / $totalPoin;
        }

        $nilaiAll = $pdo->query("
            SELECT n.id_alternatif, n.id_kriteria, n.nilai, a.nama AS alternatif
            FROM nilai n
            JOIN alternatif a ON a.id = n.id_alternatif
        ")->fetchAll();

        $alternatifData = [];
        foreach ($nilaiAll as $row) {
            $alternatifData[$row['id_alternatif']]['nama'] = $row['alternatif'];
            $alternatifData[$row['id_alternatif']]['nilai'][$row['id_kriteria']] = $row['nilai'];
        }

        $vectorS = [];
        foreach ($alternatifData as $idAlt => $alt) {
            $prod = 1;
            foreach ($kriteria as $k) {
                $val = $alt['nilai'][$k['id']] ?? 1;
                $bobot = $bobotFinal[$k['id']];
                $prod *= pow($val, $k['tipe'] == 'cost' ? -$bobot : $bobot);
            }
            $vectorS[$idAlt] = $prod;
        }

        $totalS = array_sum($vectorS);

        $ranking = [];
        foreach ($alternatifData as $idAlt => $alt) {
            $ranking[] = [
                'nama' => $alt['nama'],
                'V' => $vectorS[$idAlt] / $totalS
            ];
        }

        usort($ranking, fn($a, $b) => $b['V'] <=> $a['V']);
?>

<h3>Hasil Perankingan Berdasarkan Preferensi Anda</h3>
<table>
<thead><tr><th>Ranking</th><th>Alternatif</th><th>Nilai V</th></tr></thead>
<tbody>
<?php $r = 1; foreach ($ranking as $a): ?>
<tr>
    <td><?= $r++ ?></td>
    <td><?= $a['nama'] ?></td>
    <td><?= round($a['V'], 4) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<form method="post">
    <button name="reset">Reset Preferensi</button>
</form>

<?php
    }
endif;

if (isset($_POST['reset'])) {
    header("Location: preferensi.php");
    exit;
}
?>
