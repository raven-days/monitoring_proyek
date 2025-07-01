<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID proyek tidak ditemukan!'); location.href='dashboard.php';</script>";
    exit;
}

$proyek_id = (int) $_GET['id'];
$proyek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proyek WHERE id = $proyek_id"));
if (!$proyek) {
    echo "<script>alert('Proyek tidak ditemukan!'); location.href='dashboard.php';</script>";
    exit;
}

$riwayat = mysqli_query($conn, "
    SELECT p.*, u.nama AS nama_teknisi 
    FROM progress_proyek p 
    LEFT JOIN users u ON p.teknisi_id = u.id 
    WHERE proyek_id = $proyek_id 
    ORDER BY tanggal_update DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Progress - <?= htmlspecialchars($proyek['nama_proyek']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional custom style -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1000px;
        }
        .card {
            border-radius: 10px;
        }
        th, td {
            vertical-align: middle !important;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">Monitoring Proyek</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav gap-3">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="data_proyek.php">Data Proyek</a></li>
        <li class="nav-item"><a class="nav-link text-danger fw-semibold" href="../auth/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <div class="card shadow-sm p-4 mb-4">
        <h4 class="mb-3">Riwayat Progress Proyek: <strong><?= htmlspecialchars($proyek['nama_proyek']) ?></strong></h4>
        <a href="dashboard.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke Dashboard</a>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Catatan</th>
                        <th>Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($riwayat) > 0): ?>
                        <?php while ($log = mysqli_fetch_assoc($riwayat)): ?>
                            <tr>
                                <td class="text-center"><?= $log['tanggal_update'] ?></td>
                                <td><?= htmlspecialchars($log['nama_teknisi']) ?></td>
                                <td class="text-center"><?= $log['status'] ?></td>
                                <td class="text-center"><?= $log['progress'] ?>%</td>
                                <td><?= nl2br(htmlspecialchars($log['catatan'])) ?></td>
                                <td class="text-center">
                                    <?php if ($log['lampiran']): ?>
                                        <a href="../uploads/<?= $log['lampiran'] ?>" class="btn btn-sm btn-primary" target="_blank">Lihat</a>
                                    <?php else: ?>
                                        <em class="text-muted">-</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Belum ada riwayat progress</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
