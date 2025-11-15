<?php
session_start();
include 'config/koneksi.php';

// Cek login user
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
  header('Location: login.php');
  exit;
}

$id_villa = $_GET['id'] ?? null;
if (!$id_villa) {
  header('Location: villa_list.php');
  exit;
}

// Ambil data villa
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM detail_villa WHERE id_villa='$id_villa'"));

// Simpan transaksi
if (isset($_POST['pesan'])) {
  $id_pengguna = $_SESSION['id_pengguna'];
  $checkin = $_POST['checkin'];
  $checkout = $_POST['checkout'];
  $status = 'pending';

  $harga_permalam = $data['harga_permalam'];
  $selisih = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);

  if ($selisih <= 0) {
    echo "<script>alert('Tanggal check-out harus setelah check-in!'); window.history.back();</script>";
    exit;
  }

  $total_harga = $harga_permalam * $selisih;

  // Simpan transaksi baru
  $query = "INSERT INTO transaksi (id_pengguna, id_villa, tanggal_checkin, tanggal_checkout, total_harga, status)
            VALUES ('$id_pengguna', '$id_villa', '$checkin', '$checkout', '$total_harga', '$status')";
  mysqli_query($conn, $query);

  // Ubah status villa jadi "dibooking"
  mysqli_query($conn, "UPDATE detail_villa SET status='dibooking' WHERE id_villa='$id_villa'");

  echo "<script>alert('Booking berhasil!'); window.location='user/dashboard.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Booking - <?= $data['nama_villa'] ?> - DAF Villa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
      color: #ffffff;
      min-height: 100vh;
    }

     nav {
      background-color: #5a9ea0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar {
      max-width: 1200px;
      margin: 0 auto;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-logo {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      text-decoration: none;
    }

    .navbar-links {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    .navbar-links li a {
      text-decoration: none;
      color: #fff;
      font-weight: 600;
      transition: 0.3s;
    }

    .navbar-links li a:hover {
      color: #e6e6e6;
      text-decoration: underline;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
      .navbar-links {
        display: none;
      }
    }

    .container-fluid {
      padding: 0 20px;
    }

    .booking-card {
      background: white;
      max-width: 500px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      border: 2px solid #5F9EA0;
      margin: 20px auto;
    }

    .booking-card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .booking-card img:hover {
      transform: scale(1.05);
    }

    .booking-content {
      padding: 30px;
    }

    .booking-content h2 {
      color: #5F9EA0;
      font-weight: 700;
      font-size: 1.8rem;
      margin-bottom: 15px;
      text-align: left;
    }

    .location {
      text-align: left;
      color: #5F9EA0;
      font-size: 1rem;
      margin-bottom: 10px;
    }

    .location i {
      margin-right: 8px;
    }

    .price {
      text-align: left;
      color: #5F9EA0;
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 20px;
      padding: 10px;
      background: linear-gradient(135deg, #c3e2d8 0%, #c3e2d8 100%);
      border-radius: 10px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      color: #5F9EA0;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .form-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #5F9EA0;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: #5F9EA0;
      box-shadow: 0 0 0 3px #c3e2d8;
    }

    .total-price {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
      text-align: center;
      color: white;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .btn-book {
      width: 100%;
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      color: white;
      border: none;
      padding: 15px;
      border-radius: 25px;
      font-weight: 600;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px #c3e2d8;
    }

    .btn-book:hover {
      background: linear-gradient(135deg, #5F9EA0 0%, #5F9EA0 100%);
      transform: translateY(-3px);
      box-shadow: 0 12px 35px #c3e2d8;
    }

    .back-link {
      display: block;
      margin-top: 15px;
      text-align: center;
      color: #5F9EA0;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .back-link:hover {
      color: #5F9EA0;
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .booking-content {
        padding: 20px;
      }

      .booking-content h2 {
        font-size: 1.5rem;
      }

      .booking-card img {
        height: 200px;
      }
    }
  </style>
</head>

<body>
  <nav>
    <div class="navbar navbar-expand-lg fixed-top">
      <a href="#" class="navbar-logo">DAF Villa</a>
      <ul class="navbar-links">
        <li><a href="index.php">Beranda</a></li>
        <li><a href="villa.php">Villa</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid" style="padding-top: 80px;">
    <div class="row">
      <div class="col-md-6">
        <div class="booking-card">
          <img src="assets/img/villa/<?= $data['gambar'] ?>" alt="<?= $data['nama_villa'] ?>">
          <div class="booking-content">
            <h2><?= $data['nama_villa'] ?></h2>
            <p class="location"><i class="fas fa-map-marker-alt"></i><?= $data['lokasi'] ?></p>
            <p class="price">Rp <?= number_format($data['harga_permalam'], 0, ',', '.') ?> / malam</p>

            <form method="POST">
              <div class="form-group">
                <label for="checkin">Check-in:</label>
                <input type="date" id="checkin" name="checkin" required>
              </div>
              <div class="form-group">
                <label for="checkout">Check-out:</label>
                <input type="date" id="checkout" name="checkout" required>
              </div>

              <div class="total-price" id="totalHarga">Total: Rp 0</div>

              <button type="submit" name="pesan" class="btn-book">Pesan Sekarang</button>
            </form>

            <a href="detail_villa.php?id=<?= $data['id_villa'] ?>" class="back-link">Batalkan Pemesanan</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  <script>
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');
    const totalHarga = document.getElementById('totalHarga');
    const hargaPerMalam = <?= $data['harga_permalam'] ?>;

    function hitungTotal() {
      if (checkin.value && checkout.value) {
        const tglIn = new Date(checkin.value);
        const tglOut = new Date(checkout.value);
        const selisih = (tglOut - tglIn) / (1000 * 60 * 60 * 24);

        if (selisih > 0) {
          const total = hargaPerMalam * selisih;
          totalHarga.textContent = `Total (${selisih} malam): Rp ${total.toLocaleString('id-ID')}`;
        } else {
          totalHarga.textContent = "Tanggal tidak valid!";
        }
      }
    }

    checkin.addEventListener('change', hitungTotal);
    checkout.addEventListener('change', hitungTotal);
  </script>
</body>

</html>
