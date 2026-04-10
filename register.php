<?php
// Menghubungkan ke file functions.php (koneksi database)
require 'functions.php';

// ================= PROSES REGISTER =================
// Mengecek apakah tombol register ditekan
if (isset($_POST['register'])) {

    // Mengambil data dari form dan menghindari karakter berbahaya (XSS sederhana)
    $nama = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']); // NIS sebagai username
    $kelas = htmlspecialchars($_POST['kelas']);
    $password = htmlspecialchars($_POST['password']);

    // ================= CEK USERNAME =================
    // Mengecek apakah NIS sudah terdaftar di database
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$username'");

    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, tampilkan alert dan kembali ke halaman login
        echo "<script>
                alert('NIS sudah terdaftar!');
                document.location.href = 'index.php';
              </script>";
        exit;
    }

    // ================= INSERT DATA =================
    // Menambahkan data siswa baru ke database
    $query = "INSERT INTO siswa (nis, nama, kelas, password) 
              VALUES ('$username', '$nama', '$kelas', '$password')";

    // Menjalankan query insert
    mysqli_query($conn, $query);

    // ================= CEK HASIL =================
    // Jika berhasil ditambahkan
    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>
                alert('Akun berhasil dibuat!');
                document.location.href = 'index.php';
              </script>";
    } else {
        // Jika gagal
        echo "<script>alert('Gagal daftar!');</script>";
    }
}
?>