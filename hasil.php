<?php include '../includes/config.php'; include 'menu.php'; ?>

<h2>Hasil Perhitungan Metode Weighted Product</h2>

<?php
class WeightedProduct {
    private $alternatives = [];
    private $criteria = [];
    private $weights = [];
    
    public function __construct($alternatives, $criteria, $weights) {
        $this->alternatives = $alternatives;
        $this->criteria = $criteria;
        $this->weights = $weights;
    }
    
    private function normalizeWeights() {
        $sum = array_sum($this->weights);
        $normalized = [];
        foreach ($this->weights as $weight) {
            $normalized[] = $weight / $sum;
        }
        return $normalized;
    }
    
    private function calculateVectorS() {
        $normalizedWeights = $this->normalizeWeights();
        $vectorS = [];
        
        foreach ($this->alternatives as $altIndex => $alternative) {
            $product = 1;
            foreach ($alternative['values'] as $critIndex => $value) {
                $exponent = $this->criteria[$critIndex]['type'] == 'benefit' 
                    ? $normalizedWeights[$critIndex] 
                    : -$normalizedWeights[$critIndex];
                $product *= pow($value, $exponent);
            }
            $vectorS[$altIndex] = $product;
        }
        
        return $vectorS;
    }
    
    public function calculate() {
        $vectorS = $this->calculateVectorS();
        $sumVectorS = array_sum($vectorS);
        $results = [];
        
        foreach ($vectorS as $altIndex => $value) {
            $results[$this->alternatives[$altIndex]['name']] = $value / $sumVectorS;
        }
        
        arsort($results);
        
        return $results;
    }
}

// --- Ambil Data ---
$criteria = [];
$weights = [];
$stmt = $pdo->query("SELECT * FROM kriteria ORDER BY id");
foreach ($stmt as $k) {
    $criteria[] = [
        'id'   => $k['id'],
        'name' => $k['nama'],
        'type' => $k['tipe']
    ];
    $weights[] = $k['bobot'];
}

$alternatives = [];

// Ambil seluruh nilai sekaligus (lebih efisien)
$nilai_all = $pdo->query("
    SELECT * FROM nilai
")->fetchAll(PDO::FETCH_ASSOC);

// Mapping nilai: [id_alternatif][id_kriteria] = nilai
$nilai_map = [];
foreach ($nilai_all as $n) {
    $nilai_map[$n['id_alternatif']][$n['id_kriteria']] = $n['nilai'];
}

// Ambil alternatif dan nilai per kriteria
$stmt = $pdo->query("SELECT * FROM alternatif ORDER BY id");
foreach ($stmt as $alt) {
    $values = [];
    foreach ($criteria as $k) {
        $val = isset($nilai_map[$alt['id']][$k['id']]) 
            ? $nilai_map[$alt['id']][$k['id']] 
            : 1; // Default nilai 1 jika kosong
        $values[] = $val;
    }
    $alternatives[] = [
        'name' => $alt['nama'],
        'values' => $values
    ];
}

// --- Proses WP ---
$wp = new WeightedProduct($alternatives, $criteria, $weights);
$results = $wp->calculate();
?>

<?php if ($results): ?>
<table border="1" cellpadding="5" cellspacing="0">
<thead>
<tr><th>Peringkat</th><th>Alternatif</th><th>Nilai V</th></tr>
</thead>
<tbody>
<?php $rank = 1; foreach ($results as $name => $value): ?>
<tr>
<td><?= $rank++ ?></td>
<td><?= $name ?></td>
<td><?= round($value, 4) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p><b>Data belum tersedia atau perhitungan gagal.</b></p>
<?php endif; ?>
