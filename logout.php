<?php
// Memulai session (biar bisa dihapus / dihancurkan)
session_start();

// Mengosongkan semua data session (array session jadi kosong)
$_SESSION = [];

// Menghapus semua variabel session
session_unset();

// Menghancurkan session sepenuhnya (logout total)
session_destroy();

// ================= REDIRECT =================
// Setelah logout, arahkan kembali ke halaman login
header("Location: index.php");
exit;
?>