<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Hapus pengguna
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengguna WHERE id_pengguna='$id'");
    echo "<script>alert('Pengguna berhasil dihapus!'); window.location='kelola_pengguna.php';</script>";
    exit;
}

// Edit pengguna
if (isset($_POST['update_pengguna'])) {
    $id = $_POST['id_pengguna'];
    $nama = $_POST['nama_pengguna'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    
    $update_query = "UPDATE pengguna SET nama_pengguna='$nama', email='$email', no_telp='$no_telp', alamat='$alamat'";
    
    // Jika password diisi, hash password terlebih dahulu
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query .= ", password='$password'";
    }
    
    $update_query .= " WHERE id_pengguna='$id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Pengguna berhasil diupdate!'); window.location='kelola_pengguna.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate pengguna!');</script>";
    }
    exit;
}

// Ambil data pengguna
$pengguna = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id_pengguna DESC");
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
    }

    h3 {
      color: #5F9EA0;
      text-align: center;
      margin-bottom: 10px;
    }

    .input-group {
        display: flex;
        justify-content: center;
        text-align: center;
        margin-bottom: 30px;
        margin-top: -50px;
    }

    .input-group input {
        width: 60%;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 0.95rem;
        outline: none;
        transition: 0.3s;
    }

    .input-group input:focus {
        border-color: #5F9EA0;
        box-shadow: 0 0 5px #5F9EA0;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background: #5F9EA0;
    }

    tr:hover {
        background: #f1f8f2;
    }

    .btn-hapus {
        background: #c62828;
        color: white;
        padding: 5px 8px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .btn-hapus:hover {
        background: #a01818;
    }

    .btn-edit {
        background: #1976d2;
        color: white;
        padding: 5px 8px;
        border-radius: 4px;
        border: none;
        font-size: 0.85rem;
        cursor: pointer;
        margin-right: 5px;
    }

    .btn-edit:hover {
        background: #1565c0;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
    }

    .modal-content h3 {
        text-align: left;
        color: #5F9EA0;
        margin-top: 0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 0.95rem;
        font-family: 'Poppins', sans-serif;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #5F9EA0;
        box-shadow: 0 0 5px #5F9EA0;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 60px;
    }

    .modal-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .modal-buttons button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-save {
        background: #28a745;
        color: white;
    }

    .btn-save:hover {
        background: #218838;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background: #5a6268;
    }

    footer {
        text-align: center;
        color: #5F9EA0;
        margin-top: 40px;
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
    <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
    <a href="kelola_villa.php"><i class="bi bi-houses"></i> Kelola Villa</a>
    <a href="kelola_transaksi.php"><i class="bi bi-cash-coin"></i> Kelola Transaksi</a>
    <a href="kelola_pengguna.php" class="active" ><i class="bi bi-person-gear"></i> Kelola Pengguna</a>
    <a href="../logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </div>

  <div class="main-content">
    <header>
      <h1>Kelola Pengguna</h1>
      <p>Halo, <?= $_SESSION['nama_admin']; ?> 👋</p>
    </header>

    <div class="container">
            <h2>Daftar Pengguna Terdaftar</h2>

            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search" id="searchIcon"></i></span>
                <input type="text" id="searchInput"   placeholder="Cari pengguna berdasarkan nama atau email...">
            </div>

            <table id="penggunaTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($pengguna) > 0): ?>
                        <?php $no = 1;
                        while ($p = mysqli_fetch_assoc($pengguna)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($p['nama_pengguna']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= $p['no_telp'] ?: '-' ?></td>
                                <td><?= $p['alamat'] ?: '-' ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editPengguna(<?= htmlspecialchars(json_encode($p)) ?>)">Edit</button>
                                    <a href="?hapus=<?= $p['id_pengguna'] ?>" class="btn-hapus"
                                        onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Belum ada pengguna terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Edit Pengguna -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Edit Pengguna</h3>
                <form id="editForm" method="POST">
                    <input type="hidden" name="id_pengguna" id="id_pengguna">
                    
                    <div class="form-group">
                        <label for="nama_pengguna">Nama Pengguna</label>
                        <input type="text" id="nama_pengguna" name="nama_pengguna" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="no_telp">No. Telepon</label>
                        <input type="text" id="no_telp" name="no_telp">
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="password">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password baru atau kosongkan">
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                        <button type="submit" name="update_pengguna" class="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <footer>
            © <?= date('Y') ?> DAF Villa | All Rights Reserved
        </footer>
    </div>

    <script>
        const modal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');

        // Buka modal edit
        function editPengguna(data) {
            document.getElementById('id_pengguna').value = data.id_pengguna;
            document.getElementById('nama_pengguna').value = data.nama_pengguna;
            document.getElementById('email').value = data.email;
            document.getElementById('no_telp').value = data.no_telp || '';
            document.getElementById('alamat').value = data.alamat || '';
            document.getElementById('password').value = '';
            modal.style.display = 'block';
        }

        // Tutup modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Tutup modal saat klik tombol close
        closeBtn.onclick = function() {
            closeModal();
        }

        // Tutup modal saat klik di luar modal
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Fitur search realtime
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('#penggunaTable tbody tr');

        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();

            tableRows.forEach(row => {
                const nama = row.children[1].textContent.toLowerCase();
                const email = row.children[2].textContent.toLowerCase();

                if (nama.includes(query) || email.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>