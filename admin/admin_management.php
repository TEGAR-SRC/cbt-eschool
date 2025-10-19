<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
// Cek jika sudah login
check_login('admin');
include '../inc/dataadmin.php';

// Proses hapus admin
if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Jangan biarkan hapus admin yang sedang login
    if ($id == $_SESSION['admin_id']) {
        echo "<script>alert('Tidak dapat menghapus akun yang sedang aktif!'); window.location.href='admin_management.php';</script>";
        exit;
    }
    
    $query = "DELETE FROM admins WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Admin berhasil dihapus!'); window.location.href='admin_management.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus admin!'); window.location.href='admin_management.php';</script>";
    }
    mysqli_stmt_close($stmt);
    exit;
}

// Ambil data admin
$query = "SELECT * FROM admins ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Admin</title>
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
                                    <h5 class="card-title mb-0">Manajemen Admin</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <a href="tambah_admin.php" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah Admin Baru
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <span class="badge bg-info">Total Admin: <?php echo mysqli_num_rows($result); ?></span>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Username</th>
                                                    <th>Nama Admin</th>
                                                    <th>Status</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                mysqli_data_seek($result, 0);
                                                while ($admin = mysqli_fetch_assoc($result)):
                                                    $isCurrentUser = ($admin['id'] == $_SESSION['admin_id']);
                                                ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($admin['username']); ?>
                                                        <?php if ($isCurrentUser): ?>
                                                            <span class="badge bg-success ms-1">Anda</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($admin['nama'] ?? $admin['nama_admin'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <span class="badge bg-success">Aktif</span>
                                                    </td>
                                                    <td><?php echo date('d M Y', strtotime($admin['created_at'] ?? 'now')); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="edit_admin.php?id=<?php echo $admin['id']; ?>" 
                                                               class="btn btn-sm btn-warning" title="Edit Admin">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <?php if (!$isCurrentUser): ?>
                                                            <a href="admin_management.php?hapus=1&id=<?php echo $admin['id']; ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Yakin ingin menghapus admin ini?')" 
                                                               title="Hapus Admin">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            <?php else: ?>
                                                            <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat menghapus akun sendiri">
                                                                <i class="fas fa-lock"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <?php include '../inc/js.php'; ?>
</body>
</html>
