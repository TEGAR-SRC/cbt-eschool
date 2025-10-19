<?php
// Memulai session jika dibutuhkan
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Error Page</title>
  <link rel="icon" type="image/png" href="assets/images/icon.png" />
  <!-- Bootstrap 5 CSS -->
  <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
  <link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
        background: #f8f9fa;
        color: #333;
    }

    .container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quote-box {
        text-align: center;
        font-size: 1.5rem;
        font-style: italic;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
            <div class="wrapper">
                <div class="container p-0">
                    <div class="row">
                            <div class="card">
                                <center><img src="assets/images/codelite.png" width="150" height="auto"></center><br>
                            </div>
                            <div class="quote-box">
                                <p><b>"Jangan menghapus footer, karena setiap bagian dari aplikasi memiliki peran yang penting, bahkan yang tampaknya kecil sekalipun!"</b></p>
                                <p style="font-size:18px;">--EduPus--</p>
                            </div>
                    </div>
                </div>
            </div>

  <!-- JavaScript -->
  <script src="../assets/bootstrap-5.3.6/js/bootstrap.bundle.min.js"></script>
</body>
</html>
