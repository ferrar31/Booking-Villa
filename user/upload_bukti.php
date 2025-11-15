<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['upload'])) {
  $id_transaksi = $_POST['id_transaksi'];
  $namaFile = $_FILES['bukti']['name'];
  $tmpName = $_FILES['bukti']['tmp_name'];

  $folder = "../assets/bukti/";
  if (!is_dir($folder)) mkdir($folder, 0777, true);

  $newName = time() . "_" . $namaFile;
  move_uploaded_file($tmpName, $folder . $newName);

  mysqli_query($conn, "UPDATE transaksi SET bukti_pembayaran='$newName' WHERE id_transaksi='$id_transaksi'");

  echo "<script>alert('Bukti pembayaran berhasil diunggah!'); window.location='dashboard.php';</script>";
  exit;
} else {
  header('Location: dashboard.php');
}
