<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

require_once '../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$error = '';
$success = '';
$imported_count = 0;
$failed_count = 0;
$duplicate_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_word'])) {
    $allowed_types = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword'
    ];

    $file_type = $_FILES['file_word']['type'];
    $file_name = $_FILES['file_word']['name'];
    $tmp_file = $_FILES['file_word']['tmp_name'];
    $kode_soal = trim($_POST['kode_soal']);

    if (empty($kode_soal)) {
        $error = 'Kode soal harus diisi!';
    } elseif (!in_array($file_type, $allowed_types)) {
        $error = 'Format file tidak valid! Hanya file Word (.docx, .doc) yang diperbolehkan.';
    } else {
        try {
            // Load Word document
            $phpWord = IOFactory::load($tmp_file);
            $sections = $phpWord->getSections();
            
            $questions = [];
            $current_question = null;
            $question_number = 0;

            // Extract text from all sections
            foreach ($sections as $section) {
                $elements = $section->getElements();
                
                foreach ($elements as $element) {
                    if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                        $text = '';
                        foreach ($element->getElements() as $textElement) {
                            if (get_class($textElement) === 'PhpOffice\PhpWord\Element\Text') {
                                $text .= $textElement->getText();
                            }
                        }
                        
                        // Parse questions based on patterns
                        $lines = explode("\n", $text);
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;

                            // Detect question number (1., 2., etc.)
                            if (preg_match('/^(\d+)\.\s*(.+)$/', $line, $matches)) {
                                if ($current_question) {
                                    $questions[] = $current_question;
                                }
                                $question_number = intval($matches[1]);
                                $current_question = [
                                    'nomer_soal' => $question_number,
                                    'kode_soal' => $kode_soal,
                                    'pertanyaan' => $matches[2],
                                    'tipe_soal' => 'Pilihan Ganda',
                                    'pilihan_1' => '',
                                    'pilihan_2' => '',
                                    'pilihan_3' => '',
                                    'pilihan_4' => '',
                                    'jawaban_benar' => '',
                                    'status_soal' => 'Aktif'
                                ];
                            }
                            // Detect options (A., B., C., D.)
                            elseif (preg_match('/^([A-D])\.\s*(.+)$/', $line, $matches)) {
                                if ($current_question) {
                                    $option_key = 'pilihan_' . (ord($matches[1]) - ord('A') + 1);
                                    $current_question[$option_key] = $matches[2];
                                }
                            }
                            // Detect correct answer (Jawaban: A, Kunci: B, etc.)
                            elseif (preg_match('/^(Jawaban|Kunci|Answer):\s*([A-D])/i', $line, $matches)) {
                                if ($current_question) {
                                    $answer_letter = $matches[2];
                                    $answer_number = ord($answer_letter) - ord('A') + 1;
                                    $current_question['jawaban_benar'] = $answer_number;
                                }
                            }
                            // If line doesn't match patterns, add to question text
                            elseif ($current_question && !preg_match('/^[A-D]\./', $line)) {
                                $current_question['pertanyaan'] .= ' ' . $line;
                            }
                        }
                    }
                }
            }

            // Add last question
            if ($current_question) {
                $questions[] = $current_question;
            }

            if (empty($questions)) {
                $error = 'Tidak ada soal yang ditemukan dalam dokumen Word. Pastikan format dokumen sesuai template.';
            } else {
                // Insert questions to database
                foreach ($questions as $question) {
                    // Check for duplicates
                    $check_query = "SELECT COUNT(*) FROM butir_soal WHERE nomer_soal = ? AND kode_soal = ?";
                    $check_stmt = mysqli_prepare($koneksi, $check_query);
                    mysqli_stmt_bind_param($check_stmt, 'is', $question['nomer_soal'], $question['kode_soal']);
                    mysqli_stmt_execute($check_stmt);
                    $check_result = mysqli_stmt_get_result($check_stmt);
                    $count = mysqli_fetch_row($check_result)[0];
                    mysqli_stmt_close($check_stmt);

                    if ($count > 0) {
                        $duplicate_count++;
                        continue;
                    }

                    // Insert question
                    $insert_query = "INSERT INTO butir_soal (nomer_soal, kode_soal, pertanyaan, tipe_soal, pilihan_1, pilihan_2, pilihan_3, pilihan_4, jawaban_benar, status_soal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $insert_stmt = mysqli_prepare($koneksi, $insert_query);
                    mysqli_stmt_bind_param($insert_stmt, 'isssssssss', 
                        $question['nomer_soal'], 
                        $question['kode_soal'], 
                        $question['pertanyaan'], 
                        $question['tipe_soal'], 
                        $question['pilihan_1'], 
                        $question['pilihan_2'], 
                        $question['pilihan_3'], 
                        $question['pilihan_4'], 
                        $question['jawaban_benar'], 
                        $question['status_soal']
                    );

                    if (mysqli_stmt_execute($insert_stmt)) {
                        $imported_count++;
                    } else {
                        $failed_count++;
                    }
                    mysqli_stmt_close($insert_stmt);
                }

                if ($imported_count > 0) {
                    $success = "Berhasil mengimpor {$imported_count} soal dari dokumen Word!";
                    if ($duplicate_count > 0) {
                        $success .= " ({$duplicate_count} soal duplikat dilewati)";
                    }
                    if ($failed_count > 0) {
                        $success .= " ({$failed_count} soal gagal diimpor)";
                    }
                } else {
                    $error = "Tidak ada soal yang berhasil diimpor. Periksa format dokumen Word.";
                }
            }

        } catch (Exception $e) {
            $error = "Terjadi kesalahan saat membaca file Word: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal dari Word</title>
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
                                    <h5 class="card-title mb-0">Import Soal dari Word</h5>
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
                                        <a href="daftar_butir_soal.php?kode_soal=<?php echo urlencode($kode_soal); ?>" class="btn btn-sm btn-outline-success ms-2">
                                            <i class="fas fa-list"></i> Lihat Soal
                                        </a>
                                    </div>
                                    <?php endif; ?>

                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="kode_soal" class="form-label">Kode Soal <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="kode_soal" name="kode_soal" 
                                                           value="<?php echo htmlspecialchars($_POST['kode_soal'] ?? ''); ?>" required>
                                                    <div class="form-text">Masukkan kode soal yang akan digunakan untuk semua soal dalam dokumen.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="file_word" class="form-label">File Word <span class="text-danger">*</span></label>
                                                    <input type="file" class="form-control" id="file_word" name="file_word" 
                                                           accept=".docx,.doc" required>
                                                    <div class="form-text">Hanya file Word (.docx, .doc) yang diperbolehkan.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <h6><i class="fas fa-info-circle"></i> Format Dokumen Word yang Diperlukan:</h6>
                                                    <ul class="mb-0">
                                                        <li><strong>Nomor Soal:</strong> 1., 2., 3., dst.</li>
                                                        <li><strong>Pertanyaan:</strong> Teks pertanyaan setelah nomor</li>
                                                        <li><strong>Pilihan:</strong> A., B., C., D. (opsi jawaban)</li>
                                                        <li><strong>Jawaban:</strong> Jawaban: A, Kunci: B, atau Answer: C</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-warning">
                                                    <h6><i class="fas fa-exclamation-triangle"></i> Contoh Format:</h6>
                                                    <pre class="mb-0">
1. Berapa hasil dari 2 + 2?
A. 3
B. 4
C. 5
D. 6
Jawaban: B

2. Apa ibukota Indonesia?
A. Jakarta
B. Bandung
C. Surabaya
D. Medan
Kunci: A
                                                    </pre>
                                                    <div class="mt-2">
                                                        <a href="../assets/template_import_soal.docx" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download"></i> Download Template Word
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-upload"></i> Import Soal
                                                </button>
                                                <a href="daftar_butir_soal.php" class="btn btn-secondary">
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
</body>
</html>
