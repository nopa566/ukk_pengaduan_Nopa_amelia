<?php
// Memulai session untuk menyimpan data login
session_start();

// Menghubungkan ke file functions.php (koneksi database & function login)
include 'functions.php';

// ================= PROSES LOGIN =================
// Mengecek apakah tombol login ditekan
if(isset($_POST['login'])){
    // Mengambil input dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Memanggil function cek_login
    $role = cek_login($username, $password);

    // Jika login sebagai admin
    if($role == "admin"){
        header("Location: admin.php"); // arahkan ke dashboard admin
        exit;
    }
    // Jika login sebagai siswa
    elseif($role == "siswa"){
        header("Location: siswa.php"); // arahkan ke dashboard siswa
        exit;
    }
    // Jika login gagal
    else {
        $error = "Username / NIS atau Password salah!";
    }
}

// ================= PROSES DAFTAR =================
// Mengecek apakah tombol daftar ditekan
if(isset($_POST['daftar'])){
    // Mengambil data dari form daftar
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $kelas = $_POST['kelas'];

    // Mengecek apakah NIS sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek) > 0){
        $error = "NIS sudah terdaftar!";
    } else {

        // Menyimpan data ke database
        $insert = mysqli_query($conn, "
            INSERT INTO siswa (nis, nama, password, kelas)
            VALUES ('$nis','$nama','$password','$kelas')
        ");

        // Jika berhasil daftar
        if($insert){
            $success = "Akun berhasil dibuat, silakan login!";
        } else {
            $error = "Gagal daftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Pengaduan Sekolah</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* CSS GLOBAL */
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            /* Background serasi dengan Dashboard (Slate & Indigo Accent) */
            background-color: #f1f5f9;
            background-image: radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%), 
                              radial-gradient(at 100% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        /* CARD STYLING */
        .card-login {
            width: 100%;
            max-width: 420px;
            background: white;
            border: none;
            border-radius: 24px;
            padding: 40px 30px;
            /* Shadow halus agar terlihat 'floating' */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        /* ICON LOGO (Clean Version) */
        .icon-school {
            font-size: 64px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            display: inline-block;
        }

        /* TYPOGRAPHY */
        .title {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.5rem;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 35px;
        }

        /* FORM ELEMENTS */
        label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
            color: #94a3b8;
            border-radius: 12px 0 0 12px;
            border-color: #e2e8f0;
        }

        .form-control {
            background-color: #f8fafc;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 12px;
            font-size: 0.95rem;
            border-color: #e2e8f0;
            color: #1e293b;
        }

        /* Input tanpa group (Daftar) */
        .form-control.no-group {
            border-left: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #4f46e5;
            background-color: white;
        }

        /* TOMBOL UNGU (Serasi Sidebar Admin) */
        .btn-purple {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-purple:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            color: white;
        }

        /* ALERT CUSTOM */
        .alert {
            border: none;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* TOGGLE LINK */
        .toggle-link {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .toggle-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 576px) {
            .card-login {
                padding: 30px 20px;
            }
            .title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>

<div class="card-login text-center">

    <i class="bi bi-mortarboard-fill icon-school"></i>

    <h4 class="title">Aplikasi Pengaduan Sekolah</h4>
    <p class="subtitle" id="formDesc">Silakan login untuk melanjutkan</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 mb-4">
            <i class="bi bi-exclamation-circle me-1"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success py-2 mb-4">
            <i class="bi bi-check-circle me-1"></i> <?= $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <div class="mb-3 text-start">
            <label>Username / NIS</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan ID Anda" required>
            </div>
        </div>

        <div class="mb-4 text-start">
            <label>Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="loginPass" class="form-control" placeholder="••••••••" required>
                <span class="input-group-text" onclick="togglePass()" style="cursor:pointer; border-radius: 0 12px 12px 0; border-left: none;">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <button type="submit" name="login" class="btn btn-purple w-100">Login Sekarang</button>

        <div class="mt-4 small text-muted">
            Belum punya akun? <span class="toggle-link" onclick="showDaftar()">Daftar Akun</span>
        </div>
    </form>

    <form method="POST" id="daftarForm" style="display:none;">
        <div class="row g-2 text-start">
            <div class="col-6 mb-2">
                <label>NIS</label>
                <input type="text" name="nis" class="form-control no-group" placeholder="Nomor Induk" required>
            </div>
            <div class="col-6 mb-2">
                <label>Kelas</label>
                <input type="text" name="kelas" class="form-control no-group" placeholder="Kelas" required>
            </div>
        </div>

        <div class="mb-2 text-start">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control no-group" placeholder="Nama Lengkap" required>
        </div>

        <div class="mb-4 text-start">
            <label>Password</label>
            <input type="password" name="password" class="form-control no-group" placeholder="Buat Password" required>
        </div>

        <button type="submit" name="daftar" class="btn btn-purple w-100">Daftar Sekarang</button>

        <div class="mt-4 small text-muted">
            Sudah punya akun? <span class="toggle-link" onclick="showLogin()">Kembali Login</span>
        </div>
    </form>

</div>

<script>
// ================= JS FUNCTIONS =================

// Fungsi menampilkan form daftar
function showDaftar(){
    document.getElementById('loginForm').style.display='none';
    document.getElementById('daftarForm').style.display='block';
    document.getElementById('formDesc').innerText = 'Lengkapi data untuk membuat akun siswa';
}

// Fungsi menampilkan form login
function showLogin(){
    document.getElementById('loginForm').style.display='block';
    document.getElementById('daftarForm').style.display='none';
    document.getElementById('formDesc').innerText = 'Silakan login untuk melanjutkan';
}

// Fungsi toggle lihat password
function togglePass(){
    const passInput = document.getElementById("loginPass");
    const eyeIcon = document.getElementById("eyeIcon");
    
    if (passInput.type === "password") {
        passInput.type = "text";
        eyeIcon.classList.replace("bi-eye", "bi-eye-slash");
    } else {
        passInput.type = "password";
        eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>