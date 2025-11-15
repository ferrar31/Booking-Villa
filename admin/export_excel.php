<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login_admin.php');
    exit;
}

// Ambil tanggal dari form (jika ada)
$start = isset($_GET['start']) ? mysqli_real_escape_string($conn, $_GET['start']) : null;
$end   = isset($_GET['end']) ? mysqli_real_escape_string($conn, $_GET['end']) : null;

// Validasi tanggal
if (!$start || !$end) {
    die("<script>alert('Silakan pilih rentang tanggal terlebih dahulu!'); window.location='kelola_transaksi.php';</script>");
}

// Header agar langsung download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_transaksi_{$start}_sd_{$end}.xls");

// Query berdasarkan tanggal
$query = mysqli_query($conn, "
    SELECT t.id_transaksi, p.nama_pengguna, h.nama_villa, h.lokasi,
           t.tanggal_checkin, t.tanggal_checkout, t.total_harga, t.status
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna
    JOIN detail_villa h ON t.id_villa = h.id_villa
    WHERE DATE(t.tanggal_checkin) BETWEEN '$start' AND '$end'
    ORDER BY t.id_transaksi DESC
");

echo "<h3 style='color:#333;'>Laporan Transaksi Villa</h3>";
echo "<p>Periode: <b>" . date('d M Y', strtotime($start)) . "</b> s/d <b>" . date('d M Y', strtotime($end)) . "</b></p>";
echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse; font-family:Arial, sans-serif; font-size:13px;'>";
echo "<tr style='background:#5F9EA0; color:white; font-weight:bold; text-align:center;'>
        <th>ID</th>
        <th>Nama Pengguna</th>
        <th>Villa</th>
        <th>Lokasi</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Total Harga</th>
        <th>Status</th>
      </tr>";

$totalPendapatan = 0;
$totalData = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $status = strtolower(trim($row['status']));
    $bgColor = '#ffffff'; // default putih

    // Warna otomatis sesuai status
    switch ($status) {
        case 'dibayar':
        case 'lunas':
        case 'selesai':
            $bgColor = '#c3f7c0'; // hijau muda
            $totalPendapatan += $row['total_harga'];
            break;
        case 'pending':
            $bgColor = '#fff8c4'; // kuning muda
            break;
        case 'batal':
            $bgColor = '#f7c0c0'; // merah muda
            break;
    }

    echo "<tr style='background:{$bgColor};'>
          <td>{$row['id_transaksi']}</td>
          <td>{$row['nama_pengguna']}</td>
          <td>{$row['nama_villa']}</td>
          <td>{$row['lokasi']}</td>
          <td>{$row['tanggal_checkin']}</td>
          <td>{$row['tanggal_checkout']}</td>
          <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
          <td style='text-transform:capitalize;'>{$row['status']}</td>
        </tr>";

    $totalData++;
}

// Total pendapatan
if ($totalData > 0) {
    echo "<tr style='font-weight:bold; background:#5F9EA0; color:white;'>
          <td colspan='6' style='text-align:right;'>Total Pendapatan (Dibayar/Selesai)</td>
          <td colspan='2'>Rp " . number_format($totalPendapatan, 0, ',', '.') . "</td>
        </tr>";
} else {
    echo "<tr><td colspan='8' style='text-align:center;'>Tidak ada transaksi pada rentang tanggal ini.</td></tr>";
}

echo "</table>";
?>
