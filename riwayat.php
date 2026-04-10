<?php
// Memulai session
session_start();

// Menghubungkan ke functions.php (koneksi database & helper query)
require 'functions.php';

// ================= CEK LOGIN =================
// Jika belum login atau bukan siswa, arahkan ke halaman login
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

// Ambil NIS (identitas siswa) dari session
$nis = $_SESSION['id'];

// ================= FITUR HAPUS TERPILIH (MULTI DELETE) =================
if(isset($_POST['hapus_terpilih'])){
    // Cek apakah ada checkbox yang dicentang
    if(!empty($_POST['pilih'])){
        foreach($_POST['pilih'] as $id){
            // Hapus data berdasarkan ID dari tabel aspirasi
            mysqli_query($conn, "DELETE FROM aspirasi WHERE id_aspirasi='$id'");
        }
    }
    // Refresh halaman agar data terbaru muncul
    header("Location: riwayat.php");
    exit;
}

// ================= FITUR BATALKAN LAPORAN =================
if(isset($_GET['batal'])){
    $id = $_GET['batal'];
    // Update status menjadi Dibatalkan
    mysqli_query($conn, "UPDATE aspirasi SET status='Dibatalkan' WHERE id_aspirasi='$id'");
    header("Location: riwayat.php");
    exit;
}

// ================= AMBIL DATA RIWAYAT =================
// Query JOIN untuk mengambil data aspirasi dan nama kategorinya
$riwayat = query("SELECT aspirasi.*, kategori.nama_kategori 
FROM aspirasi 
JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
WHERE nis='$nis' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Laporan | Siswa Panel</title>
    
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

        /* CARD BOX DENGAN WARNA DI SAMPING KIRI */
        .card-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            /* AKSEN WARNA DI SAMPING KIRI */
            border-left: 8px solid #4f46e5; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-top: none;
            border-right: none;
            border-bottom: none;
        }

        /* TOMBOL HAPUS */
        .btn-hapus-multi {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .btn-hapus-multi:hover {
            background-color: #dc2626;
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* TABEL STYLING */
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            font-size: 0.9rem;
        }

        /* BADGE STATUS */
        .badge-status {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .bg-selesai { background: #dcfce7; color: #16a34a; }
        .bg-proses { background: #fef3c7; color: #d97706; }
        .bg-menunggu { background: #fee2e2; color: #dc2626; }
        .bg-batal { background: #f1f5f9; color: #64748b; }

        .detail-row {
            background-color: #f8fafc;
        }

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
        <a href="siswa.php"><i class="bi bi-pencil-square"></i> Tulis Laporan</a>
        <a href="riwayat.php" class="active"><i class="bi bi-clock-history"></i> Riwayat</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Laporan</h3>
    </div>

    <div class="card-box">
        <form method="post">
            
            <div class="mb-3">
                <button type="submit" name="hapus_terpilih" class="btn btn-hapus-multi" onclick="return confirm('Hapus laporan yang dipilih?')">
                    <i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" class="form-check-input" onclick="toggleAll(this)"></th>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($riwayat)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat laporan ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $i=1; foreach($riwayat as $r): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="pilih[]" value="<?= $r['id_aspirasi']; ?>" class="form-check-input">
                                </td>
                                
                                <td><?= $i++; ?></td>
                                <td class="fw-medium"><?= date('d M Y', strtotime($r['tanggal'])); ?></td>
                                
                                <td><span class="text-secondary"><?= $r['nama_kategori']; ?></span></td>
                                
                                <td>
                                    <?php 
                                        $s = $r['status'];
                                        $class = ($s == 'Selesai') ? 'bg-selesai' : (($s == 'Proses') ? 'bg-proses' : (($s == 'Dibatalkan') ? 'bg-batal' : 'bg-menunggu'));
                                    ?>
                                    <span class="badge-status <?= $class; ?>"><?= $s; ?></span>
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit_laporan.php?id=<?= $r['id_aspirasi']; ?>" class="btn btn-sm btn-light text-primary border" title="Edit Laporan">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <?php if($r['status'] == 'Menunggu'): ?>
                                        <a href="riwayat.php?batal=<?= $r['id_aspirasi']; ?>" class="btn btn-sm btn-light text-warning border" title="Batalkan Laporan" onclick="return confirm('Apakah Anda yakin ingin membatalkan laporan ini?')">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <tr class="detail-row">
                                <td colspan="1"></td>
                                <td colspan="5">
                                    <div class="py-2">
                                        <div class="mb-2">
                                            <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Isi Laporan:</small>
                                            <span class="text-dark small"><?= $r['keterangan']; ?></span>
                                        </div>
                                        <div class="p-2 rounded-3 border-start border-4 border-primary bg-white shadow-sm">
                                            <small class="text-primary d-block fw-bold" style="font-size: 0.7rem;">Tanggapan Admin:</small>
                                            <span class="small"><?= $r['feedback'] ?: '<i class="text-muted">Laporan Anda sedang menunggu tanggapan admin.</i>'; ?></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAll(source) {
    let checkboxes = document.getElementsByName('pilih[]');
    for(let i=0; i<checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>