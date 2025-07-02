<?php
// ---------------------------------
// Koneksi PDO ke database

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? die('DB_NAME tidak ditemukan di .env');
$DB_USER = $_ENV['DB_USER'] ?? die('DB_USER tidak ditemukan di .env');
$DB_PASS = $_ENV['DB_PASS'] ?? die('DB_PASS tidak ditemukan di .env');

$DB_PASS = $key;

try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Bila koneksi gagal, tampilkan pesan
    exit("Database connection failed: " . $e->getMessage());
}
