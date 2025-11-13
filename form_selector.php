<?php
// ไฟล์: form_selector.php (ฉบับแก้ไข PRG และลบค่าเริ่มต้น)
session_start(); 

// ตัวแปรสำหรับเก็บข้อความเตือน
$error_message = '';

// ----------------------------------------------------------------
// 1. ตรวจสอบการส่งข้อมูลแบบฟอร์ม (POST)
// ----------------------------------------------------------------

// A) ตรวจสอบว่าเป็นการส่งข้อมูลมาจาก summary.php (กดปุ่ม "ดำเนินการเลือกแบบประเมินต่อ")
if (isset($_POST['submit_inspection_data'])) {
    
    // ⭐️ บันทึกข้อมูลทั้งหมด (รวมถึงผู้นิเทศ/ผู้รับนิเทศ) ลงใน Session ⭐️
    $_SESSION['inspection_data'] = $_POST;
    
    // ⭐️ PRG FIX: เพิ่ม Redirect ตัวเองเพื่อเคลียร์ประวัติ POST ⭐️
    header("Location: form_selector.php");
    exit(); 
} 
// B) ตรวจสอบว่าเป็นการส่งข้อมูลมาจาก form_selector.php เอง (กดปุ่ม "ดำเนินการเลือกแบบฟอร์ม")
else if (isset($_POST['submit_selection'])) {
    
    if (isset($_POST['evaluation_type'])) {
        
        $selected_form = $_POST['evaluation_type'];
        $target_page = '';
        
        if ($selected_form === 'kpi_form') {
            $target_page = 'kpi_form.php'; 
        } elseif ($selected_form === 'form_2') {
            $target_page = 'form_2.php';
        }
        
        // ทำการเปลี่ยนหน้า (Redirect) ไปยังไฟล์ปลายทาง
        if ($target_page !== '') {
            // ตรวจสอบข้อมูลใน session ก่อนไปหน้า kpi_form/form_2
            if (isset($_SESSION['inspection_data'])) {
                header("Location: " . $target_page);
                exit(); 
            } else {
                 $error_message = 'ไม่พบข้อมูลการนิเทศ กรุณากลับไปหน้าสรุปข้อมูล';
            }
        } else {
            $error_message = 'เกิดข้อผิดพลาดในการกำหนดแบบฟอร์มปลายทาง';
        }
    } else {
        $error_message = 'กรุณาเลือกแบบฟอร์มประเมินก่อนดำเนินการต่อ';
    }
}
// ----------------------------------------------------------------
// ตรวจสอบข้อมูลใน Session และแสดงผล (สำหรับเมธอด GET)
// ----------------------------------------------------------------
// ถ้ามีการส่ง POST มาก่อนหน้านี้ ข้อมูลจะถูกเก็บใน Session
$inspection_data = $_SESSION['inspection_data'] ?? null;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เลือกแบบฟอร์ม</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #feee91; padding: 50px;">
    <div class="container" style="max-width: 600px; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        
        <?php 
        // แสดงข้อความเตือนถ้ามี
        if (isset($error_message) && $error_message !== '') {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
        }
        
        // ตรวจสอบว่ามีข้อมูลการนิเทศหรือไม่ ถ้าไม่มีไม่ควรให้ดำเนินการต่อ
        if (!$inspection_data):
        ?>
            <div class="alert alert-warning" role="alert">
                ไม่พบข้อมูลการนิเทศ กรุณา <a href="summary.php" class="alert-link">กลับไปหน้าสรุปข้อมูล</a> เพื่อเริ่มต้นใหม่
            </div>
        <?php else: ?>
        <form method="POST" action="form_selector.php">
            <h5 class="mb-4">โปรดเลือกแบบฟอร์มสำหรับการประเมิน</h5>

            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="evaluation_type" id="form1" value="kpi_form" required>
                <label class="form-check-label" for="form1">
                    แบบบันทึกการจัดการเรียนรู้และการจัดการชั้นเรียน
                </label>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="radio" name="evaluation_type" id="form2" value="form_2" required>
                <label class="form-check-label" for="form2">
                    แบบกรอกข้อมูลนิทเทศตามนโยบาย
                </label>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" name="submit_selection" class="btn btn-success">
                    ดำเนินการเลือกแบบฟอร์ม
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>