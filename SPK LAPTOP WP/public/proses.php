<?php
include '../includes/config.php';

$hasil = [];

// 1. Ambil bobot tiap kriteria
$kriteria = [];
$total_bobot = 0;

$query = $pdo->query("SELECT id, bobot, tipe FROM kriteria");
foreach ($query as $k) {
    $kriteria[$k['id']] = [
        'bobot' => $k['bobot'],
        'tipe'  => $k['tipe']
    ];
    $total_bobot += $k['bobot'];
}

// 2. Normalisasi bobot
foreach ($kriteria as &$k) {
    $k['bobot'] = $k['bobot'] / $total_bobot;
    if ($k['tipe'] === 'cost') {
        $k['bobot'] = -$k['bobot'];
    }
}

// 3. Ambil semua alternatif dan hitung WP
$alternatif = $pdo->query("SELECT * FROM alternatif")->fetchAll();

foreach ($alternatif as $alt) {
    $V = 1;

    $stmt = $pdo->prepare("
        SELECT n.nilai, n.id_kriteria
        FROM nilai n
        WHERE n.id_alternatif = ?
    ");
    $stmt->execute([$alt['id']]);

    foreach ($stmt as $row) {
        $nilai = $row['nilai'];
        $bobot = $kriteria[$row['id_kriteria']]['bobot'];
        $V *= pow($nilai, $bobot);
    }

    $hasil[] = [
        'nama' => $alt['nama'],
        'V'    => $V
    ];
}

// 4. Normalisasi Nilai V
$totalV = array_sum(array_column($hasil, 'V'));
foreach ($hasil as &$h) {
    $h['V'] = $h['V'] / $totalV;
}

// 5. Urutkan hasil
usort($hasil, fn($a, $b) => $b['V'] <=> $a['V']);

// Output JSON
header('Content-Type: application/json');
echo json_encode($hasil);
