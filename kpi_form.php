<?php
session_start(); // เริ่ม Session เพื่อเข้าถึงข้อมูลที่บันทึกไว้
// 1. เชื่อมต่อฐานข้อมูล
require_once 'db_connect.php';

// 2. ดึงข้อมูลตัวชี้วัดและคำถามทั้งหมดในครั้งเดียวด้วย JOIN
$sql = "SELECT 
            ind.id AS indicator_id, 
            ind.title AS indicator_title,
            q.id AS question_id,
            q.question_text
        FROM 
            kpi_indicators ind
        LEFT JOIN 
            kpi_questions q ON ind.id = q.indicator_id
        ORDER BY 
            ind.display_order ASC, q.display_order ASC";

$result = $conn->query($sql);
// test
// 3. จัดกลุ่มข้อมูลให้อยู่ในรูปแบบที่ใช้งานง่าย
$indicators = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $indicators[$row['indicator_id']]['title'] = $row['indicator_title'];
    if ($row['question_id']) { // ตรวจสอบว่ามีคำถามหรือไม่
      $indicators[$row['indicator_id']]['questions'][] = $row;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>แบบฟอร์มประเมิน</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />

  <link href="styles.css" rel="stylesheet" />

</head>

<body>
  <div class="container mt-5">
    <!-- แบบฟอร์มหลักที่รวมทุกอย่าง -->
    <form id="evaluationForm" method="POST" action="save_kpi_data.php">

      <?php if (isset($_SESSION['inspection_data'])): ?>
        <div class="alert alert-info mb-4">
          <h5 class="alert-heading">ข้อมูลการนิเทศ</h5>
          <p class="mb-1">
            <strong>ผู้นิเทศ:</strong> <?php echo htmlspecialchars($_SESSION['inspection_data']['supervisor_name'] ?? 'N/A'); ?>
          </p>
          <p class="mb-0">
            <strong>ผู้รับการนิเทศ:</strong> <?php echo htmlspecialchars($_SESSION['inspection_data']['teacher_name'] ?? 'N/A'); ?>
          </p>
        </div>
        <!-- <div class="alert alert-danger">ไม่พบข้อมูลการนิเทศ กรุณากลับไปเริ่มต้นที่ <a href="index.php">หน้าแรก</a></div> -->
      <?php endif; ?>

      <!-- ================================================== -->
      <!-- ===== ส่วนของตัวชี้วัดและคำถาม (ของเดิม) ===== -->
      <!-- ================================================== -->

      <?php foreach ($indicators as $indicator_id => $indicator_data) : ?>
        <div class="section-header mb-3">
          <h2 class="h5"><?php echo htmlspecialchars($indicator_data['title']); ?></h2>





        </div>

        <?php if (!empty($indicator_data['questions'])) : ?>
          <?php foreach ($indicator_data['questions'] as $question) :
            $question_id = $question['question_id'];
          ?>
            <div class="card mb-3">
              <div class="card-body p-4">
                <div class="mb-3">
                  <label class="form-label-question" for="rating_<?php echo $question_id; ?>">
                    <?php echo htmlspecialchars($question['question_text']); ?>
                  </label>
                </div>
                <p>เลือกคะแนนตามความพึงพอใจของคุณ</p>

                <?php for ($i = 3; $i >= 0; $i--) : ?>
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="ratings[<?php echo $question_id; ?>]"
                      id="q<?php echo $question_id; ?>-<?php echo $i; ?>"
                      value="<?php echo $i; ?>"
                      required
                      <?php echo ($i == 3) ? 'checked' : ''; ?> /> <label class="form-check-label" for="q<?php echo $question_id; ?>-<?php echo $i; ?>"><?php echo $i; ?></label>
                  </div>
                <?php endfor; ?>

                <hr class="my-4" />
                <div class="mb-3">
                  <label for="comments_<?php echo $question_id; ?>" class="form-label">ข้อค้นพบ</label>
                  <textarea
                    class="form-control"
                    id="comments_<?php echo $question_id; ?>"
                    name="comments[<?php echo $question_id; ?>]"
                    rows="3"
                    placeholder="กรอกความคิดเห็นของคุณที่นี่...">-</textarea>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

          <!-- ส่วนสำหรับ "ข้อเสนอแนะเพิ่มเติม" ของแต่ละตัวชี้วัด -->
          <div class="card mb-4">
            <div class="card-body p-4">
              <div class="mb-3">
                <label for="indicator_suggestion_<?php echo $indicator_id; ?>" class="form-label fw-bold">ข้อเสนอแนะ</label>
                <textarea class="form-control" id="indicator_suggestion_<?php echo $indicator_id; ?>" name="indicator_suggestions[<?php echo $indicator_id; ?>]" rows="3" placeholder="กรอกข้อเสนอแนะ...">ทดสอบข้อมูล</textarea>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>

      <!-- ================================================== -->
      <!-- ===== ส่วนของข้อเสนอแนะภาพรวม (เพิ่มใหม่) ===== -->
      <!-- ================================================== -->
      <div class="card mt-4 border-primary">
        <div class="card-header bg-primary text-white fw-bold">ข้อเสนอแนะเพิ่มเติม</div>
        <div class="card-body">
          <textarea class="form-control" id="overall_suggestion" name="overall_suggestion" rows="4" placeholder="กรอกข้อเสนอแนะเพิ่มเติมเกี่ยวกับการนิเทศครั้งนี้...">-</textarea>
        </div>
      </div>

      <div class="d-flex justify-content-center my-4">
        <button type="submit" class="btn btn-success fs-5 btn-hover-blue px-4 py-2">
          บันทึกข้อมูล
        </button>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // เลือก input 'radio' ทั้งหมดในฟอร์ม
      const allRadioButtons = document.querySelectorAll(
        'form#evaluationForm input[type="radio"]'
      );

      // สามารถเพิ่ม JavaScript สำหรับตรวจสอบข้อมูลก่อนส่งได้ที่นี่
    });
  </script>
</body>

</html>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // เลือก input 'radio' ทั้งหมดในฟอร์ม
      const allRadioButtons = document.querySelectorAll(
        'form#evaluationForm input[type="radio"]'
      );

      // สามารถเพิ่ม JavaScript สำหรับตรวจสอบข้อมูลก่อนส่งได้ที่นี่
    });
  </script>
</body>

</html>