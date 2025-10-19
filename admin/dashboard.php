<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
// Cek jika sudah login
check_login('admin');
include '../inc/dataadmin.php';

// Ambil data statistik dari database
$total_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM soal"))['total'];
$total_ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM nilai"))['total'];

// Statistik tambahan
$siswa_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM siswa WHERE status = 'Aktif'"))['total'];
$ujian_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM nilai WHERE DATE(tanggal_ujian) = CURDATE()"))['total'];
$rata_nilai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT ROUND(AVG(nilai + IFNULL(nilai_uraian, 0)), 2) AS rata FROM nilai"))['rata'] ?? 0;
$nilai_tertinggi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT MAX(nilai + IFNULL(nilai_uraian, 0)) AS tertinggi FROM nilai"))['tertinggi'] ?? 0;
$ujian_bulan_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM nilai WHERE MONTH(tanggal_ujian) = MONTH(CURDATE()) AND YEAR(tanggal_ujian) = YEAR(CURDATE())"))['total'];
$soal_terbaru = 0; // Sementara diset 0 karena kolom tanggal tidak tersedia

// Ambil data jumlah siswa ikut ujian per bulan
$rekap_query = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tanggal_ujian, '%Y-%m') AS bulan, COUNT(*) AS jumlah 
    FROM nilai 
    GROUP BY bulan 
    ORDER BY bulan ASC
");

$rekap_data = [];
while ($row = mysqli_fetch_assoc($rekap_query)) {
    $rekap_data['labels'][] = date('M Y', strtotime($row['bulan'] . '-01'));
    $rekap_data['jumlah'][] = $row['jumlah'];
}
// Ambil 10 kode soal dengan rata-rata tertinggi
$kode_soal_query = mysqli_query($koneksi, "
    SELECT kode_soal, ROUND(AVG(nilai + IFNULL(nilai_uraian, 0)), 2) AS rata_rata 
    FROM nilai 
    GROUP BY kode_soal 
    ORDER BY rata_rata DESC 
    LIMIT 10
");

$kode_soal_data = ['labels' => [], 'rata' => []];
while ($row = mysqli_fetch_assoc($kode_soal_query)) {
    $kode_soal_data['labels'][] = $row['kode_soal'];
    $kode_soal_data['rata'][] = $row['rata_rata'];
}

// Ambil 10 siswa dengan rata-rata nilai akhir tertinggi
$top_siswa_query = mysqli_query($koneksi, "
    SELECT siswa.nama_siswa AS nama, 
           COUNT(*) AS jumlah_ujian,
           ROUND(AVG(nilai + IFNULL(nilai_uraian, 0)), 2) AS rata 
    FROM nilai 
    JOIN siswa ON nilai.id_siswa = siswa.id_siswa 
    GROUP BY nilai.id_siswa 
    ORDER BY rata DESC 
    LIMIT 10
") or die("Query error: " . mysqli_error($koneksi));

$top_siswa_data = ['labels' => [], 'rata' => [], 'ujian' => []];
while ($row = mysqli_fetch_assoc($top_siswa_query)) {
    $top_siswa_data['labels'][] = $row['nama'];
    $top_siswa_data['rata'][] = $row['rata'];
    $top_siswa_data['ujian'][] = $row['jumlah_ujian'];
}

// Data aktivitas harian 7 hari terakhir
$aktivitas_harian_query = mysqli_query($koneksi, "
    SELECT DATE(tanggal_ujian) AS tanggal, COUNT(*) AS jumlah 
    FROM nilai 
    WHERE tanggal_ujian >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal_ujian) 
    ORDER BY tanggal ASC
");

$aktivitas_data = ['labels' => [], 'jumlah' => []];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $aktivitas_data['labels'][] = date('d M', strtotime($tanggal));
    $aktivitas_data['jumlah'][] = 0; // Default 0
}

while ($row = mysqli_fetch_assoc($aktivitas_harian_query)) {
    $index = array_search(date('d M', strtotime($row['tanggal'])), $aktivitas_data['labels']);
    if ($index !== false) {
        $aktivitas_data['jumlah'][$index] = $row['jumlah'];
    }
}

// Data distribusi nilai
$distribusi_nilai_query = mysqli_query($koneksi, "
    SELECT 
        CASE 
            WHEN (nilai + IFNULL(nilai_uraian, 0)) >= 90 THEN 'A (90-100)'
            WHEN (nilai + IFNULL(nilai_uraian, 0)) >= 80 THEN 'B (80-89)'
            WHEN (nilai + IFNULL(nilai_uraian, 0)) >= 70 THEN 'C (70-79)'
            WHEN (nilai + IFNULL(nilai_uraian, 0)) >= 60 THEN 'D (60-69)'
            ELSE 'E (<60)'
        END AS grade,
        COUNT(*) AS jumlah
    FROM nilai 
    GROUP BY grade 
    ORDER BY grade
");

$distribusi_data = ['labels' => [], 'jumlah' => []];
while ($row = mysqli_fetch_assoc($distribusi_nilai_query)) {
    $distribusi_data['labels'][] = $row['grade'];
    $distribusi_data['jumlah'][] = $row['jumlah'];
}
$game = $_GET['game'] ?? 'math_puzzle';
$game2 = $_GET['game'] ?? 'scramble';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php include '../inc/css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>
            <!-- Content -->
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        


                                        <!-- Statistik Siswa -->
                                         <div class="col-md-4">
                                            <div class="card shadow border-secondary border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-dark fw-bold mb-2">Jumlah Siswa</h5>
                                                            <p class="card-text mb-1"><?php echo $total_siswa; ?> siswa terdaftar</p>
                                                            <a href="tambah_siswa.php" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-plus"></i> Tambah Siswa
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Icon besar di belakang, terpotong oleh card -->
                                                <i class="fa fa-user-circle position-absolute text-secondary" 
                                                style="font-size: 120px; bottom: -30px; right: -30px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>


                                        <!-- Statistik Soal -->
                                         <div class="col-md-4">
                                            <div class="card shadow border-secondary border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-dark fw-bold mb-2">Jumlah Soal
                                                            </h5>
                                                            <p class="card-text mb-1"><?php echo $total_soal; ?> soal tersedia
                                                            </p>
                                                            <a href="tambah_soal.php" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-plus"></i> Tambah Soal
                                                            </a>
                                                        </div>
                                                        <div class="ms-3">
                                                            <i class="fa fa-pen-to-square position-absolute text-secondary" 
                                                            style="font-size: 120px; bottom: -30px; right: -30px; opacity: 0.1; z-index: 0;"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Statistik Ujian -->
                                         <div class="col-md-4">
                                            <div class="card shadow border-secondary border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-dark fw-bold mb-2">Ujian
                                                            </h5>
                                                            <p class="card-text mb-1"><?php echo $total_ujian; ?> Siswa Selesai
                                                            </p>
                                                            <a href="hasil.php" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-square-check"></i> Lihat Nilai
                                                            </a>
                                                        </div>
                                                        <div class="ms-3">
                                                            <i class="fa fa-address-card position-absolute text-secondary" 
                                                            style="font-size: 120px; bottom: -30px; right: -30px; opacity: 0.1; z-index: 0;"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Statistik Tambahan Row 1 -->
                                        <div class="col-md-3">
                                            <div class="card shadow border-success border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-success fw-bold mb-2">Siswa Aktif</h5>
                                                            <p class="card-text mb-1"><?php echo $siswa_aktif; ?> siswa aktif</p>
                                                            <small class="text-muted">Dari <?php echo $total_siswa; ?> total siswa</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-user-check position-absolute text-success" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="card shadow border-info border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-info fw-bold mb-2">Ujian Hari Ini</h5>
                                                            <p class="card-text mb-1"><?php echo $ujian_hari_ini; ?> ujian selesai</p>
                                                            <small class="text-muted"><?php echo date('d M Y'); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-calendar-day position-absolute text-info" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="card shadow border-warning border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-warning fw-bold mb-2">Rata-rata Nilai</h5>
                                                            <p class="card-text mb-1"><?php echo $rata_nilai; ?> poin</p>
                                                            <small class="text-muted">Nilai tertinggi: <?php echo $nilai_tertinggi; ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-chart-line position-absolute text-warning" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="card shadow border-primary border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-primary fw-bold mb-2">Ujian Bulan Ini</h5>
                                                            <p class="card-text mb-1"><?php echo $ujian_bulan_ini; ?> ujian</p>
                                                            <small class="text-muted"><?php echo date('M Y'); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-calendar-alt position-absolute text-primary" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>

                                        <!-- Statistik Tambahan Row 2 -->
                                        <div class="col-md-6">
                                            <div class="card shadow border-danger border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-danger fw-bold mb-2">Soal Terbaru</h5>
                                                            <p class="card-text mb-1"><?php echo $soal_terbaru; ?> soal baru</p>
                                                            <small class="text-muted">7 hari terakhir</small>
                                                            <br>
                                                            <a href="soal.php" class="btn btn-sm btn-outline-danger mt-2">
                                                                <i class="fas fa-eye"></i> Lihat Soal
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-file-plus position-absolute text-danger" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card shadow border-dark border mb-3 position-relative overflow-hidden" style="height: 150px;">
                                                <div class="card-body position-relative z-1">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <h5 class="card-title text-dark fw-bold mb-2">Sistem Status</h5>
                                                            <p class="card-text mb-1">
                                                                <span class="badge bg-success">Online</span>
                                                                <span class="badge bg-info">Aktif</span>
                                                            </p>
                                                            <small class="text-muted">Server: <?php echo $_SERVER['SERVER_NAME']; ?></small>
                                                            <br>
                                                            <a href="setting.php" class="btn btn-sm btn-outline-dark mt-2">
                                                                <i class="fas fa-cog"></i> Pengaturan
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <i class="fa fa-server position-absolute text-dark" 
                                                style="font-size: 80px; bottom: -20px; right: -20px; opacity: 0.1; z-index: 0;"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Chart Statistik -->
                                        <div class="col-lg-4 md-4">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">10 Siswa Nilai Tertinggi</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chartTopSiswa"
                                                        style="height: 400px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 md-4">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Rekap Peserta Ujian</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chartRekapUjian"
                                                        style="height: 400px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 md-4">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Statistik Nilai</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chartKodeSoal"
                                                        style="height: 400px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chart Statistik Tambahan -->
                                        <div class="col-lg-6">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Aktivitas Ujian 7 Hari Terakhir</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chartAktivitasHarian"
                                                        style="height: 300px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">Distribusi Nilai</h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="chartDistribusiNilai"
                                                        style="height: 300px; width: 100%;"></canvas>
                                                </div>
                                            </div>
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
    <script src="../assets/js/chart.js"></script>
    <script>
    const ctx = document.getElementById('chartRekapUjian').getContext('2d');
    const chartRekapUjian = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($rekap_data['labels']); ?>,
            datasets: [{
                label: 'Jumlah Siswa',
                data: <?php echo json_encode($rekap_data['jumlah']); ?>,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4, // Semakin tinggi nilainya (0–1), semakin bergelombang
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart' // efek animasi gelombang halus
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
    // Grafik Statistik Nilai per Kode Soal
    const ctxKode = document.getElementById('chartKodeSoal').getContext('2d');

    // Buat gradient linear (dari kiri ke kanan)
    const gradientBlue = ctxKode.createLinearGradient(0, 0, 400, 0);
    gradientBlue.addColorStop(0, 'rgba(255, 0, 200, 0.6)');
    gradientBlue.addColorStop(1, 'rgba(0, 200, 255, 0.9)');
    const chartKodeSoal = new Chart(ctxKode, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($kode_soal_data['labels']); ?>,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: <?php echo json_encode($kode_soal_data['rata']); ?>,
                backgroundColor: gradientBlue,
                borderWidth: 0,
                borderRadius: 0, // lebih bulat ujung bar
                barThickness: 5 // bar tipis
            }]
        },
        options: {
            indexAxis: 'y', // horizontal
            responsive: true,
            animation: {
                duration: 1200,
                easing: 'easeOutCubic' // animasi smooth modern
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)' // grid halus
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false
                    },
                    grid: {
                        display: false // hilangkan garis grid Y
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // buang legend supaya clean
                }
            }
        }
    });

    // Grafik 10 Siswa dengan Rata-rata Nilai Tertinggi
    // Grafik 10 Siswa dengan Rata-rata Nilai Tertinggi (Doughnut Chart)
    const ctxTop = document.getElementById('chartTopSiswa').getContext('2d');
    const chartTopSiswa = new Chart(ctxTop, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($top_siswa_data['labels']); ?>,
            datasets: [{
                label: 'Rata-rata Nilai',
                data: <?php echo json_encode($top_siswa_data['rata']); ?>,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#00C49F', '#FF6666',
                    '#6699FF', '#FFCC99'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const nama = context.label;
                            const nilai = context.dataset.data[index];
                            const jumlahUjian = <?php echo json_encode($top_siswa_data['ujian']); ?>[index];
                            return `${nama}: ${nilai} (Ujian: ${jumlahUjian}x)`;
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Top 10 Siswa (Rata-rata Nilai)'
                }
            },
            animation: {
                animateRotate: true,
                duration: 1500
            }
        }
    });

    // Chart Aktivitas Harian 7 Hari Terakhir
    const ctxAktivitas = document.getElementById('chartAktivitasHarian').getContext('2d');
    const chartAktivitasHarian = new Chart(ctxAktivitas, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($aktivitas_data['labels']); ?>,
            datasets: [{
                label: 'Jumlah Ujian',
                data: <?php echo json_encode($aktivitas_data['jumlah']); ?>,
                backgroundColor: 'rgba(90, 225, 108, 0.6)',
                borderColor: 'rgba(90, 225, 108, 1)',
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Chart Distribusi Nilai
    const ctxDistribusi = document.getElementById('chartDistribusiNilai').getContext('2d');
    const chartDistribusiNilai = new Chart(ctxDistribusi, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($distribusi_data['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($distribusi_data['jumlah']); ?>,
                backgroundColor: [
                    '#28a745', // A - Hijau
                    '#17a2b8', // B - Biru
                    '#ffc107', // C - Kuning
                    '#fd7e14', // D - Orange
                    '#dc3545'  // E - Merah
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    </script>
</body>

</html>