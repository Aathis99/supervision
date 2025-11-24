<?php
// ไฟล์: satisfaction_form.php (จะถูก include ใน satisfaction_summary.php)
require_once 'db_connect.php'; // ⭐️ เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจาก Session ที่ถูกตั้งค่าไว้ในหน้า summary
$satisfaction_data = $_SESSION['satisfaction_data'] ?? [];

// ⭐️ ดึงคำถามจากฐานข้อมูล
$sql_questions = "SELECT id, question_text FROM satisfaction_questions ORDER BY display_order ASC";
$result_questions = $conn->query($sql_questions);

$questions = [];
if ($result_questions && $result_questions->num_rows > 0) {
    while ($row = $result_questions->fetch_assoc()) {
        $questions[] = $row;
    }
}

?>
<!-- แบบฟอร์มหลัก -->
<form id="satisfactionForm" method="POST" action="save_satisfaction.php">

    <!-- ⭐️ เพิ่มคำชี้แจงตามที่ผู้ใช้ต้องการ ⭐️ -->
    <p class="mb-2"><strong>คำชี้แจง :</strong> โปรดเลือกระดับความพึงพอใจที่ตรงกับความพึงพอใจของท่านมากที่สุด เกณฑ์การประเมินความพึงพอใจ
        มี 5 ระดับ ดังนี้ <br>5 หมายถึง มากที่สุด   4 หมายถึงมาก   3 หมายถึงปานกลาง   2 หมายถึง น้อย  1 หมายถึง น้อยที่สุด
    </p>

    <hr>

    <?php foreach ($questions as $question) : ?>
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label-question" for="rating_<?php echo $question['id']; ?>">
                        <?php echo htmlspecialchars($question['question_text']); ?>
                    </label>
                </div>

                <div class="d-flex justify-content-center flex-wrap">
                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                        <div class="form-check form-check-inline mx-2">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="ratings[<?php echo $question['id']; ?>]"
                                id="q<?php echo $question['id']; ?>-<?php echo $i; ?>"
                                value="<?php echo $i; ?>"
                                required
                                <?php echo ($i == 5) ? 'checked' : ''; // ให้คะแนน 5 เป็นค่าเริ่มต้น 
                                ?> />
                            <label class="form-check-label" for="q<?php echo $question['id']; ?>-<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- ส่วนสำหรับ "ข้อเสนอแนะเพิ่มเติม" -->
    <div class="card mt-4 border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-lightbulb"></i> ข้อเสนอแนะเพิ่มเติมเพื่อการพัฒนาระบบ
        </div>
        <div class="card-body">
            <textarea
                class="form-control"
                id="overall_suggestion"
                name="overall_suggestion"
                rows="4"
                placeholder="กรอกข้อเสนอแนะของคุณที่นี่..."></textarea>
        </div>
    </div>

    <!-- ปุ่มบันทึกข้อมูล -->
    <div class="d-flex justify-content-center my-4">
        <button type="submit" class="btn btn-success fs-5 btn-hover-blue px-4 py-2">
            <i class="fas fa-save"></i> บันทึก
        </button>
    </div>
</form><?php
// ไฟล์: satisfaction_form.php (จะถูก include ใน satisfaction_summary.php)
require_once 'db_connect.php'; // ⭐️ เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจาก Session ที่ถูกตั้งค่าไว้ในหน้า summary
$satisfaction_data = $_SESSION['satisfaction_data'] ?? [];

// ⭐️ ดึงคำถามจากฐานข้อมูล
$sql_questions = "SELECT id, question_text FROM satisfaction_questions ORDER BY display_order ASC";
$result_questions = $conn->query($sql_questions);

$questions = [];
if ($result_questions && $result_questions->num_rows > 0) {
    while($row = $result_questions->fetch_assoc()) {
        $questions[] = $row;
    }
}

?>
<!-- แบบฟอร์มหลัก -->
<form id="satisfactionForm" method="POST" action="save_satisfaction.php">

    <!-- ส่วนแสดงข้อมูลการนิเทศ -->
    <h4 class="fw-bold text-primary">ข้อมูลการนิเทศ</h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <strong>ผู้นิเทศ:</strong> <?php echo htmlspecialchars($satisfaction_data['supervisor_name'] ?? 'N/A'); ?>
        </div>
        <div class="col-md-6">
            <strong>ผู้รับการนิเทศ:</strong> <?php echo htmlspecialchars($satisfaction_data['teacher_name'] ?? 'N/A'); ?>
        </div>
    </div>

    <hr class="my-5">

    <!-- ส่วนของคำถามประเมิน -->
    <div class="section-header mb-3">
        <h2 class="h5">ประเด็นการประเมินความพึงพอใจ</h2>
    </div>

    <?php foreach ($questions as $question) : ?>
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label-question" for="rating_<?php echo $question['id']; ?>">
                        <?php echo htmlspecialchars($question['question_text']); ?>
                    </label>
                </div>
                <p>ระดับความพึงพอใจ (5 = มากที่สุด, 1 = น้อยที่สุด)</p>

                <div class="d-flex justify-content-center flex-wrap">
                    <?php for ($i = 5; $i >= 1; $i--) : ?>
                        <div class="form-check form-check-inline mx-2">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="ratings[<?php echo $question['id']; ?>]"
                                id="q<?php echo $question['id']; ?>-<?php echo $i; ?>"
                                value="<?php echo $i; ?>"
                                required
                                <?php echo ($i == 5) ? 'checked' : ''; // ให้คะแนน 5 เป็นค่าเริ่มต้น ?>
                            />
                            <label class="form-check-label" for="q<?php echo $question['id']; ?>-<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- ส่วนสำหรับ "ข้อเสนอแนะเพิ่มเติม" -->
    <div class="card mt-4 border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-lightbulb"></i> ข้อเสนอแนะเพิ่มเติมเพื่อการพัฒนาระบบ
        </div>
        <div class="card-body">
            <textarea
                class="form-control"
                id="overall_suggestion"
                name="overall_suggestion"
                rows="4"
                placeholder="กรอกข้อเสนอแนะของคุณที่นี่..."></textarea>
        </div>
    </div>

    <!-- ปุ่มบันทึกข้อมูล -->
    <div class="d-flex justify-content-center my-4">
        <button type="submit" class="btn btn-success fs-5 btn-hover-blue px-4 py-2">
            <i class="fas fa-save"></i> บันทึกข้อมูลความพึงพอใจ
        </button>
    </div>
</form>