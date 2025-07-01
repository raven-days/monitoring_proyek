<?php
$host = "localhost";
$user = "root";       // default XAMPP
$pass = "";
$dbname = "monitoring_proyek";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>