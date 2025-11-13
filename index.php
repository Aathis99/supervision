<?php
// ไฟล์: index.php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ระบบนิเทศการสอน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container mt-4">
        <form action="summary.php" method="POST">

            <?php
            // 2. ส่วนเลือกข้อมูลผู้นิเทศ
            require_once 'supervisor.php';

            // 3. ส่วนเลือกข้อมูลผู้รับนิเทศ
            require_once 'teacher.php';
            ?>

            <div class="row mt-4 mb-5">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-arrow-right"></i> ตรวจสอบข้อมูลและลงวันที่
                    </button>
                </div>
            </div>

        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>