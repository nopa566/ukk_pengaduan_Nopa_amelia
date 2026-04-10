<?php
// Memulai session untuk menyimpan data login user
session_start();

// Menghubungkan file functions.php (berisi koneksi DB & function query)
require 'functions.php';

// ================= CEK LOGIN =================
// Mengecek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    // Jika belum login / bukan admin, arahkan ke halaman login
    header("Location: index.php");
    exit;
}

// ================= STATISTIK =================
// Menghitung total data aspirasi selain yang dibatalkan
$total = count(query("SELECT * FROM aspirasi WHERE status != 'Dibatalkan'"));

// Menghitung jumlah laporan dengan status Menunggu
$menunggu = count(query("SELECT * FROM aspirasi WHERE status='Menunggu'"));

// Menghitung jumlah laporan dengan status Proses
$proses = count(query("SELECT * FROM aspirasi WHERE status='Proses'"));

// Menghitung jumlah laporan dengan status Selesai
$selesai = count(query("SELECT * FROM aspirasi WHERE status='Selesai'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Pengaduan Sekolah</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Pengaturan umum background body */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* ================= SIDEBAR STYLING ================= */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background-color: #4f46e5;
            padding: 24px;
            z-index: 100;
            transition: all 0.3s;
            color: white;
        }

        .sidebar h4 {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Navigasi Link Sidebar */
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
            transition: 0.2s;
        }

        /* Efek Hover dan Aktif pada Sidebar */
        .sidebar a.active, .sidebar a:hover {
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
        }

        /* ================= MAIN CONTENT STYLING ================= */
        .content {
            margin-left: 260px;
            padding: 40px;
            transition: all 0.3s;
        }

        .page-title {
            font-weight: 700;
            color: #0f172a;
        }

        /* ================= STAT CARD STYLING (Warna Gradien) ================= */
        .stat-card {
            border: none;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            color: white;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        /* Variasi Warna Box Statistik */
        .card-total { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .card-menunggu { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .card-proses { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .card-selesai { background: linear-gradient(135deg, #10b981, #059669); }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0;
        }

        .icon-box {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        /* ================= MONITORING BOX STYLING ================= */
        .detail-box {
            background: white;
            border: none;
            border-radius: 16px;
            padding: 30px;
            margin-top: 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .detail-box h5 {
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 25px;
        }

        /* Custom Progress Bar dengan warna yang lebih menyala */
        .progress {
            height: 16px; /* Lebih tebal agar warna terlihat */
            border-radius: 10px;
            background-color: #f1f5f9;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Warna Gradien untuk Progress Bar Monitoring */
        .progress-selesai {
            background: linear-gradient(90deg, #10b981, #34d399) !important;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
        }

        .progress-menunggu {
            background: linear-gradient(90deg, #f59e0b, #fbbf24) !important;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
        }

        /* ================= RESPONSIVE ADJUSTMENTS ================= */
        @media (max-width: 992px) {
            .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
            .sidebar h4, .sidebar hr { display: none; }
            .sidebar .nav-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
            .content { margin-left: 0; padding: 20px; }
        }

        @media (max-width: 576px) {
            .sidebar .nav-container { flex-direction: column; width: 100%; }
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4><i class="bi bi-shield-check"></i> Admin Web</h4>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
        
        <div class="nav-container">
            <a href="admin.php" class="active"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="data_laporan.php"><i class="bi bi-chat-left-text"></i> Data Laporan</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <div class="content">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h3 class="page-title mb-0">Dashboard Statistik</h3>
            <span class="badge bg-white text-dark p-2 px-3 rounded-pill shadow-sm border">
                <i class="bi bi-calendar3 me-2 text-primary"></i> <?= date('d M Y') ?>
            </span>
        </div>

        <div class="row g-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card card-total">
                    <div>
                        <div class="small opacity-75">Total Aspirasi</div>
                        <h3 class="value"><?= $total ?></h3>
                    </div>
                    <div class="icon-box"><i class="bi bi-collection"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card card-menunggu">
                    <div>
                        <div class="small opacity-75">Menunggu</div>
                        <h3 class="value"><?= $menunggu ?></h3>
                    </div>
                    <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card card-proses">
                    <div>
                        <div class="small opacity-75">Dalam Proses</div>
                        <h3 class="value"><?= $proses ?></h3>
                    </div>
                    <div class="icon-box"><i class="bi bi-arrow-repeat"></i></div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card card-selesai">
                    <div>
                        <div class="small opacity-75">Selesai</div>
                        <h3 class="value"><?= $selesai ?></h3>
                    </div>
                    <div class="icon-box"><i class="bi bi-check2-all"></i></div>
                </div>
            </div>
        </div>

        <div class="detail-box">
            <h5><i class="bi bi-bar-chart-line me-2"></i>Monitoring Progress Penanganan</h5>
            <p class="text-muted mb-4 small">Visualisasi persentase status laporan yang masuk ke sistem.</p>

            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold small text-secondary">LAPORAN SELESAI</span>
                    <span class="fw-bold text-success"><?= ($total > 0) ? round(($selesai/$total)*100) : 0 ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-selesai progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: <?= ($total > 0) ? ($selesai/$total)*100 : 0 ?>%"></div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold small text-secondary">LAPORAN MENUNGGU</span>
                    <span class="fw-bold text-warning"><?= ($total > 0) ? round(($menunggu/$total)*100) : 0 ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-menunggu" 
                         role="progressbar" 
                         style="width: <?= ($total > 0) ? ($menunggu/$total)*100 : 0 ?>%"></div>
                </div>
            </div>

            <div class="p-3 mt-4 border-start border-4 border-primary bg-light rounded-end">
                <div class="d-flex gap-3 align-items-center">
                    <i class="bi bi-patch-info-fill text-primary fs-4"></i>
                    <div>
                        <span class="d-block fw-medium">Sistem mendeteksi <strong><?= $menunggu ?> laporan baru</strong>.</span>
                        <small class="text-muted">Aksi cepat admin sangat diperlukan untuk kepuasan pelapor.</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>