<?php
// ไฟล์: summary.php (ฉบับรวมฟอร์ม 2 & 3 และส่งข้อมูลครั้งเดียว)
session_start();

// ----------------------------------------------------------------
// A) ตรวจสอบการส่งข้อมูลแบบฟอร์ม (POST Logic - ใช้ PRG Pattern)
// ----------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ⭐️ LOGIC สำหรับการ Submit ครั้งที่สอง (จากฟอร์มในหน้านี้) ⭐️
    if (isset($_POST['submit_all_data'])) {
        
        // บันทึกข้อมูลที่กรอกใหม่ (subject_code, date, time) ลงใน Session
        // รวมข้อมูลใหม่เข้ากับข้อมูลเดิมที่อยู่ใน Session (เช่น supervisor_name, teacher_name)
        // รวมถึง evaluation_type ที่ส่งกลับมาใน Hidden Field
        $_SESSION['inspection_data'] = array_merge($_SESSION['inspection_data'] ?? [], $_POST);

        $selected_form = $_SESSION['inspection_data']['evaluation_type'] ?? '';
        $target_page = '';

        // กำหนดหน้าเป้าหมายตามค่าที่ดึงมาจาก Session
        if ($selected_form === 'kpi_form') {
            $target_page = 'kpi_form.php';
        } elseif ($selected_form === 'policy_form') {
            $target_page = 'form_2.php';
        }
        
        // Redirect ไปหน้าฟอร์มประเมินที่เลือกไว้
        if ($target_page !== '') {
            header("Location: " . $target_page);
            exit();
        }

    } 
    // ⭐️ LOGIC สำหรับการ Submit ครั้งแรก (จากหน้า index.php) ⭐️
    elseif (isset($_POST['evaluation_type'])) {

        // 1. บันทึกข้อมูลทั้งหมดลงใน Session (รวมถึง 'evaluation_type' ที่เลือกมา)
        $_SESSION['inspection_data'] = $_POST;

        $selected_form = $_POST['evaluation_type'];
        $target_page = '';

        // หากเลือกตัวเลือกที่สอง ('policy_form') ให้ทำการ Redirect ทันที
        if ($selected_form === 'policy_form') {
            $target_page = 'form_2.php';
        }

        // Redirect ไปหน้าฟอร์มประเมินที่เลือกไว้ทันที (หากมีการกำหนด)
        if ($target_page !== '') {
            header("Location: " . $target_page);
            exit();
        }
        // หากเป็น 'kpi_form' จะไม่ Redirect และไปแสดง HTML ของหน้านี้ต่อ
    }
}

// ----------------------------------------------------------------
// B) โหลดข้อมูลสำหรับแสดงผล (จาก Session)
// ----------------------------------------------------------------
$inspection_data = $_SESSION['inspection_data'] ?? null;
$error_message = '';

if (!$inspection_data) {
    // ถ้าไม่พบข้อมูลบุคลากร (กรณีเข้าหน้านี้โดยตรง)
    $error_message = 'ไม่พบข้อมูลบุคลากร กรุณาเริ่มต้นจากแบบฟอร์มหลัก';
}

// ค่าเดิมสำหรับแสดงใน input fields (ถ้ามีการโหลดหน้าซ้ำ)
$subject_code = htmlspecialchars($inspection_data['subject_code'] ?? '');
$subject_name = htmlspecialchars($inspection_data['subject_name'] ?? '');
$inspection_time = htmlspecialchars($inspection_data['inspection_time'] ?? '');
$inspection_date = htmlspecialchars($inspection_data['inspection_date'] ?? '');
$evaluation_type = htmlspecialchars($inspection_data['evaluation_type'] ?? ''); // สำหรับส่งใน Hidden Field

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปข้อมูลและเลือกแบบฟอร์ม</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <div class="main-card card my-5">
        <div class="form-header card-header text-center bg-success text-white">
            <i class="fas fa-check-circle"></i> <span class="fw-bold">แบบบันทึกข้อมูลการนิเทศ</span>
        </div>

        <div class="card-body">

            <?php if ($error_message !== ''): ?>
                <div class="alert alert-danger text-center">
                    <p><?php echo $error_message; ?></p>
                    <a href="index.php" class="btn btn-danger">ไปยังแบบฟอร์มเริ่มต้น</a>
                </div>
            <?php elseif ($inspection_data): ?>

                <h4 class="fw-bold text-primary">ข้อมูลผู้นิเทศ</h4>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>ชื่อผู้นิเทศ:</strong> <?php echo htmlspecialchars($inspection_data['supervisor_name'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>เลขบัตรประชาชน:</strong> <?php echo htmlspecialchars($inspection_data['s_p_id'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>ตำแหน่ง:</strong> <?php echo htmlspecialchars($inspection_data['position'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>สังกัด:</strong> <?php echo htmlspecialchars($inspection_data['agency'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                </div>

                <h4 class="fw-bold text-danger">ข้อมูลผู้รับนิเทศ</h4>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>ชื่อผู้รับนิเทศ:</strong> <?php echo htmlspecialchars($inspection_data['teacher_name'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>เลขบัตรประชาชน:</strong> <?php echo htmlspecialchars($inspection_data['t_pid'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>วิทยฐานะ:</strong> <?php echo htmlspecialchars($inspection_data['adm_name'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>กลุ่มสาระ:</strong> <?php echo htmlspecialchars($inspection_data['learning_group'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>โรงเรียน:</strong> <?php echo htmlspecialchars($inspection_data['school_name'] ?? 'ไม่มีข้อมูล'); ?>
                    </div>
                </div>

                <hr class="my-5">

                <form method="POST" action="summary.php" onsubmit="return validateForm()">

                    <?php
                    // วนลูปข้อมูลใน $inspection_data เพื่อส่งกลับไปใน Hidden Field 
                    // ข้อมูลเหล่านี้จะถูกส่งไปยังหน้าฟอร์มที่เลือกในขั้นต่อไป
                    foreach ($inspection_data as $key => $value) {
                        // ไม่ต้องส่ง key ที่จะถูกกรอกในฟอร์มนี้ซ้ำ
                        $excluded_keys = ['subject_code', 'subject_name', 'inspection_time', 'inspection_date', 'evaluation_type'];
                        if (!in_array($key, $excluded_keys)) {
                            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                        }
                    }
                    ?>

                    <h4 class="fw-bold text-success">กรอกข้อมูลการนิเทศ</h4>
                    <div class="row g-3 mt-4">
                        <div class="col-md-6">
                            <label for="subject_code" class="form-label fw-bold">รหัสวิชา</label>
                            <input type="text" id="subject_code" name="subject_code"
                                class="form-control search-field"
                                placeholder="เช่น ท0001" required
                                value="<?php echo $subject_code; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="subject_name" class="form-label fw-bold">ชื่อวิชา</label>
                            <input type="text" id="subject_name" name="subject_name"
                                class="form-control search-field"
                                placeholder="เช่น ภาษาไทย" required
                                value="<?php echo $subject_name; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="inspection_time" class="form-label fw-bold">ครั้งที่นิเทศ</label>
                            <select id="inspection_time" name="inspection_time" class="form-select" required>
                                <option value="" disabled <?php echo $inspection_time === '' ? 'selected' : ''; ?>>-- เลือกครั้งที่นิเทศ --</option>
                                <?php for ($i = 1; $i <= 9; $i++): ?>
                                    <option value="<?php echo $i; ?>"
                                        <?php echo ($inspection_time == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="inspection_date" class="form-label fw-bold">วันที่การนิเทศ</label>
                            <input type="date" id="inspection_date" name="inspection_date"
                                class="form-control search-field" required
                                value="<?php echo $inspection_date; ?>">
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <input type="hidden" name="evaluation_type" value="<?php echo $evaluation_type; ?>">

                    <div class="row g-2 mt-0 justify-content-center">
                        <div class="col-auto">
                            <button type="submit" name="submit_all_data" class="btn btn-success btn-lg">
                                <i class="fas fa-arrow-right"></i> ดำเนินการเข้าสู่แบบฟอร์ม
                            </button>
                        </div>
                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ลบ logic สำหรับ radio button ออก เนื่องจากไม่มีตัวเลือกแล้ว
        });

        // ⭐️ JavaScript Function สำหรับตรวจสอบฟอร์ม ⭐️
        function validateForm() {
            const subjectCode = document.getElementById('subject_code').value;
            const subjectName = document.getElementById('subject_name').value;
            const inspectionTime = document.getElementById('inspection_time').value;
            const inspectionDate = document.getElementById('inspection_date').value;

            // ตรวจสอบว่ากรอกข้อมูลการนิเทศครบหรือไม่ (Required fields ควรทำงานอยู่แล้ว)
            if (!subjectCode || !subjectName || !inspectionTime || !inspectionDate) {
                alert('กรุณากรอกข้อมูลการนิเทศให้ครบถ้วน');
                return false;
            }

            // ลบ logic ตรวจสอบ radio button ออก เนื่องจากไม่มีตัวเลือกแล้ว

            return true; // ส่งฟอร์ม
        }
    </script>
</body>

</html>