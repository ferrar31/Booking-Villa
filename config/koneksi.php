<?php
$conn = new mysqli("localhost", "root", "", "booking_villa");
if ($conn->connect_error) {
		die('Connection failed: ' . $conn->connect_error);
}

// Set villa to 'tersedia' when there's no active booking for today
$updateAvailable = "
UPDATE detail_villa dv
SET dv.status = 'tersedia'
WHERE dv.status = 'dibooking'
	AND NOT EXISTS (
		SELECT 1 FROM transaksi t
		WHERE t.id_villa = dv.id_villa
			AND t.tanggal_checkin <= CURDATE()
			AND t.tanggal_checkout >= CURDATE()
			AND t.status <> 'batal'
	)";
$conn->query($updateAvailable);

// Ensure villas with an active booking today are marked 'dibooking'
$updateBooked = "
UPDATE detail_villa dv
SET dv.status = 'dibooking'
WHERE dv.status <> 'dibooking'
	AND EXISTS (
		SELECT 1 FROM transaksi t
		WHERE t.id_villa = dv.id_villa
			AND t.tanggal_checkin <= CURDATE()
			AND t.tanggal_checkout >= CURDATE()
			AND t.status <> 'batal'
	)";
$conn->query($updateBooked);

// Optionally mark past transactions as 'selesai' (skip if already 'batal' or 'selesai')
$conn->query("UPDATE transaksi SET status = 'selesai' WHERE tanggal_checkout < CURDATE() AND status NOT IN ('batal','selesai')");
?>

