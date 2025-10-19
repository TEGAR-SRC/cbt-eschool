<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="#">
                    <span class="align-middle"><?= htmlspecialchars($pengaturan['nama_aplikasi'] ?? 'EduPus') ?></span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Menu Utama</li>

                    <li class="sidebar-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="dashboard.php">
                        <i class="align-middle fas fa-tachometer-alt"></i> <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'soal.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_soal.php') ? 'active' : '' ?>  
                        <?= ($currentPage == 'tambah_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_butir_soal.php') ? 'active' : '' ?> 
                        <?= ($currentPage == 'tambah_butir_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'preview_soal.php') ? 'active' : '' ?> 
                        <?= ($currentPage == 'daftar_butir_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'upload-gambar.php') ? 'active' : '' ?> 
                        <?= ($currentPage == 'kartu_siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'daftar_hadir.php') ? 'active' : '' ?>
                        <?= ($currentPage == 'berita_acara.php') ? 'active' : '' ?>">
                    <a data-bs-toggle="collapse" href="#soal" class="sidebar-link collapsed">
                        <i class="align-middle fa fa-file"></i> <span class="align-middle">Manajemen Ujian </span><i class="fa fa-chevron-down ms-auto float-end"></i>
                    </a>
                    <ul id="soal" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="soal.php">
                                <i class="align-middle fas fa-book"></i> <span class="align-middle">Bank Soal</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="upload-gambar.php">
                                <i class="align-middle fas fa-upload"></i> <span class="align-middle">Upload Gambar</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="kartu_siswa.php">
                                <i class="align-middle fa fa-id-card"></i> <span class="align-middle">Cetak Kartu Ujian</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="daftar_hadir.php">
                                <i class="align-middle fa fa-print"></i> <span class="align-middle">Cetak Daftar Hadir</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="berita_acara.php">
                                <i class="align-middle fa fa-print"></i> <span class="align-middle">Cetak Berita Acara</span>
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'tambah_siswa.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="siswa.php">
                            <i class="align-middle fas fa-user"></i> <span class="align-middle">Manajemen Siswa</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'admin_management.php') ? 'active' : '' ?> <?= ($currentPage == 'tambah_admin.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_admin.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="admin_management.php">
                            <i class="align-middle fas fa-user-shield"></i> <span class="align-middle">Manajemen Admin</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'monitor.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="monitor.php">
                            <i class="align-middle fas fa-laptop"></i> <span class="align-middle">Monitoring Ujian</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-item <?= ($currentPage == 'reset_login.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="reset_login.php">
                            <i class="align-middle fas fa-redo"></i> <span class="align-middle">Reset Login</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'hasil.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="hasil.php">
                            <i class="align-middle fas fa-chart-line"></i> <span class="align-middle">Hasil Ujian</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'online.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="online.php">
                            <i class="align-middle fas fa-chalkboard-teacher"></i> <span class="align-middle">Who's Online</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'faq.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="faq.php">
                            <i class="align-middle fa-solid fa-robot"></i> <span class="align-middle">FAQ</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'chatbox_siswa.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="chatbox_siswa.php">
                            <i class="align-middle fas fa-comment"></i> <span class="align-middle">ChatBox</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'leaderboard.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="leaderboard.php">
                        <i class="fa fa-gamepad" aria-hidden="true"></i> <span class="align-middle">Mini Games</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'backup.php') ? 'active' : '' ?> <?= ($currentPage == 'reset_database.php') ? 'active' : '' ?> <?= ($currentPage == 'backup_gbr.php') ? 'active' : '' ?>">
                    <a data-bs-toggle="collapse" href="#backup" class="sidebar-link collapsed">
                        <i class="align-middle fa fa-hdd"></i> <span class="align-middle">Backup </span><i class="fa fa-chevron-down ms-auto float-end"></i>
                    </a>
                    <ul id="backup" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="backup.php">
                                <i class="align-middle fa fa-database"></i> <span class="align-middle">Backup Database</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="backup_gbr.php">
                                <i class="align-middle fa fa-download"></i> <span class="align-middle">Backup Gambar Soal</span>
                            </a>
                        </li>
                        <li class="sidebar-item submenu">
                            <a class="sidebar-link" href="reset_database.php">
                                <i class="align-middle fa-regular fa-floppy-disk"></i> <span class="align-middle">Reset Database</span>
                            </a>
                        </li>
                    </ul>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'log.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="log.php">
                            <i class="align-middle fas fa-history"></i> <span class="align-middle">Log Activity</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'setting.php') ? 'active' : '' ?> <?= ($currentPage == 'pass.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="setting.php">
                            <i class="align-middle fas fa-cogs"></i> <span class="align-middle">Pengaturan</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link btnLogout" href="logout.php">
                            <i class="align-middle fas fa-sign-out-alt"></i> <span class="align-middle">Logout</span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>