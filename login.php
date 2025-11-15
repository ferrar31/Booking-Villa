<?php
session_start();
include 'config/koneksi.php';

$error = ""; // supaya gak undefined di tampilan

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Cek di tabel pengguna
    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");

    if (mysqli_num_rows($result) === 1) {
        $data = mysqli_fetch_assoc($result);

        if (password_verify($password, $data['password'])) {
            $_SESSION['id_pengguna'] = $data['id_pengguna'];
            $_SESSION['nama_pengguna'] = $data['nama_pengguna'];
            $_SESSION['role'] = 'user';
            header('Location: user/dashboard.php');
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        // admin
        $query_admin = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email'");
        if (mysqli_num_rows($query_admin) === 1) {
            $admin = mysqli_fetch_assoc($query_admin);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['id_admin'] = $admin['id_admin'];
                $_SESSION['nama_admin'] = $admin['nama_admin'];
                $_SESSION['role'] = 'admin';
                header("Location: admin/dashboard.php");
                exit;
            } else {
                $error = "Password admin salah!";
            }
        } else {
            $error = "Akun tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DAF Villa</title>
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

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 360px;
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

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #5F9EA0;
            border-radius: 5px;
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

        .register {
            text-align: center;
            margin-top: 15px;
        }

        .register a {
            color: #5F9EA0;
            text-decoration: none;
            font-weight: 600;
        }

        .error {
            background: #5F9EA0;
            color: #5F9EA0;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-box">
            <h2>Login Akun</h2>
            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>


            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>

            <div class="register">
                <p>Belum punya akun? <a href="register.php">Daftar Sekarang</a></p>
            </div>
        </div>
    </div>
</body>

</html>