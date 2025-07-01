<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Ambil data proyek dan nama teknisinya
$query = "SELECT proyek.*, users.nama AS teknisi_nama 
          FROM proyek 
          LEFT JOIN users ON proyek.teknisi_id = users.id";
$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Proyek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
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

<div class="container mt-4">
    <h2>Data Proyek</h2>
    <a href="tambah_proyek.php" class="btn btn-primary mb-3">+ Tambah Proyek</a>
    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Proyek</th>
                <th>Jenis</th>
                <th>Lokasi</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Status</th>
                <th>Teknisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_proyek'] ?></td>
                <td><?= $row['jenis_proyek'] ?></td>
                <td><?= $row['lokasi'] ?></td>
                <td><?= $row['tanggal_mulai'] ?></td>
                <td><?= $row['tanggal_selesai'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['teknisi_nama'] ?></td>
                <td>
    <a href="edit_proyek.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning">✏️</a>
    <a href="hapus_proyek.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin mau hapus proyek ini?')">🗑️</a>
</td>

            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
