<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teknisi') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$teknisi_id = $_SESSION['id'];
$query = "SELECT * FROM proyek WHERE teknisi_id = $teknisi_id ORDER BY tanggal_mulai DESC";
$data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Teknisi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
          <a class="nav-link active fw-semibold" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../auth/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten -->
<div class="container mt-4">
    <h2 class="fw-bold mb-1">Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👷‍♂️</h2>
    <p class="text-muted mb-4">Berikut adalah proyek yang sedang kamu kerjakan:</p>

    <?php if (mysqli_num_rows($data) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white shadow-sm">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Proyek</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Selesai</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_proyek']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_proyek']) ?></td>
                        <td><?= htmlspecialchars($row['lokasi']) ?></td>
                        <td><?= $row['tanggal_mulai'] ?></td>
                        <td><?= $row['tanggal_selesai'] ?></td>
                        <td class="text-center">
                            <span class="badge <?= $row['status'] == 'Berjalan' ? 'bg-info' : 'bg-success' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="update_proyek.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                ✏️ Update
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada proyek yang ditugaskan kepadamu saat ini.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
