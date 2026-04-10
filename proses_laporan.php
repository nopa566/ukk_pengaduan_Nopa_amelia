<?php
// Memulai session
session_start();

// Menghubungkan ke file functions.php (koneksi & helper query)
require 'functions.php';

// ================= CEK LOGIN & ROLE =================
// Hanya admin yang boleh akses halaman ini
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// ================= AMBIL ID =================
// Mengambil ID dari URL, default 0 jika tidak ada
$id = $_GET["id"] ?? 0;

// ================= AMBIL DATA =================
// Mengambil data laporan + data siswa + kategori (JOIN)
$data = query("
    SELECT * FROM aspirasi 
    JOIN siswa ON aspirasi.nis = siswa.nis 
    JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
    WHERE id_aspirasi = $id
");

// Ambil data pertama
$laporan = $data[0] ?? null;

// ================= CEK DATA =================
// Jika data tidak ditemukan
if (!$laporan) {
    echo "<script>alert('Data tidak ditemukan!'); document.location.href='admin.php';</script>";
    exit;
}

// ================= PROSES UPDATE =================
if (isset($_POST["update"])) {

    // Ambil data dari form
    $status_baru = $_POST["status"];
    $feedback_baru = htmlspecialchars($_POST["feedback"]);

    // Query update status & feedback
    $query = "
        UPDATE aspirasi SET 
        status = '$status_baru', 
        feedback = '$feedback_baru' 
        WHERE id_aspirasi = $id
    ";

    // Jalankan query
    mysqli_query($conn, $query);

    // Cek apakah ada perubahan
    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>alert('Laporan Berhasil Ditanggapi!'); document.location.href='data_laporan.php';</script>";
    } else {
        echo "<script>alert('Gagal update / Tidak ada perubahan data'); document.location.href='data_laporan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Laporan | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* CSS GLOBAL (Tema Indigo & Slate) */
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

        /* CARD BOX DENGAN AKSEN WARNA DI SAMPING KIRI */
        .card-box {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            /* AKSEN WARNA DI SAMPING KIRI */
            border-left: 8px solid #4f46e5; 
            border-top: none;
            border-right: none;
            border-bottom: none;
            max-width: 900px;
        }

        /* TOMBOL KEMBALI */
        .btn-back {
            background-color: #6366f1;
            color: white;
            border-radius: 10px;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background-color: #4f46e5;
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        /* FORM ELEMENT STYLING */
        label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
        }

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

        /* PREVIEW FOTO */
        .img-preview {
            border-radius: 12px;
            max-width: 250px;
            border: 3px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* RESPONSIVE LAYOUT */
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
        <h4><i class="bi bi-shield-check"></i> Admin Web</h4>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
        <div class="nav-container">
            <a href="admin.php"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="data_laporan.php" class="active"><i class="bi bi-chat-left-text"></i> Data Laporan</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i> Tanggapi Laporan</h3>
            <a href="data_laporan.php" class="btn-back shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card-box">
            <form method="post">
                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <label>Pelapor</label>
                        <input type="text" class="form-control" value="<?= $laporan['nama']; ?>" disabled>
                    </div>

                    <div class="col-md-6">
                        <label>Kategori</label>
                        <input type="text" class="form-control" value="<?= $laporan['nama_kategori']; ?>" disabled>
                    </div>

                    <div class="col-12">
                        <label>Isi Laporan</label>
                        <textarea class="form-control" rows="4" disabled><?= $laporan['keterangan']; ?></textarea>
                    </div>

                    <div class="col-12">
                        <label>Bukti Foto</label>
                        <?php if (!empty($laporan['foto'])): ?>
                            <img src="assets/img/<?= $laporan['foto']; ?>" class="img-preview" alt="Bukti">
                        <?php else: ?>
                            <div class="text-muted small italic"><i class="bi bi-image-alt"></i> Tidak ada foto lampiran</div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 my-2"><hr style="opacity: 0.1;"></div>

                    <div>
                        <h5 class="fw-bold text-primary mb-1">Respon Admin</h5>
                        <p class="text-muted small mb-0">Tentukan status dan berikan feedback laporan ini.</p>
                    </div>

                    <div class="col-md-6">
                        <label>Status Laporan</label>
                        <select name="status" class="form-select shadow-sm">
                            <option value="Menunggu" <?= ($laporan['status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="Proses" <?= ($laporan['status'] == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                            <option value="Selesai" <?= ($laporan['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label>Feedback / Tanggapan</label>
                        <textarea name="feedback" class="form-control shadow-sm" rows="4" placeholder="Tulis tanggapan untuk siswa di sini..."><?= $laporan['feedback']; ?></textarea>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" name="update" class="btn btn-primary px-4 py-2 shadow" style="border-radius: 10px; background-color: #4f46e5; border: none;">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Tanggapan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>