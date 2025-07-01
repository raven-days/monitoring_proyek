<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID proyek tidak ditemukan!'); location.href='dashboard.php';</script>";
    exit;
}

$id = (int) $_GET['id'];
$proyek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM proyek WHERE id = $id"));

if (!$proyek) {
    echo "<script>alert('Proyek tidak ditemukan!'); location.href='dashboard.php';</script>";
    exit;
}

if ($proyek['teknisi_id'] != $_SESSION['id']) {
    echo "<script>alert('Kamu tidak memiliki akses ke proyek ini!'); location.href='dashboard.php';</script>";
    exit;
}

if (isset($_POST['simpan'])) {
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    $progress = (int) $_POST['progress'];
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan']);
    $lampiran = '';

    if (!empty($_FILES['lampiran']['name'])) {
        $filename = time() . '_' . basename($_FILES['lampiran']['name']);
        $uploadPath = '../uploads/' . $filename;
        move_uploaded_file($_FILES['lampiran']['tmp_name'], $uploadPath);
        $lampiran = $filename;
    }

    // Simpan ke riwayat progress_proyek
    mysqli_query($conn, "INSERT INTO progress_proyek (proyek_id, teknisi_id, status, progress, catatan, lampiran, tanggal_update) 
                         VALUES ($id, {$_SESSION['id']}, '$status', $progress, '$catatan', '$lampiran', NOW())");

    // Update data utama di tabel proyek
    $updateFields = "status = '$status', progress = $progress";
    if ($catatan !== '') $updateFields .= ", catatan = '$catatan'";
    if ($lampiran !== '') $updateFields .= ", lampiran = '$lampiran'";
    mysqli_query($conn, "UPDATE proyek SET $updateFields WHERE id = $id");

    echo "<script>alert('Progress proyek berhasil diperbarui!'); location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Progress Proyek</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Update Progress: <strong><?= htmlspecialchars($proyek['nama_proyek']) ?></strong></h3>

    <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm mt-4">
        <div class="mb-3">
            <label>Status Proyek</label>
            <select name="status" class="form-select" required>
                <option value="Berjalan" <?= $proyek['status'] == 'Berjalan' ? 'selected' : '' ?>>Berjalan</option>
                <option value="Selesai" <?= $proyek['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Progress (%)</label>
            <input type="number" name="progress" class="form-control" min="0" max="100" required value="<?= $proyek['progress'] ?>">
        </div>

        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label>Lampiran (Opsional)</label>
            <input type="file" name="lampiran" class="form-control">
        </div>

        <button name="simpan" class="btn btn-primary">Simpan</button>
        <a href="dashboard.php" class="btn btn-secondary ms-2">Kembali</a>
    </form>

    <hr>
    <h5 class="mt-5">Riwayat Progress</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Catatan</th>
                <th>Lampiran</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $riwayat = mysqli_query($conn, "SELECT * FROM progress_proyek WHERE proyek_id = $id ORDER BY tanggal_update DESC");
        while ($log = mysqli_fetch_assoc($riwayat)):
        ?>
            <tr>
                <td><?= $log['tanggal_update'] ?></td>
                <td><?= $log['status'] ?></td>
                <td><?= $log['progress'] ?>%</td>
                <td><?= htmlspecialchars($log['catatan']) ?></td>
                <td>
                    <?php if ($log['lampiran']): ?>
                        <a href="../uploads/<?= $log['lampiran'] ?>" target="_blank">Lihat</a>
                    <?php else: ?>
                        <em class="text-muted">-</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
