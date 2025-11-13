<?php 
// ไฟล์: kpi_form.php (แก้ไขล่าสุด)
session_start(); // ⭐️ 1. เพิ่ม session_start() เพื่อดึงข้อมูลจาก Session ⭐️
$inspection_data = $_SESSION['inspection_data'] ?? [];

// ดึงข้อมูลหลักมาแสดงผล
$supervisor_name = htmlspecialchars($inspection_data['supervisor_name'] ?? 'N/A');
$teacher_name = htmlspecialchars($inspection_data['teacher_name'] ?? 'N/A');
$subject_code = htmlspecialchars($inspection_data['subject_code'] ?? 'N/A');
$subject_name = htmlspecialchars($inspection_data['subject_name'] ?? 'N/A');
$inspection_date = htmlspecialchars($inspection_data['inspection_date'] ?? 'N/A');
?>
<!DOCTYPE html>
<html lang="th">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>แบบบันทึกการจัดการเรียนรู้และการจัดการชั้นเรียน</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    
    <link href="styles.css" rel="stylesheet" />

    </head>
  <body>
    <div class="container mt-5">
        <div class="section-header mb-3">
        <h2 class="h5">
          ตัวชี้วัดที่ 1 ผู้เรียนสามารถ เข้าถึงสิ่งเรียนและ เข้าใจบทเรียน /
          กิจกรรม
        </h2>
      </div>

      <form id="evaluationForm" method="POST" action="save_evaluation.php"> 
      
            <?php 
                foreach ($inspection_data as $key => $value) {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                }
            ?>

            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label-question"
                            >เนื้อหา (Content) พร้อมโน้ตทัศน์ที่จัดให้ผู้เรียนเรียนรู้
                            หรือฝึกฝน มีความถูกต้อง และ ตรงตามหลักสูตร</label
                        >
                    </div>
                    <p>เลือกคะแนนตามความพึงพอใจของคุณ</p>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="q1_contentRating" 
                            id="q1_1-3"
                            value="3"
                        />
                        <label class="form-check-label" for="q1_1-3">3</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q1_contentRating" id="q1_1-2" value="2" />
                        <label class="form-check-label" for="q1_1-2">2</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q1_contentRating" id="q1_1-1" value="1" />
                        <label class="form-check-label" for="q1_1-1">1</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q1_contentRating" id="q1_1-0" value="0" />
                        <label class="form-check-label" for="q1_1-0">0</label>
                    </div>

                    <hr class="my-4" />
                    <div class="mb-3">
                        <label for="q1_comments" class="form-label">ข้อค้นพบ</label>
                        <textarea
                            class="form-control"
                            id="q1_comments"
                            name="q1_comments"
                            rows="3"
                            placeholder="กรอกความคิดเห็นของคุณที่นี่..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label-question"
                            >ออกแบบและจัดโครงสร้างบทเรียนเป็นระบบและใช้เวลาเหมาะสม</label
                        >
                    </div>
                    <p>เลือกคะแนนตามความพึงพอใจของคุณ</p>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q2_designRating" id="q2_1-3" value="3" />
                        <label class="form-check-label" for="q2_1-3">3</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q2_designRating" id="q2_1-2" value="2" />
                        <label class="form-check-label" for="q2_1-2">2</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q2_designRating" id="q2_1-1" value="1" />
                        <label class="form-check-label" for="q2_1-1">1</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q2_designRating" id="q2_1-0" value="0" />
                        <label class="form-check-label" for="q2_1-0">0</label>
                    </div>

                    <hr class="my-4" />
                    <div class="mb-3">
                        <label for="q2_comments" class="form-label">ข้อค้นพบ</label>
                        <textarea
                            class="form-control"
                            id="q2_comments"
                            name="q2_comments"
                            rows="3"
                            placeholder="กรอกความคิดเห็นของคุณที่นี่..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label-question"
                            >ใช้สื่อประกอบบทเรียนได้เหมาะสมและช่วยในการเรียนรู้บรรลุวัตถุประสงค์ของบทเรียน</label
                        >
                    </div>
                    <p>เลือกคะแนนตามความพึงพอใจของคุณ</p>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q3_mediaRating" id="q3_1-3" value="3" />
                        <label class="form-check-label" for="q3_1-3">3</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q3_mediaRating" id="q3_1-2" value="2" />
                        <label class="form-check-label" for="q3_1-2">2</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q3_mediaRating" id="q3_1-1" value="1" />
                        <label class="form-check-label" for="q3_1-1">1</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="q3_mediaRating" id="q3_1-0" value="0" />
                        <label class="form-check-label" for="q3_1-0">0</label>
                    </div>

                    <hr class="my-4" />
                    <div class="mb-3">
                        <label for="q3_comments" class="form-label">ข้อค้นพบ</label>
                        <textarea
                            class="form-control"
                            id="q3_comments"
                            name="q3_comments"
                            rows="3"
                            placeholder="กรอกความคิดเห็นของคุณที่นี่..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-3 p-2">
                <label for="final_suggestions" class="form-label">ข้อเสนอแนะ (รวม)</label>
                <textarea
                    class="form-control"
                    id="final_suggestions"
                    name="final_suggestions"
                    rows="3"
                    placeholder="กรอกความคิดเห็นของคุณที่นี่..."
                ></textarea>
            </div>

            <div class="d-flex justify-content-center mt-4 mb-5">
                <button
                    type="submit"
                    class="btn btn-success fs-5 btn-hover-blue"
                >
                    <i class="fas fa-save"></i> บันทึกข้อมูลและเสร็จสิ้น
                </button>
            </div>
      </form> </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // ... (โค้ด JavaScript เดิมสำหรับ debug)
      });
      // ลบ function changeBackgroundColor(color) ที่ไม่จำเป็นออก
    </script>
  </body>
</html>