<?php
// Memulai session untuk menyimpan data login
session_start();

// Menghubungkan ke file functions.php (koneksi database & function query)
require 'functions.php';

// ================= CEK LOGIN =================
// Mengecek apakah user sudah login dan role = admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    // Jika tidak, redirect ke halaman login
    header("Location: index.php");
    exit;
}

// ================= HAPUS MULTI =================
// Mengecek apakah tombol hapus terpilih ditekan
if(isset($_POST['hapus_terpilih'])){
    // Mengecek apakah ada checkbox yang dipilih
    if(!empty($_POST['pilih'])){
        // Loop setiap id yang dipilih
        foreach($_POST['pilih'] as $id){
            // Menghapus data berdasarkan id
            mysqli_query($conn, "DELETE FROM aspirasi WHERE id_aspirasi='$id'");
        }
        echo "<script>alert('Data terpilih berhasil dihapus!'); window.location='data_laporan.php';</script>";
        exit;
    }
}

// ================= FILTER =================
// Default filter (tidak menampilkan yang dibatalkan)
$where = "WHERE aspirasi.status != 'Dibatalkan'";

// Filter berdasarkan kategori
if(isset($_GET['kategori']) && $_GET['kategori'] != ''){
    $kategori = $_GET['kategori'];
    $where .= " AND aspirasi.id_kategori='$kategori'";
}

// Filter berdasarkan status
if(isset($_GET['status']) && $_GET['status'] != ''){
    $status = $_GET['status'];
    $where .= " AND aspirasi.status='$status'";
}

// Filter berdasarkan tanggal (range)
if(isset($_GET['tgl_awal']) && $_GET['tgl_awal'] != '' && isset($_GET['tgl_akhir']) && $_GET['tgl_akhir'] != ''){
    $awal = $_GET['tgl_awal'];
    $akhir = $_GET['tgl_akhir'];
    // Mengambil data antara tanggal awal dan akhir
    $where .= " AND aspirasi.tanggal BETWEEN '$awal' AND '$akhir'";
}

// ================= QUERY DATA =================
// Mengambil data laporan + join ke tabel siswa & kategori
$laporan = query("SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
FROM aspirasi 
JOIN siswa ON aspirasi.nis = siswa.nis 
JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
$where
ORDER BY aspirasi.tanggal DESC");

// Mengambil semua data kategori (untuk dropdown filter)
$kategori_list = query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Laporan | Admin Panel</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
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

        /* BOX UTAMA */
        .card-box {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-left: 8px solid #4f46e5;
            border-top: none;
            border-right: none;
            border-bottom: none;
        }

        /* TABLE STYLING */
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody td {
            padding: 15px;
            color: #334155;
            font-size: 0.9rem;
        }

        /* BADGE STATUS */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
        }
        .badge.selesai { background-color: #d1fae5; color: #065f46; }
        .badge.proses { background-color: #e0f2fe; color: #075985; }
        .badge.menunggu { background-color: #fef3c7; color: #92400e; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
            .sidebar h4, .sidebar hr { display: none; }
            .sidebar .nav-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
            .content { margin-left: 0; padding: 20px; }
        }

        @media print {
            .sidebar, .no-print, .btn-danger, .filter-row, .aksi-col, .check-col { display: none !important; }
            .content { margin-left: 0; padding: 0; }
            .card-box { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>

<body>

    <div class="sidebar no-print">
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
            <h3 class="fw-bold"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Data Laporan Pengaduan</h3>
            <button type="button" onclick="window.print()" class="btn btn-success btn-sm px-3 shadow-sm no-print">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>
        </div>

        <div class="card-box">
            <form method="GET" class="row g-2 mb-4 align-items-center no-print filter-row">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm shadow-sm">
                        <option value="">Semua Kategori</option>
                        <?php foreach($kategori_list as $k): ?>
                        <option value="<?= $k['id_kategori']; ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']) ? 'selected' : ''; ?>><?= $k['nama_kategori']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" <?= (isset($_GET['status']) && $_GET['status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="Proses" <?= (isset($_GET['status']) && $_GET['status'] == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                        <option value="Selesai" <?= (isset($_GET['status']) && $_GET['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Mulai Tanggal</label>
                    <input type="date" name="tgl_awal" class="form-control form-control-sm shadow-sm" value="<?= $_GET['tgl_awal'] ?? ''; ?>">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" class="form-control form-control-sm shadow-sm" value="<?= $_GET['tgl_akhir'] ?? ''; ?>">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2" style="height: 55px;">
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="data_laporan.php" class="btn btn-light border btn-sm px-3 shadow-sm">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>

            <hr class="no-print mb-4" style="opacity: 0.1;">

            <form method="post">
                <button type="submit" name="hapus_terpilih" class="btn btn-danger btn-sm mb-3 no-print shadow-sm px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')">
                    <i class="bi bi-trash me-1"></i> Hapus Terpilih
                </button>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="no-print check-col" style="width: 40px;">
                                    <input type="checkbox" class="form-check-input shadow-none" onclick="toggleAll(this)">
                                </th>
                                <th style="width: 50px;">No</th>
                                <th>Tanggal</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Isi Pengaduan</th>
                                <th class="text-center">Bukti</th>
                                <th class="text-center">Status</th>
                                <th class="no-print text-center aksi-col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if(empty($laporan)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php $i=1; foreach($laporan as $row): ?>
                            <tr>
                                <td class="no-print check-col">
                                    <input type="checkbox" name="pilih[]" value="<?= $row['id_aspirasi']; ?>" class="form-check-input shadow-none">
                                </td>
                                <td class="fw-bold"><?= $i++; ?></td>
                                <td class="text-nowrap"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td class="fw-medium"><?= $row['nama']; ?></td>
                                <td><span class="text-muted small">#</span><?= $row['nama_kategori']; ?></td>
                                <td style="max-width: 250px;" class="text-truncate"><?= $row['keterangan']; ?></td>
                                <td class="text-center">
                                    <?php if($row['foto']): ?>
                                    <a href="assets/img/<?= $row['foto']; ?>" target="_blank" class="btn btn-light btn-sm rounded-circle border">
                                        <i class="bi bi-image text-primary"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= strtolower($row['status']) ?>">
                                        <?= $row['status']; ?>
                                    </span>
                                </td>
                                <td class="no-print text-center aksi-col">
                                    <a href="tanggapi.php?id=<?= $row['id_aspirasi']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <script>
    function toggleAll(source){
        let checkboxes = document.getElementsByName('pilih[]');
        for(let i=0; i<checkboxes.length; i++){
            checkboxes[i].checked = source.checked;
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>