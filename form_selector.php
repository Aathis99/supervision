<?php
// ไฟล์: form_selector.php
session_start(); // เริ่ม Session เพื่อเก็บข้อมูล

// 1. รับข้อมูลจาก summary.php แล้วเก็บลง SESSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inspection_data'])) {
    // เก็บข้อมูลทั้งหมดที่ส่งมาจาก summary.php ลงใน Session ชื่อ 'supervision_data'
    $_SESSION['supervision_data'] = $_POST;
}

// 2. ตรวจสอบการกดปุ่มบันทึกการเลือกแบบฟอร์ม
if (isset($_POST['submit_selection'])) {

    if (isset($_POST['evaluation_type'])) {
        $selected_form = $_POST['evaluation_type'];
        $target_page = '';

        if ($selected_form === 'kpi_form') {
            $target_page = 'kpi_form.php';
        } elseif ($selected_form === 'form_2') {
            $target_page = 'form_2.php';
        }

        if ($target_page !== '') {
            header("Location: " . $target_page);
            exit();
        } else {
            $error_message = "ค่าแบบฟอร์มไม่ถูกต้อง";
        }
    } else {
        $error_message = "กรุณาเลือกแบบฟอร์มก่อน";
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกแบบฟอร์ม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color: #feee91; padding: 50px;">
    <div class="container" style="max-width: 600px; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

        <?php if (isset($error_message)) echo '<div class="alert alert-danger">' . $error_message . '</div>'; ?>

        <?php if (isset($_SESSION['supervision_data'])): ?>
            <div class="alert alert-info">
                <small>กำลังทำรายการให้: <?php echo htmlspecialchars($_SESSION['supervision_data']['teacher_name'] ?? ''); ?></small>
            </div>
        <?php endif; ?>

        <form method="POST" action="form_selector.php">
            <h5 class="mb-4">โปรดเลือกแบบฟอร์มสำหรับการประเมิน</h5>

            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="evaluation_type" id="form1" value="kpi_form">
                <label class="form-check-label" for="form1">
                    แบบบันทึกการจัดการเรียนรู้และการจัดการชั้นเรียน
                </label>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="radio" name="evaluation_type" id="form2" value="form_2" checked>
                <label class="form-check-label" for="form2">
                    แบบกรอกข้อมูลนิทเทศตามนโยบายและจุดเน้น
                </label>
            </div>

            <button type="submit" name="submit_selection" class="btn btn-success w-100">
                เริ่มทำแบบประเมิน
            </button>
        </form>
    </div>
</body>

</html>