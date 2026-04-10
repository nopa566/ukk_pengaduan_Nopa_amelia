<?php
// Memulai session untuk menyimpan data login user
session_start();

// Menghubungkan ke file functions.php (biasanya berisi koneksi database & function query)
require 'functions.php';

// ================= CEK LOGIN =================
// Mengecek apakah user sudah login atau belum
if (!isset($_SESSION['login'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: index.php");
    exit;
}

// ================= AMBIL ID =================
// Mengambil ID dari URL (contoh: batal.php?id=5)
$id = $_GET['id'];

// ================= UPDATE STATUS =================
// Mengubah status laporan menjadi "Dibatalkan" berdasarkan ID
query("UPDATE aspirasi SET status='Dibatalkan' WHERE id_aspirasi='$id'");

// ================= REDIRECT =================
// Setelah update berhasil, kembali ke halaman riwayat
header("Location: riwayat.php");
exit;
?>