<?php
require_once __DIR__ . '/auth.php';
requireLogin();
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['status'=>'error','message'=>'Akses ditolak.']);
    exit;
}

$border = $_POST['border'] ?? '';
if (!$border) {
    echo json_encode(['status'=>'error','message'=>'Nama border tidak diberikan.']);
    exit;
}

$path = dirname(__DIR__, 2) . '/hertahub.domain/uploads/borders/' . $border;
if (!file_exists($path)) {
    echo json_encode(['status'=>'error','message'=>'File border tidak ditemukan.']);
    exit;
}

if (unlink($path)) {
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','message'=>'Gagal menghapus file border.']);
}
