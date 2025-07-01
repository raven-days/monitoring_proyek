<?php
session_start();
include '../config/db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['id']   = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        // Arahkan ke halaman sesuai role
        if ($data['role'] === 'admin') {
            header('Location: ../admin/dashboard.php');
        } else if ($data['role'] === 'teknisi') {
            header('Location: ../teknisi/dashboard.php');
        } else {
            $error = "Role tidak dikenali.";
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Monitoring Proyek</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body, html {
        height: 100%;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    }
</style>

</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Login Monitoring Proyek</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>
        <button name="login" class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>

</html>
