<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan'); location.href='dashboard.php';</script>";
    exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proyek WHERE id = $id"));
$teknisi = mysqli_query($conn, "SELECT * FROM users WHERE role='teknisi'");

if (isset($_POST['update'])) {
    $nama_proyek     = $_POST['nama_proyek'];
    $jenis_proyek    = $_POST['jenis_proyek'];
    $lokasi          = $_POST['lokasi'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $teknisi_id      = $_POST['teknisi_id'];

    $update = mysqli_query($conn, "UPDATE proyek SET 
        nama_proyek='$nama_proyek',
        jenis_proyek='$jenis_proyek',
        lokasi='$lokasi',
        tanggal_mulai='$tanggal_mulai',
        tanggal_selesai='$tanggal_selesai',
        teknisi_id='$teknisi_id'
        WHERE id=$id");

    if ($update) {
        echo "<script>alert('Proyek berhasil diupdate'); location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal update');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Proyek</title>
    <meta charset="UTF-8">
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

<!-- Form Edit -->
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Proyek</h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Proyek</label>
                    <input type="text" name="nama_proyek" value="<?= $data['nama_proyek'] ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Proyek</label>
                    <input type="text" name="jenis_proyek" value="<?= $data['jenis_proyek'] ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" value="<?= $data['lokasi'] ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= $data['tanggal_mulai'] ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= $data['tanggal_selesai'] ?>" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Teknisi</label>
                    <select name="teknisi_id" class="form-select" required>
                        <?php while ($t = mysqli_fetch_assoc($teknisi)) : ?>
                            <option value="<?= $t['id'] ?>" <?= $data['teknisi_id'] == $t['id'] ? 'selected' : '' ?>>
                                <?= $t['nama'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="update" class="btn btn-success">💾 Update Proyek</button>
                <a href="dashboard.php" class="btn btn-secondary">↩️ Kembali</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
