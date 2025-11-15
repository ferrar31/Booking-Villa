<?php
session_start();
include '../config/koneksi.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
  header('Location: ../login.php');
  exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$nama_pengguna = $_SESSION['nama_pengguna'] ?? 'Pengguna';

// Panduan pembayaran (bisa disesuaikan)
$rekening_bank = "BCA - 1234567890 Arjuna Villa";
$rekening_dana = "DANA - 082125638205 Arjuna Villa";

// Batalkan booking
if (isset($_GET['batal'])) {
  $id_transaksi = $_GET['batal'];
  $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_villa FROM transaksi WHERE id_transaksi='$id_transaksi' AND id_pengguna='$id_pengguna'"));
  if ($get) {
    $id_villa = $get['id_villa'];
    mysqli_query($conn, "UPDATE transaksi SET status='batal' WHERE id_transaksi='$id_transaksi'");
    mysqli_query($conn, "UPDATE detail_villa SET status='tersedia' WHERE id_villa='$id_villa'");
  }
  header('Location: dashboard.php');
  exit;
}

$booking = mysqli_query($conn, "
  SELECT t.*, h.nama_villa, h.lokasi
  FROM transaksi t
  JOIN detail_villa h ON t.id_villa = h.id_villa
  WHERE t.id_pengguna='$id_pengguna'
  ORDER BY t.id_transaksi DESC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Pengguna - DAF Villa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #ffffffff;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px #5F9EA0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .navbar-nav .nav-link {
      color: #ffffff;
      font-weight: 500;
      transition: all 0.3s ease;
      margin: 0 10px;
    }

    .navbar-nav .nav-link:hover {
      color: #c3e2d8ff;
      transform: translateY(-2px);
    }

    .payment-info {
      background: #ffffff;
      padding: 15px 20px;
      border-radius: 8px;
      border-left: 6px solid #5F9EA0;
      margin-bottom: 25px;
      margin-top: 80px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .payment-info h3 {
      color: #5F9EA0;
      margin-top: 5px;
    }

    .payment-info p {
      margin: 6px 0;
      color: #333;
    }

    .payment-info code {
      background: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: bold;
    }

    h2 {
      color: #5F9EA0;
      text-align: center;
      margin-bottom: 25px;
    }

    .btn-book {
      display: inline-block;
      background: #5F9EA0;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-book:hover {
      background: #c3e2d8ff;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 0.95rem;
    }

    th,
    td {
      padding: 10px;
      border-bottom: 1px solid #5F9EA0;
      text-align: left;
    }

    th {
      background: #5F9EA0;
      color: white;
    }

    tr {
      background: #fff;
    }

    td:hover {
      background: #c3e2d8ff;
      transition: 0.2s;
    }

    .status {
      font-weight: bold;
      text-transform: capitalize;
      border-radius: 5px;
      padding: 4px 8px;
      display: inline-block;
    }

    .pending {
      background: #5F9EA0;
      color: #ffffffff;
    }

    .lunas {
      background: #5F9EA0;
      color: #ffffffff;
    }

    .batal {
      background: #5F9EA0;
      color: #ffffffff;
    }

    .btn-batal {
      background: #dc3545;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .btn-batal:hover {
      background: #c82333;
    }

    .btn-lunas {
      background: #28a745;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      margin-right: 5px;
    }

    .btn-lunas:hover {
      background: #218838;
    }

    .btn-pending {
      background: #ffc107;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      margin-right: 5px;
    }

    .btn-pending:hover {
      background: #e0a800;
    }

    form.upload {
      display: flex;
      gap: 5px;
      align-items: center;
    }

    form.upload input[type=file] {
      flex: 1;
      border: 1px solid #5F9EA0;
      padding: 5px;
      border-radius: 5px;
    }

    form.upload button {
      background: #5F9EA0;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    form.upload button:hover {
      background: #c3e2d8ff;
    }

    .back {
      display: inline-block;
      margin-top: 25px;
      text-decoration: none;
      color: #5F9EA0;
      font-weight: bold;
    }

    .no-data {
      text-align: center;
      background: #ffffffff;
      padding: 20px;
      border-radius: 10px;
      color: #ffffffff;
    }

    .notif {
      background: #c3e2d8ff;
      color: #5F9EA0;
      border: 1px solid #5F9EA0;
      padding: 12px;
      border-radius: 8px;
      font-weight: 600;
      text-align: center;
      margin: 15px auto;
      width: 90%;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .toast-notif {
      position: fixed;
      top: 25px;
      right: 25px;
      background: #5F9EA0;
      color: #fff;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      font-weight: 600;
      z-index: 9999;
      opacity: 0;
      transform: translateY(-20px);
      animation: fadeSlideIn 0.6s ease forwards;
    }

    .toast-content {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    @keyframes fadeSlideIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateY(-10px);
      }
    }
  </style>
</head>

<body>
  <?php
  // Ambil notifikasi user (kalau ada)
  $notif = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT notif_user FROM transaksi 
  WHERE id_pengguna='$id_pengguna' 
  AND notif_user IS NOT NULL 
  ORDER BY id_transaksi DESC LIMIT 1
"));
  if ($notif && $notif['notif_user']) {
    $pesan_notif = htmlspecialchars($notif['notif_user']);

    // Hapus notif setelah ditampilkan agar tidak muncul berulang
    mysqli_query($conn, "
    UPDATE transaksi SET notif_user=NULL 
    WHERE id_pengguna='$id_pengguna'
  ");

    echo "
  <div class='toast-notif' id='toastNotif'>
    <div class='toast-content'>
      🔔 $pesan_notif
    </div>
  </div>
  ";
  }
  ?>

  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#"></i>DAF Villa</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="../villa_list.php">Villa</a>
          </li>
          <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 'user'): ?>
              <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
              </li>
            <?php elseif ($_SESSION['role'] == 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="../admin/dashboard.php">Admin Panel</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
              </li>
            <?php endif; ?>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="payment-info">
      <h3>💳 Panduan Pembayaran</h3>
      <p>Silakan lakukan pembayaran melalui salah satu metode di bawah ini:</p>
      <p><b>🏦 Rekening Bank:</b> <code><?= $rekening_bank ?></code></p>
      <p><b>📱 DANA:</b> <code><?= $rekening_dana ?></code></p>
      <p>Setelah transfer, silakan upload bukti pembayaran di tabel “Bukti Pembayaran” di bawah.</p>
    </div>

    <h2>Riwayat Booking Anda</h2>
    <div style="text-align:center; margin-bottom: 20px;">
      <a href="../villa_list.php" class="btn-book">+ Booking Sekarang</a>
    </div>

    <table>
      <tr>
        <th>No</th>
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
      if (mysqli_num_rows($booking) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($booking)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_villa'] ?></td>
            <td><?= $row['lokasi'] ?></td>
            <td><?= $row['tanggal_checkin'] ?></td>
            <td><?= $row['tanggal_checkout'] ?></td>
            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
            <td><span class="status <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
            <td>
              <?php if ($row['status'] == 'pending'): ?>
                <a href="?batal=<?= $row['id_transaksi'] ?>" class="btn-batal"
                  onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
              <?php else: ?> - <?php endif; ?>
            </td>
            <td>
              <?php if ($row['status'] == 'pending'): ?>
                <form class="upload" method="POST" enctype="multipart/form-data" action="upload_bukti.php">
                  <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
                  <input type="file" name="bukti" accept="image/*" required>
                  <button type="submit" name="upload">Upload</button>
                </form>
              <?php elseif ($row['bukti_pembayaran']): ?>
                <a href="../assets/bukti/<?= $row['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti</a>
              <?php else: ?>
                <em>Belum diunggah</em>
              <?php endif; ?>
              /
              <?php if ($row['status'] == 'lunas'): ?>
                <a href="detail_transaksi.php?id=<?= $row['id_transaksi'] ?>" class="btn-print"
                  style=" font-weight:bold;">
                  Lihat Struk
                </a>
              <?php else: ?>
                -
              <?php endif; ?>

            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="9">
            <div class="no-data">
              Belum ada data booking.<br>
              <a href="../villa_list.php" class="btn-book" style="margin-top:10px;">Mulai Booking
                Sekarang</a>
            </div>
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>

  <script>
    function toggleDropdown() {
      document.getElementById('dropdownMenu').classList.toggle('show');
    }

    window.onclick = function(event) {
      if (!event.target.matches('.user-name')) {
        const dropdowns = document.getElementsByClassName('dropdown');
        for (let i = 0; i < dropdowns.length; i++) {
          dropdowns[i].classList.remove('show');
        }
      }
    }
  </script>

</body>

</html>