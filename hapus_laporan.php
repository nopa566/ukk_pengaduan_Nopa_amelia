<?php
// Memulai session untuk menyimpan data login user
session_start();

// Menghubungkan ke file functions.php (koneksi database & function query)
require 'functions.php';

// ================= CEK LOGIN =================
// Mengecek apakah user sudah login
if (!isset($_SESSION['login'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: index.php");
    exit;
}

// ================= AMBIL ID =================
// Mengambil ID dari URL (contoh: hapus.php?id=5)
$id = $_GET['id'];

// ================= HAPUS DATA =================
// Menghapus data laporan berdasarkan ID
query("DELETE FROM aspirasi WHERE id_aspirasi='$id'");

// ================= REDIRECT =================
// Setelah data dihapus, kembali ke halaman riwayat
header("Location: riwayat.php");
exit;
?>