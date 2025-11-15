<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login_admin.php');
  exit;
}

// ====== TAMBAH DATA VILLA ======
if (isset($_POST['tambah'])) {
  $nama = $_POST['nama'];
  $lokasi = $_POST['lokasi'];
  $harga = $_POST['harga'];
  $deskripsi = $_POST['deskripsi'];
  $fasilitas = $_POST['fasilitas'];

  // Upload gambar
  $namaFile = $_FILES['gambar']['name'];
  $tmpName = $_FILES['gambar']['tmp_name'];
  $path = "../assets/img/villa/" . $namaFile;

  if (move_uploaded_file($tmpName, $path)) {
    $query = "INSERT INTO detail_villa (nama_villa, lokasi, harga_permalam, deskripsi, fasilitas, gambar)
              VALUES ('$nama', '$lokasi', '$harga', '$deskripsi', '$fasilitas', '$namaFile')";
    mysqli_query($conn, $query);
  }
  header('Location: kelola_villa.php');
  exit;
}

// ====== HAPUS DATA ======
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];

  // Hapus transaksi terkait terlebih dahulu
  mysqli_query($conn, "DELETE FROM transaksi WHERE id_villa='$id'");

  // Hapus gambar villa jika ada
  $getGambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM detail_villa WHERE id_villa='$id'"));
  if ($getGambar && file_exists("../assets/img/villa/".$getGambar['gambar'])) {
    unlink("../assets/img/villa/".$getGambar['gambar']);
  }

  // Hapus villa
  mysqli_query($conn, "DELETE FROM detail_villa WHERE id_villa='$id'");
  header('Location: kelola_villa.php');
  exit;
}

// ====== EDIT DATA ======
if (isset($_POST['update'])) {
  $id = $_POST['id_villa'];
  $nama = $_POST['nama'];
  $lokasi = $_POST['lokasi'];
  $harga = $_POST['harga'];
  $deskripsi = $_POST['deskripsi'];
  $fasilitas = $_POST['fasilitas'];

  // kalau upload gambar baru
  if (!empty($_FILES['gambar']['name'])) {
    $namaFile = $_FILES['gambar']['name'];
    $tmpName = $_FILES['gambar']['tmp_name'];
    $path = "../assets/img/villa/" . $namaFile;
    move_uploaded_file($tmpName, $path);
    $query = "UPDATE detail_villa SET nama_villa='$nama', lokasi='$lokasi', harga_permalam='$harga', deskripsi='$deskripsi', fasilitas='$fasilitas', gambar='$namaFile' WHERE id_villa='$id'";
  } else {
    $query = "UPDATE detail_villa SET nama_villa='$nama', lokasi='$lokasi', harga_permalam='$harga', deskripsi='$deskripsi', fasilitas='$fasilitas' WHERE id_villa='$id'";
  }
  mysqli_query($conn, $query);
  header('Location: kelola_villa.php');
  exit;
}

// ====== AMBIL DATA ======
$villa = mysqli_query($conn, "SELECT * FROM detail_villa");
$editData = null;
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM detail_villa WHERE id_villa='$id'"));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Villa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #f5f7fa; color: #333; display: flex; }

    .sidebar {
      width: 250px;
      background: linear-gradient(135deg, #5F9EA0, #5F9EA0);
      color: white;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 1.2rem; }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      margin: 10px 0;
      border-radius: 6px;
      transition: 0.3s;
    }
    .sidebar a:hover, .sidebar a.active { background: #c3e2d8ff; }
    .sidebar .logout { background: rgba(255,255,255,0.1); margin-top: 50px; }
    .sidebar .logout:hover { background: #c3e2d8ff; }

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
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    header h1 { font-size: 1.5rem; color: #5F9EA0; }
    header p { font-size: 0.9rem; color: #666; }

    .container { width: 100%; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border-bottom: 1px solid #5F9EA0; text-align: left; }
    th { background: #5F9EA0; color: white; }
    .btn { padding: 6px 12px; border-radius: 5px; color: white; font-size: 14px; text-decoration: none; }
    .btn-hapus { background: #dc3545; }
    .btn-edit { background: #ffc107; color: #333; }
    .btn-hapus:hover { background: #c82333; }
    .btn-edit:hover { background: #e0a800; }
    input, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #5F9EA0; border-radius: 5px; font-size: 14px; }
    button { background: #5F9EA0; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
    button:hover { background: #c3e2d8ff; }
    img { width: 100px; border-radius: 6px; }
    .back { display: inline-block; margin-top: 20px; color: #5F9EA0; font-weight: bold; }

    @media (max-width: 768px) {
      .sidebar { width: 200px; }
      .main-content { margin-left: 200px; width: calc(100% - 200px); }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Villa</h2>
  <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
  <a href="kelola_villa.php" class="active"><i class="bi bi-houses"></i> Kelola Villa</a>
  <a href="kelola_transaksi.php"><i class="bi bi-cash-coin"></i> Kelola Transaksi</a>
  <a href="kelola_pengguna.php"><i class="bi bi-person-gear"></i> Kelola Pengguna</a>
  <a href="../logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
</div>

<div class="main-content">
  <header>
    <h1>Kelola Villa</h1>
    <p>Halo, <?php echo $_SESSION['nama_admin']; ?> 👋</p>
  </header>

  <div class="container">
    <h3><?= $editData ? 'Edit Villa' : 'Tambah Villa Baru' ?></h3>
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editData): ?>
        <input type="hidden" name="id_villa" value="<?= $editData['id_villa'] ?>">
      <?php endif; ?>
      <input type="text" name="nama" placeholder="Nama Villa" value="<?= $editData['nama_villa'] ?? '' ?>" required>
      <input type="text" name="lokasi" placeholder="Lokasi" value="<?= $editData['lokasi'] ?? '' ?>" required>
      <input type="number" name="harga" placeholder="Harga per malam" value="<?= $editData['harga_permalam'] ?? '' ?>" required>
      <textarea name="fasilitas" placeholder="Fasilitas (pisahkan dengan koma atau baris baru)" rows="3" required><?= $editData['fasilitas'] ?? '' ?></textarea>
      <textarea name="deskripsi" placeholder="Deskripsi" rows="3" required><?= $editData['deskripsi'] ?? '' ?></textarea>
      <input type="file" name="gambar" accept="image/*">
      <button type="submit" name="<?= $editData ? 'update' : 'tambah' ?>">
        <?= $editData ? 'Update Villa' : 'Tambah Villa' ?>
      </button>
    </form>

    <h3>Daftar Villa</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Lokasi</th>
        <th>Harga</th>
        <th>Fasilitas</th>
        <th>Deskripsi</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($villa)): ?>
      <tr>
        <td><?= $row['id_villa'] ?></td>
        <td><img src="../assets/img/villa/<?= $row['gambar'] ?>" alt=""></td>
        <td><?= $row['nama_villa'] ?></td>
        <td><?= $row['lokasi'] ?></td>
        <td>Rp <?= number_format($row['harga_permalam'], 0, ',', '.') ?></td>
        <td><?= nl2br($row['fasilitas']) ?></td>
        <td><?= nl2br($row['deskripsi']) ?></td>
        <td>
          <a href="kelola_villa.php?edit=<?= $row['id_villa'] ?>" class="btn btn-edit">Edit</a>
          <a href="kelola_villa.php?hapus=<?= $row['id_villa'] ?>" class="btn btn-hapus" onclick="return confirm('Yakin mau hapus villa ini?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>

  </div>
</div>
</body>
</html>
