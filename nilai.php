<?php include '../includes/config.php'; include 'menu.php'; ?>
<h2>Daftar Nilai Alternatif per Kriteria</h2>

<table border="1" cellpadding="5" cellspacing="0">
<thead>
<tr>
    <th>Alternatif</th>
    <?php
    // Ambil semua kriteria untuk header tabel
    $kriteria = $pdo->query("SELECT * FROM kriteria ORDER BY id");
    $list_kriteria = [];
    foreach ($kriteria as $k) {
        echo "<th>{$k['nama']}</th>";
        $list_kriteria[] = $k;
    }
    ?>
</tr>
</thead>
<tbody>
<?php
// Ambil semua alternatif
$alternatif = $pdo->query("SELECT * FROM alternatif ORDER BY id");

// Ambil semua nilai sekaligus
$nilai_all = $pdo->query("
    SELECT n.id_alternatif, n.id_kriteria, sk.nama AS sub_nama, n.nilai
    FROM nilai n
    JOIN sub_kriteria sk ON sk.id = n.id_sub_kriteria
")->fetchAll(PDO::FETCH_ASSOC);

// Mapping nilai: [id_alternatif][id_kriteria] = ['sub_nama' => ..., 'nilai' => ...]
$nilai_map = [];
foreach ($nilai_all as $n) {
    $nilai_map[$n['id_alternatif']][$n['id_kriteria']] = [
        'sub_nama' => $n['sub_nama'],
        'nilai' => $n['nilai']
    ];
}

foreach ($alternatif as $alt) {
    echo "<tr>";
    echo "<td>{$alt['nama']}</td>";

    foreach ($list_kriteria as $k) {
        if (isset($nilai_map[$alt['id']][$k['id']])) {
            $row = $nilai_map[$alt['id']][$k['id']];
            echo "<td>{$row['sub_nama']} (Nilai: {$row['nilai']})</td>";
        } else {
            echo "<td>-</td>";
        }
    }

    echo "</tr>";
}
?>
</tbody>
</table>
