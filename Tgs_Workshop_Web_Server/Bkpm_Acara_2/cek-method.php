<?php
// Tugas Mandiri: deteksi method request
$method = $_SERVER['REQUEST_METHOD'];
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Cek Method Request</title></head>
<body>
<?php if ($method === 'POST'): ?>
  <h2>Request ini menggunakan method POST</h2>
  <p>Halo, <?= htmlspecialchars($_POST['nama'] ?? '(kosong)') ?>! Data dikirim lewat body, tidak tampak di URL.</p>
<?php else: ?>
  <h2>Request ini menggunakan method GET</h2>
  <p>Halaman dibuka langsung / data dikirim lewat URL. Isi form di bawah untuk mencoba POST.</p>
<?php endif; ?>
  <form action="cek-method.php" method="POST">
    <input type="text" name="nama" placeholder="Nama">
    <button type="submit">Kirim (POST)</button>
  </form>
  <p><a href="cek-method.php">Kirim ulang sebagai GET</a></p>
</body>
</html>
