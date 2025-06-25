<?php
$db_url = "postgresql://laptop_database_owner:npg_O5Yn7creSDGU@ep-withered-recipe-a1qjctkw-pooler.ap-southeast-1.aws.neon.tech/laptop_database?sslmode=require";
$url = parse_url($db_url);

$host = $url['host'];
$db   = ltrim($url['path'], '/');
$user = $url['user'];
$pass = $url['pass'];
$port = 5432;
$sslmode = "require";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}