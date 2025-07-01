<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Tambah user baru
if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $query = "INSERT INTO users (nama, username, password, role) 
              VALUES ('$nama', '$username', '$password', '$role')";

    if (mysqli_query($conn, $query)) {
        $success = "User berhasil ditambahkan.";
    } else {
        $error = "Gagal menambahkan user.";
    }
}

// Hapus user
if (isset($_GET['hapus'])) {
    $hapus_id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $hapus_id");
    header("Location: tambah_user.php");
    exit;
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
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
          <a class="nav-link" href="tambah_proyek.php">Tambah Proyek</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tambah_user.php">Tambah User</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger fw-semibold" href="../auth/login.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>    

<div class="container mt-5">
    <h3 class="mb-4">Tambah User</h3>

    <!-- Notifikasi -->
    <?php if (isset($success)) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Form Tambah -->
    <form method="post">
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="teknisi">Teknisi</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button name="simpan" class="btn btn-primary w-100">Simpan</button>
        <a href="dashboard.php" class="btn btn-secondary mt-2 w-100">Kembali</a>
    </form>

    <!-- Daftar User -->
    <h5 class="mt-5">Daftar User</h5>
    <table class="table table-bordered table-striped bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($user = mysqli_fetch_assoc($users)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $user['nama'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= ucfirst($user['role']) ?></td>
                <td>
                    <a href="?hapus=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
