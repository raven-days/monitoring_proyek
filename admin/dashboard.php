<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Statistik Ringkasan
$all     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM proyek"))['total'];
$ongoing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM proyek WHERE status='Berjalan'"))['total'];
$done    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM proyek WHERE status='Selesai'"))['total'];

// Filter
$where = [];
if (!empty($_GET['cari'])) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $where[] = "nama_proyek LIKE '%$cari%'";
}
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    $where[] = "status = '$status'";
}

$sqlFilter = '';
if (!empty($where)) {
    $sqlFilter = 'WHERE ' . implode(' AND ', $where);
}

$proyek = mysqli_query($conn, "SELECT * FROM proyek $sqlFilter ORDER BY tanggal_mulai DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h2>Dashboard Admin</h2>
    <p class="fs-5 fw-semibold">Halo, <?= $_SESSION['nama'] ?> 👋</p>

    <div class="row text-white mb-4">
        <div class="col-md-4">
            <div class="bg-primary p-3 rounded shadow">
                <h4>Total Proyek</h4>
                <p class="fs-3"><?= $all ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-info p-3 rounded shadow">
                <h4>Berjalan</h4>
                <p class="fs-3"><?= $ongoing ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-success p-3 rounded shadow">
                <h4>Selesai</h4>
                <p class="fs-3"><?= $done ?></p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <h5>Grafik Jumlah Proyek (Bar Chart)</h5>
            <canvas id="proyekChartBar" style="max-height: 300px;"></canvas>
        </div>
        <div class="col-md-6">
            <h5>Distribusi Proyek (Doughnut Chart)</h5>
            <canvas id="proyekChartDoughnut" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="cari" class="form-control" placeholder="Cari nama proyek..." value="<?= $_GET['cari'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">-- Semua Status --</option>
                <option value="Berjalan" <?= (isset($_GET['status']) && $_GET['status'] == 'Berjalan') ? 'selected' : '' ?>>Berjalan</option>
                <option value="Selesai" <?= (isset($_GET['status']) && $_GET['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped table-custom">
            <thead class="table-dark">
                <tr>
                    <th>Nama Proyek</th>
                    <th>Jenis</th>
                    <th>Lokasi</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Selesai</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Catatan</th>
                    <th>Lampiran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($proyek) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($proyek)): ?>
                        <tr>
                            <td><?= $row['nama_proyek'] ?></td>
                            <td><?= $row['jenis_proyek'] ?></td>
                            <td><?= $row['lokasi'] ?></td>
                            <td><?= $row['tanggal_mulai'] ?></td>
                            <td><?= $row['tanggal_selesai'] ?></td>
                            <td><span class="badge <?= $row['status'] == 'Berjalan' ? 'bg-info' : 'bg-success' ?>"><?= $row['status'] ?></span></td>
                            <td><?= $row['progress'] ?>%</td>
                            <td><?= htmlspecialchars($row['catatan']) ?></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <?php if ($row['lampiran']): ?>
                                        <a href="../uploads/<?= $row['lampiran'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Lampiran</a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                    <a href="riwayat_progres.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Lihat Progres</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const barCtx = document.getElementById('proyekChartBar').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Total', 'Berjalan', 'Selesai'],
            datasets: [{
                label: 'Jumlah Proyek',
                data: [<?= $all ?>, <?= $ongoing ?>, <?= $done ?>],
                backgroundColor: ['#007bff', '#17a2b8', '#28a745']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    const doughnutCtx = document.getElementById('proyekChartDoughnut').getContext('2d');
    new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Berjalan', 'Selesai'],
            datasets: [{
                data: [<?= $ongoing ?>, <?= $done ?>],
                backgroundColor: ['#17a2b8', '#28a745'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
