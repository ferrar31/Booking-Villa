<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login_admin.php');
  exit;
}

// Ambil filter status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where = ($status_filter != 'all') ? "WHERE t.status='$status_filter'" : "";

$transaksi = mysqli_query($conn, "
    SELECT 
        t.id_transaksi,
        t.id_pengguna,
        t.id_villa,
        t.tanggal_checkin,
        t.tanggal_checkout,
        t.total_harga,
        t.status AS status_transaksi,
        t.created_at,
        t.bukti_pembayaran,
        t.notif_user,
        p.nama_pengguna,
        h.nama_villa,
        h.lokasi
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna
    JOIN detail_villa h ON t.id_villa = h.id_villa
    $where
    ORDER BY t.id_transaksi DESC
");

// Statistik ringkasan
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi"))['total'];
$total_lunas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status='lunas'"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status='pending'"))['total'];
$total_batal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status='batal'"))['total'];
$pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_harga),0) as total FROM transaksi WHERE status='lunas'"))['total'];

// Ubah status transaksi
if (isset($_GET['ubah']) && isset($_GET['id'])) {
  $id = $_GET['id'];
  $ubah = $_GET['ubah'];

  $pesan_notif = ($ubah == 'lunas')
    ? 'Pembayaran Anda telah diverifikasi (LUNAS)'
    : 'Pesanan Anda dibatalkan oleh admin';

  mysqli_query($conn, "UPDATE transaksi SET status='$ubah', notif_user='$pesan_notif' WHERE id_transaksi='$id'");

  if ($ubah == 'lunas') {
    mysqli_query($conn, "UPDATE detail_villa 
                         SET status='dibooking' 
                         WHERE id_villa=(SELECT id_villa FROM transaksi WHERE id_transaksi='$id')");
    $msg = "Transaksi #$id telah diverifikasi (LUNAS)";
  } elseif ($ubah == 'batal') {
    mysqli_query($conn, "UPDATE detail_villa 
                         SET status='tersedia' 
                         WHERE id_villa=(SELECT id_villa FROM transaksi WHERE id_transaksi='$id')");
    $msg = "Transaksi #$id telah DIBATALKAN.";
  }

  echo "<script>alert('$msg'); window.location='kelola_transaksi.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Kelola Transaksi - Admin</title>
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
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
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
      color: #333;
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
      width: 100%;
      margin: 0 auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    h2 {
      color: #ffffff;
      text-align: center;
      margin-bottom: 20px;
      font-style: bold;
    }

    h3 {
      color: #5F9EA0;
      text-align: center;
      margin-bottom: 10px;
    }

    .rekap {
      display: flex;
      justify-content: space-around;
      background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 25px;
      text-align: center;
      border: 1px solid #5F9EA0;
    }

    .rekap div {
      font-weight: 600;
      color: #333;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .filter {
      text-align: center;
      margin-bottom: 20px;
    }

    .filter a {
      padding: 8px 15px;
      border-radius: 6px;
      background: #5F9EA0;
      color: white;
      text-decoration: none;
      margin: 0 5px;
      transition: 0.3s;
    }

    .filter a.active {
      background: #c3e2d8ff;
      color: white;
    }

    .filter a:hover {
      background: #c3e2d8ff;
      color: white;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }

    th,
    td {
      padding: 8px 6px;
      border-bottom: 1px solid #ddd;
      text-align: center;
      vertical-align: middle;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    th {
      background: #5F9EA0;
      color: white;
      font-weight: 600;
      white-space: nowrap;
    }

    td:nth-child(2),
    td:nth-child(3),
    td:nth-child(4) {
      max-width: 120px;
      text-align: left;
    }

    td:nth-child(5),
    td:nth-child(6) {
      white-space: nowrap;
    }

    td:nth-child(7) {
      font-weight: bold;
      color: #5F9EA0;
    }

    tr:hover {
      background: #f9f9f9;
    }

    .btn {
      padding: 5px 10px;
      border-radius: 5px;
      color: white;
      text-decoration: none;
      font-size: 0.9rem;
      border: none;
      cursor: pointer;
    }

    .btn-lunas {
      background: #28a745;
    }

    .btn-lunas:hover {
      background: #218838;
    }

    .btn-batal {
      background: #dc3545;
    }

    .btn-batal:hover {
      background: #c82333;
    }

    .btn-detail {
      background: #007bff;
    }

    .btn-detail:hover {
      background: #0069d9;
    }

    .status {
      font-weight: bold;
      text-transform: capitalize;
    }

    .pending {
      color: #ffc107;
    }

    .lunas {
      color: #28a745;
    }

    .batal {
      color: #dc3545;
    }

    .bukti-link {
      color: #5F9EA0;
      text-decoration: underline;
      cursor: pointer;
      font-weight: 600;
    }

    .bukti-link:hover {
      color: #c3e2d8ff;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      position: relative;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-content img {
      width: 100%;
      border-radius: 8px;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      cursor: pointer;
      color: #333;
    }

    .close:hover {
      color: #dc3545;
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
    <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
    <a href="kelola_villa.php"><i class="bi bi-houses"></i> Kelola Villa</a>
    <a href="kelola_transaksi.php" class="active"><i class="bi bi-cash-coin"></i> Kelola Transaksi</a>
    <a href="kelola_pengguna.php"><i class="bi bi-person-gear"></i> Kelola Pengguna</a>
    <a href="../logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>

  <div class="main-content">
    <header>
      <h1>Kelola Transaksi</h1>
      <p>Halo, <?= $_SESSION['nama_admin']; ?> 👋</p>
    </header>

    <div class="container">
      <!-- PESAN SUKSES -->
      <?php if (isset($_SESSION['message'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
          <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
      <?php endif; ?>

      <!-- RINGKASAN -->
      <div class="rekap">
        <div>📊 Total Transaksi: <?= $total_transaksi ?></div>
        <div>💰 Pendapatan: Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
        <div>✅ Lunas: <?= $total_lunas ?></div>
        <div>⏳ Pending: <?= $total_pending ?></div>
        <div>❌ Batal: <?= $total_batal ?></div>
      </div>

      <h3 style="text-align:center;">Daftar Transaksi Pengguna</h3>

      <!-- FILTER STATUS -->
      <div class="filter">
        <a href="kelola_transaksi.php?status=all" class="<?= $status_filter == 'all' ? 'active' : '' ?>">Semua</a>
        <a href="kelola_transaksi.php?status=pending" class="<?= $status_filter == 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="kelola_transaksi.php?status=lunas" class="<?= $status_filter == 'lunas' ? 'active' : '' ?>">Lunas</a>
        <a href="kelola_transaksi.php?status=batal" class="<?= $status_filter == 'batal' ? 'active' : '' ?>">Batal</a>
      </div>

      <form method="GET" action="export_excel.php" style="text-align:right; margin-bottom:20px;">
        <label>Dari: </label>
        <input type="date" id="startDate" name="start" required>
        <label>Sampai: </label>
        <input type="date" id="endDate" name="end" required>
        <button type="submit" style="background:#5F9EA0; color:white; padding:8px 15px; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
          📊 Export ke Excel
        </button>
      </form>

      <div class="table-wrapper">
        <table>
          <tr>
            <th>ID</th>
            <th>Nama Pengguna</th>
            <th>Villa</th>
            <th>Lokasi</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
            <th>Bukti Pembayaran</th>
          </tr>
          <?php $no = 1;
          while ($row = mysqli_fetch_assoc($transaksi)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
              <td><?= htmlspecialchars($row['nama_villa']) ?></td>
              <td><?= htmlspecialchars($row['lokasi']) ?></td>
              <td><?= $row['tanggal_checkin'] ?></td>
              <td><?= $row['tanggal_checkout'] ?></td>
              <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
              <td class="status <?= $row['status_transaksi'] ?>"><?= ucfirst($row['status_transaksi']) ?></td>

              <td class="aksi">
                <?php if ($row['status_transaksi'] == 'pending'): ?>
                  <a href="?ubah=lunas&id=<?= $row['id_transaksi'] ?>" class="btn btn-lunas"
                    onclick="return confirm('Verifikasi pembayaran ini?')"><i class="bi bi-check-square"></i>
                  </a>
                  <a href="?ubah=batal&id=<?= $row['id_transaksi'] ?>" class="btn btn-batal"
                    onclick="return confirm('Tolak pembayaran ini?')"><i class="bi bi-x-square"></i>
                  </a>
                <?php endif; ?>
                <a href="detail_transaksi_admin.php?id=<?= $row['id_transaksi'] ?>" class="btn btn-detail"><i class="bi bi-ticket-detailed"></i>
                </a>
              </td>

              <td>
                <?php if (!empty($row['bukti_pembayaran'])): ?>
                  <span class="bukti-link" onclick="openModal('../assets/bukti/<?= $row['bukti_pembayaran'] ?>')">
                    Lihat Bukti
                  </span>
                <?php else: ?><em>Belum ada</em><?php endif; ?>
              </td>

            </tr>
          <?php endwhile; ?>
        </table>
      </div>

    </div>

    <!-- Modal Preview Bukti -->
    <div class="modal" id="buktiModal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="buktiImg" src="" alt="Bukti Pembayaran">
      </div>
    </div>

    <script>
      function openModal(src) {
        document.getElementById('buktiImg').src = src;
        document.getElementById('buktiModal').style.display = 'flex';
      }

      function closeModal() {
        document.getElementById('buktiModal').style.display = 'none';
      }
      window.onclick = e => {
        if (e.target == document.getElementById('buktiModal'))
          closeModal();
      }
    </script>
  </div>
</body>

</html>