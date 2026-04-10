<?php
// Memulai session untuk menyimpan data login user
session_start();

// Menghubungkan ke file functions.php (koneksi database & function query)
require 'functions.php';

// ================= CEK LOGIN =================
// Mengecek apakah user sudah login dan memiliki role siswa
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    // Jika belum login, redirect ke halaman login
    header("Location: index.php");
    exit;
}

// Ambil data nama dari session untuk sapaan di header
$nama_user = $_SESSION['nama'];

// ================= AMBIL ID =================
// Mengambil ID laporan dari URL (contoh: edit_laporan.php?id=5)
$id = $_GET['id'] ?? 0;

// ================= AMBIL DATA LAPORAN =================
// Mengambil data laporan berdasarkan ID untuk ditampilkan di form
$result = query("SELECT * FROM aspirasi WHERE id_aspirasi='$id'");
$data = $result[0] ?? null;

// Jika data tidak ditemukan, balikkan ke riwayat
if (!$data) {
    header("Location: riwayat.php");
    exit;
}

// ================= UPDATE DATA =================
// Mengecek apakah tombol submit ditekan
if(isset($_POST['submit'])){
    // Mengambil data dari form dan mengamankan input
    $kategori = $_POST['id_kategori'];
    $isi = htmlspecialchars($_POST['keterangan']);
    $lokasi = htmlspecialchars($_POST['lokasi']);

    // ================= CEK FOTO BARU =================
    // Jika user upload foto baru
    if($_FILES['foto']['name'] != ''){
        $namaFile = $_FILES['foto']['name']; // nama file foto
        $tmp = $_FILES['foto']['tmp_name']; // lokasi sementara file

        // Memindahkan file ke folder assets/img
        move_uploaded_file($tmp, "assets/img/".$namaFile);

        // Update data termasuk foto baru
        mysqli_query($conn, "UPDATE aspirasi SET 
            id_kategori='$kategori',
            keterangan='$isi',
            lokasi='$lokasi',
            foto='$namaFile'
            WHERE id_aspirasi='$id'
        ");
    } else {
        // Jika tidak upload foto, hanya update kategori, lokasi & isi
        mysqli_query($conn, "UPDATE aspirasi SET 
            id_kategori='$kategori',
            lokasi='$lokasi',
            keterangan='$isi'
            WHERE id_aspirasi='$id'
        ");
    }

    // Setelah update, munculkan notif dan kembali ke halaman riwayat
    echo "<script>alert('Laporan berhasil diubah!'); window.location='riwayat.php';</script>";
    exit;
}

// ================= AMBIL DATA KATEGORI =================
// Untuk mengisi pilihan di dropdown kategori
$kategori_list = query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Laporan | Siswa Panel</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* CSS GLOBAL */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* SIDEBAR STYLING - Tetap menyamping di desktop */
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

        /* CONTENT AREA - Beri margin kiri agar tidak tertutup sidebar */
        .content { margin-left: 260px; padding: 40px; transition: all 0.3s; }

        /* CARD BOX - Kotak putih utama */
        .card-box {
            background: white; border-radius: 16px; padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-left: 8px solid #4f46e5;
            max-width: 800px;
        }

        /* LABEL & INPUT STYLING */
        label { font-weight: 600; font-size: 0.85rem; color: #64748b; margin-bottom: 8px; display: block; }
        .form-control, .form-select {
            border-radius: 10px; padding: 12px 15px; border: 1px solid #e2e8f0;
            background-color: #f8fafc; font-size: 0.95rem;
        }

        /* BUTTON KEMBALI - Warna Slate yang serasi */
        .btn-back {
            background-color: #64748b; color: white; border-radius: 10px;
            padding: 11px 20px; text-decoration: none; font-weight: 500;
            transition: 0.3s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-back:hover { background-color: #475569; color: white; }

        /* BUTTON SIMPAN - Warna Indigo khas Dashboard */
        .btn-submit {
            background-color: #4f46e5; border: none; padding: 12px 25px;
            border-radius: 10px; font-weight: 600; color: white; transition: 0.3s;
        }
        .btn-submit:hover { background-color: #4338ca; transform: translateY(-2px); }

        /* IMAGE PREVIEW */
        .img-preview {
            border-radius: 12px; max-width: 150px; border: 3px solid #f1f5f9;
            margin-bottom: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* RESPONSIVE LAYOUT - Sidebar pindah ke atas di layar kecil */
        @media (max-width: 992px) {
            .sidebar { width: 100%; height: auto; position: relative; padding: 15px; }
            .sidebar h4, .sidebar hr { display: none; }
            .sidebar .nav-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
            .content { margin-left: 0; padding: 20px; }
            .btn-group-custom { flex-direction: column-reverse; width: 100%; }
            .btn-group-custom a, .btn-group-custom button { width: 100%; text-align: center; justify-content: center; }
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

    <div class="mb-4">
        <h3 class="fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Laporan</h3>
        <p class="text-muted small">Anda sedang mengedit laporan dengan ID: #<?= $id; ?></p>
    </div>

    <div class="card-box">
        <div class="mb-4">
            <h5 class="fw-bold text-primary mb-1">Perbarui Laporan Anda</h5>
            <p class="text-muted small">Pastikan data yang diubah sudah benar sebelum disimpan.</p>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label>Pilih Kategori</label>
                    <select name="id_kategori" class="form-select shadow-sm" required>
                        <?php foreach($kategori_list as $k): ?>
                            <option value="<?= $k['id_kategori']; ?>" 
                                <?= ($k['id_kategori'] == $data['id_kategori']) ? 'selected' : ''; ?>>
                                <?= $k['nama_kategori']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Lokasi Kejadian</label>
                    <input type="text" name="lokasi" class="form-control shadow-sm" 
                           value="<?= $data['lokasi']; ?>" required>
                </div>

                <div class="col-12 mb-3">
                    <label>Detail Laporan</label>
                    <textarea name="keterangan" class="form-control shadow-sm" rows="5" required><?= $data['keterangan']; ?></textarea>
                </div>

                <div class="col-12 mb-4">
                    <label>Bukti Foto</label>
                    <?php if(!empty($data['foto'])): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Foto saat ini:</small>
                            <img src="assets/img/<?= $data['foto']; ?>" class="img-preview" alt="Foto Laporan">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control shadow-sm">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto.</div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end border-top pt-4 btn-group-custom">
                    <a href="riwayat.php" class="btn-back shadow-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="submit" class="btn-submit shadow">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>