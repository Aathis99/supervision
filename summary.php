<?php
// ไฟล์: summary.php
session_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปข้อมูลนิเทศ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>

    <div class="main-card card my-5">
        <div class="form-header card-header text-center bg-success text-white">
            <i class="fas fa-check-circle"></i> <span class="fw-bold">สรุปข้อมูลผู้นิเทศและผู้รับนิเทศ</span>
        </div>

        <div class="card-body">

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"):
                $post_data = $_POST;
            ?>

                <h4 class="fw-bold text-primary">ข้อมูลผู้นิเทศ</h4>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>ชื่อผู้นิเทศ:</strong> <?php echo htmlspecialchars($post_data['supervisor_name'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>เลขบัตรประชาชน:</strong> <?php echo htmlspecialchars($post_data['supervisor_p_id'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>สังกัด:</strong> <?php echo htmlspecialchars($post_data['supervisor_agency'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>ตำแหน่ง:</strong> <?php echo htmlspecialchars($post_data['position'] ?? '-'); ?>
                    </div>
                </div>

                <hr>

                <h4 class="fw-bold text-primary">ข้อมูลผู้รับนิเทศ</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>ชื่อผู้รับนิเทศ:</strong> <?php echo htmlspecialchars($post_data['teacher_name'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>เลขบัตรประชาชน:</strong> <?php echo htmlspecialchars($post_data['teacher_t_pid'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>วิทยฐานะ:</strong> <?php echo htmlspecialchars($post_data['adm_name'] ?? '-'); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>กลุ่มสาระฯ:</strong> <?php echo htmlspecialchars($post_data['learning_group'] ?? '-'); ?>
                    </div>
                </div>

                <hr class="mt-4 mb-4">
                <h4 class="fw-bold text-success">กรอกข้อมูลการนิเทศเพิ่มเติม</h4>

                <form method="POST" action="form_selector.php" class="mt-4">

                    <?php
                    // วนลูปสร้าง Hidden Fields เพื่อส่งข้อมูลชุดเดิมต่อไปยังหน้าถัดไป
                    foreach ($post_data as $key => $value) {
                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                    }
                    ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subject_code" class="form-label fw-bold">รหัสวิชา</label>
                            <input type="text" id="subject_code" name="subject_code"
                                class="form-control search-field" placeholder="เช่น ว10101" required>
                        </div>

                        <div class="col-md-6">
                            <label for="subject_name" class="form-label fw-bold">ชื่อวิชา</label>
                            <input type="text" id="subject_name" name="subject_name"
                                class="form-control search-field" placeholder="เช่น วิทยาศาสตร์พื้นฐาน" required>
                        </div>

                        <div class="col-md-6">
                            <label for="inspection_time" class="form-label fw-bold">ครั้งที่นิเทศ</label>
                            <select id="inspection_time" name="inspection_time" class="form-select search-field" required>
                                <option value="" disabled selected>-- เลือกครั้งที่นิเทศ --</option>
                                <?php for ($i = 1; $i <= 9; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="inspection_date" class="form-label fw-bold">วันที่การนิเทศ</label>
                            <input type="date" id="inspection_date" name="inspection_date"
                                class="form-control search-field" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-4 justify-content-center">
                        <div class="col-auto">
                            <button type="submit" name="submit_inspection_data" class="btn btn-success btn-lg">
                                ดำเนินการเลือกแบบประเมินต่อ <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <p>ไม่พบการส่งข้อมูลแบบฟอร์ม</p>
                    <a href="index.php" class="btn btn-warning">กลับไปเลือกข้อมูล</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>