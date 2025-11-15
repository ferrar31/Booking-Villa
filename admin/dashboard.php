<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login_admin.php');
  exit;
}

// Ambil data statistik
$total_villa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM detail_villa"))['total'];
$total_pengguna = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pengguna"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi"))['total'];

$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status='pending'"))['total'];
$lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status='lunas'"))['total'];
$batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status='batal'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - DAF Villa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f5f7fa;
      color: #333;
      display: flex;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(135deg, #5F9EA0, #5F9EA0);
      color: white;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 1.2rem;
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      margin: 10px 0;
      border-radius: 6px;
      transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #c3e2d8ff;
    }

    .sidebar .logout {
      background: rgba(255, 255, 255, 0.1);
      margin-top: 50px;
    }

    .sidebar .logout:hover {
      background: #c3e2d8ff;
    }

    .main-content {
      margin-left: 250px;
      width: calc(100% - 250px);
      padding: 20px;
    }

    header {
      background: white;
      color: #5F9EA0;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    header h1 {
      font-size: 1.5rem;
      color: #5F9EA0;
    }

    header p {
      font-size: 0.9rem;
      color: #666;
    }

    .container {
      max-width: 100%;
      margin: 0 auto;
      padding: 0;
    }

    h2 {
      color: #5F9EA0;
      text-align: center;
      margin-bottom: 30px;
    }

    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
      transition: 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h3 {
      color: #5F9EA0;
      margin-bottom: 10px;
    }

    .card .num {
      font-size: 2rem;
      font-weight: bold;
      color: #333;
    }

    .transaksi-status {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .status-box {
      background: linear-gradient(135deg, #5F9EA0, #c3e2d8ff);
      border: 1px solid #5F9EA0;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .status-box h4 {
      margin-bottom: 8px;
      color: #333;
    }

    .pending {
      color: #ff9800;
    }

    .lunas {
      color: #4caf50;
    }

    .batal {
      color: #f44336;
    }

    .menu {
      text-align: center;
      margin-top: 40px;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      background: #5F9EA0;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn:hover {
      background: #c3e2d8ff;
    }

    footer {
      text-align: center;
      color: #5F9EA0;
      padding: 20px;
      margin-top: 50px;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }

      .main-content {
        margin-left: 200px;
        width: calc(100% - 200px);
      }
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <h2>Admin Villa</h2>
    <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Dashboard</a>
    <a href="kelola_villa.php"><i class="bi bi-houses"></i> Kelola Villa</a>
    <a href="kelola_transaksi.php"><i class="bi bi-cash-coin"></i> Kelola Transaksi</a>
    <a href="kelola_pengguna.php"><i class="bi bi-person-gear"></i> Kelola Pengguna</a>
    <a href="../logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>

  <div class="main-content">
    <header>
      <h1>Dashboard Admin</h1>
      <p>Selamat datang, <?= $_SESSION['nama_admin'] ?> 👋</p>
    </header>

    <div class="container">
      <h2>Statistik Sistem</h2>

      <div class="stats">
        <div class="card">
          <h3>Total Villa</h3>
          <div class="num"><?= $total_villa ?></div>
        </div>
        <div class="card">
          <h3>Total Pengguna</h3>
          <div class="num"><?= $total_pengguna ?></div>
        </div>
        <div class="card">
          <h3>Total Transaksi</h3>
          <div class="num"><?= $total_transaksi ?></div>
        </div>
      </div>

      <div class="transaksi-status">
        <div class="status-box">
          <h4 class="pending">Pending</h4>
          <div class="num"><?= $pending ?></div>
        </div>
        <div class="status-box">
          <h4 class="lunas">Lunas</h4>
          <div class="num"><?= $lunas ?></div>
        </div>
        <div class="status-box">
          <h4 class="batal">Batal</h4>
          <div class="num"><?= $batal ?></div>
        </div>
      </div>

    </div>

    <footer>
      &copy; <?= date('Y') ?> DAF Villa. All rights reserved.
    </footer>
  </div>

</body>

</html>