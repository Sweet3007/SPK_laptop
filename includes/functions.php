<?php
function normalisasiBobot($pdo) {
  $total = $pdo->query("SELECT SUM(bobot) FROM kriteria")->fetchColumn();
  $stmt = $pdo->query("SELECT id, bobot FROM kriteria");
  $normal = [];
  foreach ($stmt as $k) {
    $normal[$k['id']] = $k['bobot'] / $total;
  }
  return $normal;
}
function getKriteria($pdo) {
    return $pdo->query("SELECT * FROM kriteria")->fetchAll(PDO::FETCH_ASSOC);
}

function getSubKriteria($pdo) {
    return $pdo->query("SELECT * FROM sub_kriteria")->fetchAll(PDO::FETCH_ASSOC);
}

function getAlternatif($pdo) {
    return $pdo->query("SELECT * FROM alternatif")->fetchAll(PDO::FETCH_ASSOC);
}

function getNilai($pdo, $id_alt, $id_sub) {
    $stmt = $pdo->prepare("SELECT nilai FROM nilai WHERE id_alternatif=? AND id_sub_kriteria=?");
    $stmt->execute([$id_alt, $id_sub]);
    return $stmt->fetchColumn() ?: 1;
}
?>
