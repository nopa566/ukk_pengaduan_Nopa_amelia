<?php
// Memulai session untuk validasi login
session_start();

// Menghubungkan ke file functions.php (pastikan file ini ada untuk koneksi ke database)
require 'functions.php';

// ================= CEK LOGIN =================
// Memastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Mengambil ID aspirasi dari parameter URL
if (!isset($_GET['id'])) {
    header("Location: data_laporan.php");
    exit;
}
$id = $_GET['id'];

// ================= QUERY DATA LAPORAN =================
// Mengambil data detail laporan, nama siswa, dan nama kategori menggunakan JOIN
$row = query("SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
              FROM aspirasi 
              JOIN siswa ON aspirasi.nis = siswa.nis 
              JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
              WHERE aspirasi.id_aspirasi = '$id'")[0];

// Validasi jika data dengan ID tersebut tidak ditemukan di database
if (!$row) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='data_laporan.php';</script>";
    exit;
}

// ================= PROSES TANGGAPAN =================
// Mengecek apakah tombol submit tanggapan ditekan
if (isset($_POST['submit_tanggapan'])) {
    // Mengamankan input teks dari karakter berbahaya (XSS Protection)
    $tanggapan = htmlspecialchars($_POST['tanggapan']);
    $status_baru = $_POST['status'];

    // Update field feedback dan status pada tabel aspirasi di database
    $query_update = "UPDATE aspirasi SET 
                        feedback = '$tanggapan', 
                        status = '$status_baru' 
                     WHERE id_aspirasi = '$id'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Tanggapan berhasil disimpan!');
                window.location='data_laporan.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menyimpan tanggapan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanggapi Laporan | Admin Panel</title>
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

        /* SIDEBAR STYLING - Sesuai dengan dashboard utama */
        .sidebar {
            width: 260px; height: 100vh; position: fixed;
            background-color: #4f46e5; padding: 24px; z-index: 100; color: white;
            transition: all 0.3s;
        }
        .sidebar h4 { font-weight: 700; font-size: 1.2rem; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .sidebar a {
            display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.8);
            padding: 12px 16px; border-radius: 12px; margin-bottom: 8px; text-decoration: none; font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.2); color: #ffffff; }

        /* CONTENT AREA */
        .content { margin-left: 260px; padding: 40px; transition: all 0.3s; }

        /* CARD BOX - Desain kotak putih dengan aksen border kiri warna dashboard */
        .card-box {
            background: white; border-radius: 16px; padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-left: 8px solid #4f46e5;
        }

        /* WARNA IDENTITAS DASHBOARD (#4f46e5) */
        .text-dashboard { color: #4f46e5 !important; }
        
        .btn-dashboard { 
            background-color: #4f46e5 !important; 
            border-color: #4f46e5 !important; 
            color: white !important; 
        }
        .btn-dashboard:hover { background-color: #3730a3 !important; }

        /* WARNA TOMBOL KEMBALI (Slate Grey yang serasi) */
        .btn-kembali {
            background-color: #64748b !important;
            border-color: #64748b !important;
            color: white !important;
        }
        .btn-kembali:hover { background-color: #475569 !important; }

        .info-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .info-value { font-weight: 500; color: #1e293b; margin-bottom: 20px; }

        /* Styling gambar bukti agar tidak pecah dan rapi */
        .img-preview {
            max-width: 450px; width: 100%; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 10px;
        }

        /* Styling Badge Status */
        .badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; }
        .badge.selesai { background-color: #d1fae5; color: #065f46; }
        .badge.proses { background-color: #e0f2fe; color: #075985; }
        .badge.menunggu { background-color: #fef3c7; color: #92400e; }

        /* RESPONSIVE: Penyesuaian untuk layar Smartphone */
        @media (max-width: 992px) {
            .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
            .sidebar h4, .sidebar hr { display: none; }
            .sidebar .nav-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
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
        <div class="mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square text-dashboard me-2"></i>Detail & Tanggapi</h3>
            <p class="text-muted small">Kelola status dan berikan umpan balik kepada siswa.</p>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card-box shadow-sm">
                    
                    <h5 class="fw-bold mb-4 text-dashboard"><i class="bi bi-file-earmark-text me-2"></i>Detail Pengaduan</h5>
                    
                    <div class="row border-bottom mb-4 pb-2">
                        <div class="col-md-3 col-6">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value text-dark fw-bold"><?= $row['nama']; ?></div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-label">Tanggal Masuk</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-label">Kategori</div>
                            <div class="info-value"><span class="badge bg-light text-dark border">#<?= $row['nama_kategori']; ?></span></div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-label">Status Saat Ini</div>
                            <div class="info-value">
                                <span class="badge <?= strtolower($row['status']) ?>">
                                    <?= $row['status']; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Keterangan Aspirasi</div>
                        <div class="info-value p-3 bg-light rounded-3 border-start border-4 border-primary shadow-sm">
                            "<?= $row['keterangan']; ?>"
                        </div>
                    </div>

                    <?php if($row['foto']): ?>
                    <div class="mb-4">
                        <div class="info-label">Lampiran Bukti</div>
                        <a href="assets/img/<?= $row['foto']; ?>" target="_blank">
                            <img src="assets/img/<?= $row['foto']; ?>" class="img-preview shadow-sm" alt="Bukti Laporan">
                        </a>
                        <p class="small text-muted mt-2 fst-italic">* Klik gambar untuk memperbesar</p>
                    </div>
                    <?php endif; ?>

                    <hr class="my-5" style="opacity: 0.1;">
                    
                    <h5 class="fw-bold mb-4 text-dashboard"><i class="bi bi-reply-all-fill me-2"></i>Kirim Tanggapan Admin</h5>
                    
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tentukan Status Baru:</label>
                                <select name="status" class="form-select shadow-sm" required>
                                    <option value="Menunggu" <?= ($row['status'] == 'Menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="Proses" <?= ($row['status'] == 'Proses') ? 'selected' : ''; ?>>Proses</option>
                                    <option value="Selesai" <?= ($row['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pesan Tanggapan / Feedback:</label>
                                <textarea name="tanggapan" class="form-control shadow-sm" rows="5" placeholder="Tuliskan tindakan atau jawaban untuk siswa..." required><?= $row['feedback']; ?></textarea>
                            </div>

                            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end border-top pt-4">
                                <a href="data_laporan.php" class="btn btn-kembali px-4 fw-bold shadow-sm">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" name="submit_tanggapan" class="btn btn-dashboard px-5 fw-bold shadow-sm">
                                    <i class="bi bi-save-fill me-2"></i>Simpan Tanggapan
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>