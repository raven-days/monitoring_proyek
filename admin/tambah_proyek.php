<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

$teknisi = mysqli_query($conn, "SELECT * FROM users WHERE role='teknisi'");
$allowed_jenis = ['FTTH', 'VSAT', 'Tower', 'Server'];
$success = $error = "";

// Simpan proyek baru
if (isset($_POST['simpan'])) {
    $nama_proyek     = $_POST['nama_proyek'];
    $jenis_proyek    = $_POST['jenis_proyek'];
    $lokasi          = $_POST['lokasi'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $teknisi_id      = $_POST['teknisi_id'];

    // Validasi jenis proyek
    if (!in_array($jenis_proyek, $allowed_jenis)) {
        $error = "Jenis proyek tidak valid!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO proyek 
            (nama_proyek, jenis_proyek, lokasi, tanggal_mulai, tanggal_selesai, teknisi_id) 
            VALUES (
                '$nama_proyek', 
                '$jenis_proyek', 
                '$lokasi', 
                '$tanggal_mulai', 
                '$tanggal_selesai', 
                $teknisi_id
            )");

        if ($insert) {
            $success = "Proyek berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan proyek.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Proyek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Monitoring Proyek</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav gap-3">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="data_proyek.php">Data Proyek</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tambah_user.php">Tambah User</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger fw-semibold" href="../auth/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Form Tambah -->
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Proyek Baru</h5>
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Proyek</label>
                    <input type="text" name="nama_proyek" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Proyek</label>
                    <select name="jenis_proyek" class="form-select" required>
                        <option value="">-- Pilih Jenis Proyek --</option>
                        <option value="FTTH">FTTH</option>
                        <option value="VSAT">VSAT</option>
                        <option value="Tower">Tower</option>
                        <option value="Server">Server</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Teknisi</label>
                    <select name="teknisi_id" class="form-select" required>
                        <option value="">-- Pilih Teknisi --</option>
                        <?php while ($t = mysqli_fetch_assoc($teknisi)) : ?>
                            <option value="<?= $t['id'] ?>"><?= $t['nama'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">💾 Simpan Proyek</button>
                <a href="dashboard.php" class="btn btn-secondary">↩️ Kembali</a>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
