<?php
// ================= CEGAH SESSION DOUBLE =================
// Mengecek apakah session belum aktif
if (session_status() === PHP_SESSION_NONE) {
    // Jika belum aktif, maka jalankan session_start()
    session_start();
}

// ================= KONEKSI DATABASE =================
// Membuat koneksi ke database MySQL
$conn = mysqli_connect("localhost", "root", "", "db_pengaduan_sekolah");

// ================= CEK KONEKSI =================
// Jika koneksi gagal
if (!$conn) {
    // Tampilkan error dan hentikan program
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// =========================
// QUERY HELPER (FUNCTION QUERY)
// =========================
function query($query) {
    global $conn; // Mengambil variabel koneksi dari luar function

    // Menjalankan query
    $result = mysqli_query($conn, $query);

    // Jika query gagal
    if(!$result){
        // Tampilkan error query
        die("Query error: " . mysqli_error($conn));
    }

    // Jika query bukan SELECT (INSERT, UPDATE, DELETE)
    if($result === true){
        return true; // hanya return true (tidak ada data)
    }

    // Jika query SELECT
    $rows = [];
    // Ambil semua data satu per satu
    while($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row; // masukkan ke array
    }

    // Kembalikan semua data
    return $rows;
}

// =========================
// FUNCTION LOGIN (ADMIN + SISWA)
// =========================
function cek_login($username, $password) {
    global $conn; // Mengambil koneksi database

    // ================= CEK ADMIN =================
    // Query untuk mencari admin berdasarkan username & password
    $q_admin = mysqli_query($conn, "
        SELECT * FROM admin 
        WHERE username='$username' 
        AND password='$password'
    ");

    // Jika data admin ditemukan
    if(mysqli_num_rows($q_admin) > 0){
        // Ambil data admin
        $data = mysqli_fetch_assoc($q_admin);

        // Set session login
        $_SESSION['login'] = true;
        $_SESSION['role'] = 'admin'; // role admin
        $_SESSION['nama'] = $data['nama_petugas']; // nama admin
        $_SESSION['id'] = $data['id_admin']; // id admin

        return "admin"; // return role
    }

    // ================= CEK SISWA =================
    // Query untuk mencari siswa berdasarkan NIS & password
    $q_siswa = mysqli_query($conn, "
        SELECT * FROM siswa 
        WHERE nis='$username' 
        AND password='$password'
    ");

    // Jika data siswa ditemukan
    if(mysqli_num_rows($q_siswa) > 0){
        // Ambil data siswa
        $data = mysqli_fetch_assoc($q_siswa);

        // Set session login
        $_SESSION['login'] = true;
        $_SESSION['role'] = 'siswa'; // role siswa
        $_SESSION['nama'] = $data['nama']; // nama siswa
        $_SESSION['id'] = $data['nis']; // NIS siswa

        return "siswa"; // return role
    }

    // ================= LOGIN GAGAL =================
    // Jika tidak ditemukan di admin maupun siswa
    return false;
}
?>