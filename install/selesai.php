<?php
session_start();

// Jika sudah terinstal, alihkan ke error
if (file_exists(__DIR__ . '/../koneksi/koneksi.php')) {
    header('Location: error.php');
    exit;
}

// Hapus session data
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Instalasi Selesai - EduPus</title>
  <link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/images/icon.png" />
  <style>
    body {
      background: #f8f9fa;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .success-container {
      background: white;
      padding: 2rem 3rem;
      border-radius: 1rem;
      box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 500px;
    }
    .success-container img {
      width: 80px;
      margin-bottom: 20px;
    }
    .success-container h3 {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      color: #28a745;
    }
    .success-container p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 1.5rem;
    }
    .btn {
      margin: 10px;
      padding: 12px 24px;
      font-weight: 600;
    }
    .alert {
      margin-top: 20px;
      text-align: left;
    }
  </style>
</head>
<body>
  <div class="success-container">
    <img src="../assets/images/codelite.png" alt="EduPus Logo">
    <h3>🎉 Instalasi Berhasil!</h3>
    <p>Sistem <strong>EduPus</strong> telah berhasil diinstal.</p>
    
    <div class="alert alert-warning">
      <strong>⚠️ Penting:</strong> Untuk keamanan, sebaiknya hapus folder <code>/install</code> setelah instalasi selesai.
    </div>
    
    <div class="d-grid gap-2">
      <a href="../admin/login.php" class="btn btn-primary">
        <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Admin Panel
      </a>
      <a href="../" class="btn btn-outline-secondary">
        <i class="fas fa-home me-2"></i>Kembali ke Halaman Utama
      </a>
    </div>
  </div>
</body>
</html>
