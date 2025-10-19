<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
// Cek jika sudah login
check_login('admin');
include '../inc/dataadmin.php';

$error = '';
$success = '';
$admin_id = (int)($_GET['id'] ?? 0);

if (!$admin_id) {
    header("Location: admin_management.php");
    exit;
}

// Ambil data admin
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$admin) {
    header("Location: admin_management.php");
    exit;
}

$isCurrentUser = ($admin_id == $_SESSION['admin_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($username) || empty($nama)) {
        $error = 'Username dan nama harus diisi!';
    } else {
        // Cek apakah username sudah ada (kecuali admin ini)
        $check_query = "SELECT id FROM admins WHERE username = ? AND id != ?";
        $check_stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($check_stmt, 'si', $username, $admin_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            // Update data admin
            if (!empty($password)) {
                // Jika password diisi, validasi dan update password
                if (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter!';
                } elseif ($password !== $confirm_password) {
                    $error = 'Konfirmasi password tidak cocok!';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE admins SET username = ?, nama = ?, password = ? WHERE id = ?";
                    $update_stmt = mysqli_prepare($koneksi, $update_query);
                    mysqli_stmt_bind_param($update_stmt, 'sssi', $username, $nama, $hashed_password, $admin_id);
                }
            } else {
                // Jika password kosong, update tanpa password
                $update_query = "UPDATE admins SET username = ?, nama = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($update_stmt, 'ssi', $username, $nama, $admin_id);
            }
            
            if (!isset($error) || empty($error)) {
                if (mysqli_stmt_execute($update_stmt)) {
                    $success = 'Data admin berhasil diperbarui!';
                    // Update data admin yang sedang login jika mengedit diri sendiri
                    if ($isCurrentUser) {
                        $_SESSION['admin_username'] = $username;
                        $_SESSION['admin_nama'] = $nama;
                    }
                } else {
                    $error = 'Gagal memperbarui data admin: ' . mysqli_error($koneksi);
                }
                mysqli_stmt_close($update_stmt);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}

// Ambil data admin terbaru
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $admin_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <?php include '../inc/css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>
            
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Edit Admin: <?php echo htmlspecialchars($admin['username']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($error): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($success): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                        <a href="admin_management.php" class="btn btn-sm btn-outline-success ms-2">
                                            <i class="fas fa-list"></i> Lihat Daftar Admin
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <form method="POST" action="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="username" name="username" 
                                                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                                    <div class="form-text">Username harus unik dan tidak boleh sama dengan admin lain.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama" name="nama" 
                                                           value="<?php echo htmlspecialchars($admin['nama'] ?? $admin['nama_admin'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">Password Baru</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="password" name="password">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                            <i class="fas fa-eye" id="password-icon"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                            <i class="fas fa-eye" id="confirm_password-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Informasi:</strong> 
                                                    <?php if ($isCurrentUser): ?>
                                                    Anda sedang mengedit akun sendiri. Perubahan akan langsung berlaku.
                                                    <?php else: ?>
                                                    Mengedit data admin lain. Pastikan data yang diisi sudah benar.
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Simpan Perubahan
                                                </button>
                                                <a href="admin_management.php" class="btn btn-secondary">
                                                    <i class="fas fa-arrow-left"></i> Kembali
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <?php include '../inc/js.php'; ?>
    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Validasi konfirmasi password
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Password tidak cocok!');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>
