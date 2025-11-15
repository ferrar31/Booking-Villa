<?php
session_start();
include '../config/koneksi.php';

// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Pastikan ada ID transaksi
if (!isset($_GET['id'])) {
    header('Location: kelola_transaksi.php');
    exit;
}

$id = $_GET['id'];

// Ambil detail transaksi
$query = mysqli_query($conn, "
    SELECT t.*, p.nama_pengguna, p.email, p.no_telp,
           h.nama_villa, h.lokasi
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna
    JOIN detail_villa h ON t.id_villa = h.id_villa
    WHERE t.id_transaksi='$id'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data transaksi tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        padding: 20px;
    }

    .struk {
        width: 500px;
        margin: auto;
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    h2,
    h3 {
        text-align: center;
        color: #2e7d32;
    }

    table {
        width: 100%;
        margin-top: 10px;
    }

    td {
        padding: 5px 0;
        font-size: 14px;
    }

    .print-btn,
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 18px;
        font-weight: bold;
        border-radius: 6px;
        text-decoration: none;
    }

    .print-btn {
        background: #2e7d32;
        color: white;
    }

    .back-btn {
        background: #c62828;
        color: white;
        margin-left: 10px;
    }

    @media print {

        .print-btn,
        .back-btn {
            display: none;
        }

        body {
            background: white;
        }
    }
    </style>
</head>

<body>

    <div class="struk">
        <h2>DAF Villa</h2>
        <center><small>Alamat: Gununghalu<br>
                Telp: 085720958285 | Email: arjuna@gmail.com</small></center>
        <hr>

        <table>
            <tr>
                <td><strong>ID Transaksi</strong></td>
                <td>: <?= $data['id_transaksi'] ?></td>
            </tr>
            <tr>
                <td><strong>Nama Pengguna</strong></td>
                <td>: <?= $data['nama_pengguna'] ?></td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>: <?= $data['email'] ?></td>
            </tr>
            <tr>
                <td><strong>No HP</strong></td>
                <td>: <?= $data['no_telp'] ?: '-' ?></td>
            </tr>
            <tr>
                <td><strong>Villa</strong></td>
                <td>: <?= $data['nama_villa'] ?></td>
            </tr>
            <tr>
                <td><strong>Lokasi</strong></td>
                <td>: <?= $data['lokasi'] ?></td>
            </tr>
            <tr>
                <td><strong>Check-in</strong></td>
                <td>: <?= $data['tanggal_checkin'] ?></td>
            </tr>
            <tr>
                <td><strong>Check-out</strong></td>
                <td>: <?= $data['tanggal_checkout'] ?></td>
            </tr>
            <tr>
                <td><strong>Total Harga</strong></td>
                <td>: Rp <?= number_format($data['total_harga'],0,',','.') ?></td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: <?= ucfirst($data['status']) ?></td>
            </tr>
        </table>

        <hr>
        <center><strong>Terima kasih telah melakukan pemesanan!</strong></center>

        <center>
            <a href="#" onclick="window.print()" class="print-btn">🖨 Cetak Struk</a>
            <a href="kelola_transaksi.php" class="back-btn">Kembali</a>
        </center>
    </div>

</body>

</html>