<?php
session_start();
include 'config/koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  header('Location: villa_list.php');
  exit;
}

// Ambil data villa
$query = "SELECT * FROM detail_villa WHERE id_villa='$id'";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$data) {
  echo "<h2>Villa tidak ditemukan.</h2>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $data['nama_villa'] ?> - DAF Villa</title>
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
      color: #5F9EA0;
      min-height: 100vh;
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
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      transition: all 0.3s ease;
      margin: 0 10px;
    }

    .navbar-nav .nav-link:hover {
      color: #c3e2d8ff;
      transform: translateY(-2px);
    }

    /* Detail Section */
    .detail-section {
      padding: 120px 0 80px;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
      overflow: hidden;
    }

    .detail-img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .detail-img:hover {
      transform: scale(1.05);
    }

    .detail-body {
      padding: 40px;
      border: 2px solid #5F9EA0;
      border-radius: 15px;
      background: rgba(255, 255, 255, 0.9);
      box-shadow: 0 8px 25px rgba(95, 158, 160, 0.3);
    }

    .detail-body h2 {
      color: #5F9EA0;
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 15px;
      text-align: center;
    }

    .detail-body .location {
      text-align: center;
      color: #5F9EA0;
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .detail-body .location i {
      color: #5F9EA0;
      margin-right: 8px;
    }

    .price {
      font-size: 1.8rem;
      font-weight: 700;
      color: #5F9EA0;
      text-align: center;
      margin: 25px 0;
      padding: 15px;
      background: linear-gradient(135deg, rgba(106, 90, 205, 0.1) 0%, rgba(72, 61, 139, 0.1) 100%);
      border-radius: 15px;
    }

    .description {
      color: #5F9EA0;
      line-height: 1.8;
      font-size: 1rem;
      margin-bottom: 20px;
      text-align: justify;
    }

    .facilities {
      color: #5F9EA0;
      line-height: 1.6;
      font-size: 1rem;
      margin-bottom: 30px;
      text-align: justify;
    }

    .facilities strong {
      color: #5F9EA0;
      font-weight: 600;
    }

    .btn-book {
      display: inline-block;
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      color: white;
      padding: 15px 30px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px #5F9EA0;
    }

    .btn-book:hover {
      background: linear-gradient(135deg, #c3e2d8ff 0%, #c3e2d8ff 100%);
      transform: translateY(-3px);
      box-shadow: 0 12px 35px #5F9EA0;
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

    @media (max-width: 768px) {
      .detail-body {
        padding: 25px;
      }

      .detail-img {
        height: 250px;
      }

      .detail-body h2 {
        font-size: 1.8rem;
      }

      .price {
        font-size: 1.5rem;
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

  <section class="detail-section">
    <div class="container">

      <div class="detail-body">
        <img src="assets/img/villa/<?= $data['gambar'] ?>" class="detail-img" alt="<?= $data['nama_villa'] ?>">
        <h2><?= $data['nama_villa'] ?></h2>
        <p class="location"><i class="fas fa-map-marker-alt"></i><?= $data['lokasi'] ?></p>
        <p class="price">Rp <?= number_format($data['harga_permalam'], 0, ',', '.') ?> / malam</p>
        <p class="description"><?= nl2br($data['deskripsi']) ?></p>
        <p class="facilities"><?= nl2br($data['fasilitas']) ?></p>

        <div style="text-align: center;">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
            <a href="booking.php?id=<?= $data['id_villa'] ?>" class="btn-book">Pesan Sekarang</a>
          <?php else: ?>
            <a href="login.php" class="btn-book">Login untuk Pesan</a>
          <?php endif; ?>
          <br>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; <?= date('Y') ?> DAF Villa. All rights reserved.
  </footer>

</body>

</html>