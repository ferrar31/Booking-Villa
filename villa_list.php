<?php
session_start();
include 'config/koneksi.php';

$filter = $_GET['filter'] ?? 'all';
if ($filter === 'tersedia') {
  $query = "SELECT * FROM detail_villa WHERE status='tersedia' ORDER BY id_villa DESC";
} else {
  $query = "SELECT * FROM detail_villa ORDER BY id_villa DESC";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Villa - DAF Villa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #ffffffff 0%, #ffffffff 100%);
      color: #ffffffff;
      min-height: 100vh;
    }

    /* Enhanced Navbar */
    .navbar {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(106, 90, 205, 0.3);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .navbar-nav .nav-link {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      transition: all 0.3s ease;
      margin: 0 10px;
    }

    .navbar-nav .nav-link:hover {
      color: #5F9EA0;
      transform: translateY(-2px);
    }

    /* Villa Section */
    .section-villa {
      padding: 80px 0;
      background: white;
    }

    .section-villa h2 {
      text-align: center;
      color: #5F9EA0;
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 50px;
      position: relative;
    }

    .section-villa h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      border-radius: 2px;
    }

    .villa-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      padding: 0 50px;
    }

    .card {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      border: 1px solid rgba(106, 90, 205, 0.1);
    }

    .card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 40px rgba(106, 90, 205, 0.2);
    }

    .card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover img {
      transform: scale(1.1);
    }

    .card-body {
      padding: 25px;
    }

    .card-body h3 {
      color: #5F9EA0;
      font-weight: 600;
      margin-bottom: 10px;
      font-size: 1.2rem;
    }

    .card-body p {
      color: #5F9EA0;
      margin-bottom: 8px;
      font-size: 0.95rem;
    }

    .card-body .price {
      color: #5F9EA0;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 15px;
    }

    .card-body .btn-view {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      color: white;
      padding: 8px 20px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .card-body .btn-view:hover {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      transform: translateY(-2px);
    }

    .empty {
      text-align: center;
      color: #5F9EA0;
      font-style: italic;
      margin-top: 50px;
      font-size: 1.1rem;
    }

    /* Footer */
    footer {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      color: white;
      text-align: center;
      padding: 40px 20px;
      margin-top: 80px;
      position: relative;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .villa-list {
        padding: 0 20px;
        grid-template-columns: 1fr;
      }

      .section-villa h2 {
        font-size: 2rem;
      }
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">DAF Villa</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="villa_list.php">Villa</a>
          </li>
          <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 'user'): ?>
              <li class="nav-item">
                <a class="nav-link" href="user/dashboard.php">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
              </li>
            <?php elseif ($_SESSION['role'] == 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="admin/dashboard.php">Admin Panel</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
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

  <section class="section-villa">
    <h2>Daftar Villa Tersedia</h2>
    <div class="d-flex justify-content-center mb-4">
      <a href="?filter=all" class="btn btn-outline me-2 <?= ($filter === 'all') ? 'active' : '' ?>" style="color: #5F9EA0; border-color: #5F9EA0;">Semua</a>
      <a href="?filter=tersedia" class="btn me-2 <?= ($filter === 'tersedia') ? 'active' : '' ?>" style="background-color: #5F9EA0; color: white; border: 1px solid #5F9EA0;">Hanya Tersedia</a>
    </div>
    <div class="villa-list">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="card">
            <a href="detail_villa.php?id=<?= $row['id_villa'] ?>" class="needs-check" data-status="<?= isset($row['status']) ? $row['status'] : 'tersedia' ?>">
              <img src="assets/img/villa/<?= $row['gambar'] ?>" alt="<?= $row['nama_villa'] ?>">
            </a>
            <div class="card-body">
              <h3><?= $row['nama_villa'] ?></h3>
              <?php
              $status = isset($row['status']) ? $row['status'] : 'tersedia';
              if ($status === 'tersedia') {
                echo '<p><span class="badge bg-success">Tersedia</span></p>';
              } elseif ($status === 'dibooking') {
                echo '<p><span class="badge bg-warning text-dark">Dibooking</span></p>';
              } else {
                echo '<p><span class="badge bg-danger">' . htmlspecialchars(ucfirst($status)) . '</span></p>';
              }
              ?>
              <p class="price">Rp <?= number_format($row['harga_permalam'], 0, ',', '.') ?> / malam</p>
              <p><i class="fas fa-map-marker-alt me-1"></i><?= $row['lokasi'] ?></p>
              <p><?= substr($row['deskripsi'], 0, 80) ?>...</p>
              <a href="detail_villa.php?id=<?= $row['id_villa'] ?>" class="btn-view needs-check" data-status="<?= $status ?>">Lihat Detail</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="empty">Belum ada villa yang tersedia saat ini.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer>
    &copy; <?= date('Y') ?> DAF Villa. All rights reserved.
  </footer>

  <script>
    // Notify if villa already booked and prevent navigation
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.needs-check').forEach(function(el) {
        el.addEventListener('click', function(e) {
          var status = el.getAttribute('data-status') || 'tersedia';
          if (status === 'dibooking') {
            e.preventDefault();
            alert('Villa sudah dibooking');
          }
        });
      });
    });
  </script>

</body>

</html>