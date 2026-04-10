<?php
// MULAI SESSION UNTUK AKSES DATA LOGIN
session_start();

// PANGGIL FILE FUNCTIONS (KONEKSI DATABASE + FUNCTION QUERY)
require 'functions.php';

// CEK LOGIN DAN ROLE HARUS SISWA
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

// AMBIL DATA DARI SESSION
$nis = $_SESSION['id'];    // ID siswa (NIS)
$nama = $_SESSION['nama']; // Nama lengkap siswa

// CEK JIKA TOMBOL KIRIM DIKLIK
if (isset($_POST["kirim"])) {

    // AMBIL DATA DARI FORM DAN BERSIHKAN (SECURITY)
    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]); 
    $id_kategori = $_POST["id_kategori"];                   
    $lokasi = htmlspecialchars($_POST["lokasi"]);           
    $tanggal = date("Y-m-d");                               
    $status = "Menunggu";                                   

    // PROSES UPLOAD FOTO
    $nama_foto = $_FILES['foto']['name'];      
    $tmp = $_FILES['foto']['tmp_name'];        
    $folder = "assets/img/";                  

    // CEK JIKA USER MENGUNGGAH FILE
    if ($nama_foto != "") {
        move_uploaded_file($tmp, $folder . $nama_foto); 
    }

    // SIMPAN KE DATABASE
    mysqli_query($conn, "INSERT INTO aspirasi 
    VALUES (NULL,'$nis','$id_kategori','$lokasi','$isi_laporan','$nama_foto','$tanggal','$status','')");

    // NOTIFIKASI DAN REDIRECT
    echo "<script>alert('Laporan berhasil dikirim!');document.location.href='siswa.php';</script>";
}

// AMBIL DATA KATEGORI UNTUK DROPDOWN
$kategori = query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tulis Laporan | Sistem Pengaduan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* CSS GLOBAL (IDENTIK DENGAN DASHBOARD ADMIN) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* SIDEBAR STYLING */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background-color: #4f46e5;
            padding: 24px;
            z-index: 100;
            color: white;
            transition: all 0.3s;
        }

        .sidebar h4 {
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
        }

        /* CONTENT AREA */
        .content {
            margin-left: 260px;
            padding: 40px;
            transition: all 0.3s;
        }

        /* FORM CARD BOX DENGAN VARIASI WARNA */
        .card-box {
            background: white;
            border-radius: 16px;
            padding: 30px;
            /* Tambahan warna di border atas dan bayangan halus */
            border-top: 5px solid #4f46e5; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 800px;
        }

        /* LABEL FORM (BERSIH TANPA ICON) */
        label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }

        /* INPUT STYLING */
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background-color: #fff;
        }

        /* TOMBOL KIRIM */
        .btn-kirim {
            background-color: #4f46e5;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-kirim:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* RESPONSIVE LAYOUT UNTUK HP */
        @media (max-width: 992px) {
            .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
            .sidebar h4, .sidebar hr { display: none; }
            .sidebar .nav-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
            .sidebar a { margin-bottom: 0; padding: 10px; font-size: 14px; }
            .content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4><i class="bi bi-buildings"></i> Siswa Panel</h4>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
        
        <div class="nav-container">
            <a href="siswa.php" class="active">
                <i class="bi bi-pencil-square"></i> Tulis Laporan
            </a>

            <a href="riwayat.php">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>

            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="content">

        <div class="mb-4">
            <h5 class="text-muted mb-1">Selamat Datang,</h5>
            <h3 class="fw-bold" style="color: #0f172a;">
                <i class="bi bi-person-circle text-primary me-2"></i><?= $nama; ?>
            </h3>
        </div>

        <div class="card-box">
            <div class="mb-4">
                <h5 class="fw-bold text-primary mb-1">Tulis Laporan Baru</h5>
                <p class="text-muted small">Suara Anda penting untuk sekolah yang lebih baik.</p>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Pilih Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="" disabled selected>Pilih kategori laporan...</option>
                            <?php foreach($kategori as $k): ?>
                            <option value="<?= $k['id_kategori']; ?>">
                                <?= $k['nama_kategori']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Lokasi Kejadian</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Kantin, Lab, atau Kelas" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Detail Laporan</label>
                        <textarea name="isi_laporan" class="form-control" rows="5" placeholder="Tuliskan keluhan atau saran Anda secara detail..." required></textarea>
                    </div>

                    <div class="col-12 mb-4">
                        <label>Unggah Bukti (Foto)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>

                    <div class="col-12">
                        <button type="submit" name="kirim" class="btn btn-kirim w-100">
                            <i class="bi bi-send-fill me-2"></i> Kirim Laporan Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>