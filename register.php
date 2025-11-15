<?php
include 'config/koneksi.php';
$msg = '';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Cek email sudah digunakan atau belum
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $msg = "Email sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO pengguna (nama_pengguna, email, password, no_telp, alamat)
                         VALUES ('$nama', '$email', '$password', '$no_telp', '$alamat')");
        $msg = "Registrasi berhasil! Silakan login.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - DAF Villa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #5F9EA0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 380px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            text-align: center;
            color: #5F9EA0;
            margin-bottom: 20px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #5F9EA0;
            border-radius: 5px;
        }

        .login {
            text-align: center;
            margin-top: 15px;
        }

        .login a {
            color: #5F9EA0;
            text-decoration: none;
            font-weight: 600;
        }

        button {
            width: 100%;
            background: #5F9EA0;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #5F9EA0;
        }

        .msg {
            text-align: center;
            margin-bottom: 15px;
            color: #5F9EA0;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="register-box">
            <h2>Daftar Akun</h2>
            <?php if ($msg): ?>
                <div class="msg"><?= $msg ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="nama" placeholder="Nama Lengkap" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="no_telp" placeholder="Nomor Telepon" required>
                <textarea name="alamat" placeholder="Alamat Lengkap" rows="2" required></textarea>
                <button type="submit" name="register">Daftar Sekarang</button>
            </form>

            <div class="login">
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>