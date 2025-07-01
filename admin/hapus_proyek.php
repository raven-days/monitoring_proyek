<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>alert('ID proyek tidak ditemukan!'); location.href='data_proyek.php';</script>";
    exit;
}

include '../config/db.php';

$id = $_GET['id'];

// Hapus dari tabel proyek
$hapus = mysqli_query($conn, "DELETE FROM proyek WHERE id = $id");

if ($hapus) {
    echo "<script>alert('Proyek berhasil dihapus'); location.href='data_proyek.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus proyek'); location.href='data_proyek.php';</script>";
}
