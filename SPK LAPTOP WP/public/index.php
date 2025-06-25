<?php include '../includes/config.php'; ?>
<?php include 'menu.php'; ?>
<link rel="stylesheet" href="assets/style.css">

<div class="container">

    <h2>Selamat Datang di Sistem SPK Metode Weighted Product</h2>
    <p>Sistem ini membantu Anda menentukan alternatif terbaik berdasarkan kriteria dan sub kriteria yang sudah ditentukan.</p>

    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 25px;">
        
        <div style="flex: 1; min-width: 200px; background: #007BFF; color: white; padding: 20px; border-radius: 8px;">
            <h3>Total Alternatif</h3>
            <p style="font-size: 24px;">
                <?php echo $pdo->query("SELECT COUNT(*) FROM alternatif")->fetchColumn(); ?>
            </p>
        </div>

        <div style="flex: 1; min-width: 200px; background: #28a745; color: white; padding: 20px; border-radius: 8px;">
            <h3>Total Kriteria</h3>
            <p style="font-size: 24px;">
                <?php echo $pdo->query("SELECT COUNT(*) FROM kriteria")->fetchColumn(); ?>
            </p>
        </div>

        <div style="flex: 1; min-width: 200px; background: #17a2b8; color: white; padding: 20px; border-radius: 8px;">
            <h3>Total Sub Kriteria</h3>
            <p style="font-size: 24px;">
                <?php echo $pdo->query("SELECT COUNT(*) FROM sub_kriteria")->fetchColumn(); ?>
            </p>
        </div>
    </div>

    <h3 style="margin-top: 30px;">Panduan Penggunaan:</h3>
    <ol>
        <li>Masukkan data <b>Kriteria</b> & <b>Sub Kriteria</b></li>
        <li>Masukkan <b>Alternatif</b></li>
        <li>Input <b>Nilai Alternatif</b> terhadap Sub Kriteria</li>
        <li>Lihat hasil perhitungan menggunakan metode Weighted Product</li>
    </ol>

</div>
