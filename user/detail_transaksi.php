<?php
session_start();
include '../config/koneksi.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];
$id_pengguna = $_SESSION['id_pengguna'];

// Ambil data transaksi
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, h.nama_villa, h.lokasi, p.nama_pengguna 
    FROM transaksi t
    JOIN detail_villa h ON t.id_villa = h.id_villa
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna
    WHERE t.id_transaksi='$id' AND t.id_pengguna='$id_pengguna'
"));

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        padding: 20px;
    }

    .container {
        width: 450px;
        margin: auto;
        padding: 20px;
        border: 2px solid #5F9EA0;
        border-radius: 10px;
    }

    .title {
        text-align: center;
        color: #5F9EA0;
        font-size: 1.3rem;
    }

    .table {
        width: 100%;
        margin-top: 15px;
    }

    .table td {
        padding: 5px;
    }

    .print-btn {
        display: <?=($data['status']=='lunas') ? 'block': 'none'?>;
        width: 100%;
        text-align: center;
        background: #5F9EA0;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        font-weight: bold;
        margin-top: 20px;
    }

    .back {
        display: block;
        text-align: center;
        margin-top: 10px;
        color: #5F9EA0;
        text-decoration: none;
        font-weight: bold;
    }

    /* Mode cetak */
    @media print {

        .print-btn,
        .back {
            display: none;
        }

        .container {
            border: none;
        }
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="title">DAF Villa - Bukti Booking</div>

        <table class="table">
            <tr>
                <td>ID Transaksi</td>
                <td>: <?= $data['id_transaksi'] ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <?= $data['nama_pengguna'] ?></td>
            </tr>
            <tr>
                <td>Villa</td>
                <td>: <?= $data['nama_villa'] ?></td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>: <?= $data['lokasi'] ?></td>
            </tr>
            <tr>
                <td>Check-In</td>
                <td>: <?= $data['tanggal_checkin'] ?></td>
            </tr>
            <tr>
                <td>Check-Out</td>
                <td>: <?= $data['tanggal_checkout'] ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td>: Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <?= ucfirst($data['status']) ?></td>
            </tr>
        </table>

        <a href="#" class="print-btn" onclick="window.print()">🖨 Cetak Bukti</a>
        <a href="dashboard.php" class="back">← Kembali</a>
    </div>

</body>

</html>