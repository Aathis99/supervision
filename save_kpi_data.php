<?php
session_start(); // เริ่ม session เพื่อใช้เก็บข้อความสถานะ
require_once 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// --- ฟังก์ชันสำหรับแสดงผลและหยุดการทำงาน ---
function redirect_with_message($message, $type = 'danger') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    // เปลี่ยนเส้นทางกลับไปที่ kpi_form.php
    header("Location: kpi_form.php");
    exit();
}

// 1. ตรวจสอบว่าข้อมูลถูกส่งมาแบบ POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. รับข้อมูลหลัก (ผู้นิเทศ และ ผู้รับการนิเทศ) จาก SESSION
    if (!isset($_SESSION['supervision_data'])) {
        redirect_with_message("Session หมดอายุหรือไม่พบข้อมูลการนิเทศ กรุณาเริ่มต้นใหม่");
    }
    $supervision_data = $_SESSION['supervision_data'];
    $supervisor_p_id = isset($supervision_data['supervisor_p_id']) ? trim($supervision_data['supervisor_p_id']) : '';
    $teacher_t_pid = isset($supervision_data['teacher_t_pid']) ? trim($supervision_data['teacher_t_pid']) : '';

    // 3. รับข้อมูลการประเมิน (คะแนน, ข้อค้นพบ, ข้อเสนอแนะ)
    $ratings = isset($_POST['ratings']) ? $_POST['ratings'] : [];
    $comments = isset($_POST['comments']) ? $_POST['comments'] : [];
    $indicator_suggestions = isset($_POST['indicator_suggestions']) ? $_POST['indicator_suggestions'] : [];

    // 4. ตรวจสอบข้อมูลเบื้องต้น
    if (empty($supervisor_p_id) || empty($teacher_t_pid)) {
        redirect_with_message("เกิดข้อผิดพลาด: ไม่พบข้อมูลผู้นิเทศหรือผู้รับการนิเทศ กรุณาเลือกข้อมูลให้ครบถ้วน");
    }

    if (empty($ratings)) {
        redirect_with_message("กรุณาให้คะแนนอย่างน้อยหนึ่งข้อ");
    }

    // เริ่มต้น Transaction เพื่อให้แน่ใจว่าข้อมูลทั้งหมดจะถูกบันทึกพร้อมกัน
    $conn->begin_transaction();

    try {
        // 5. บันทึกข้อมูลลงในตาราง `supervision_sessions`
        $stmt_session = $conn->prepare("INSERT INTO supervision_sessions (supervisor_p_id, teacher_t_pid) VALUES (?, ?)");
        if (!$stmt_session) {
            throw new Exception("Prepare statement for session failed: " . $conn->error);
        }
        $stmt_session->bind_param("ss", $supervisor_p_id, $teacher_t_pid);
        $stmt_session->execute();

        // ดึง ID ของ session ที่เพิ่งสร้างขึ้นมา
        $session_id = $conn->insert_id;
        $stmt_session->close();

        // 6. วนลูปบันทึกข้อมูลคะแนนและข้อค้นพบลงใน `kpi_answers`
        $stmt_answer = $conn->prepare("INSERT INTO kpi_answers (session_id, question_id, rating_score, comment) VALUES (?, ?, ?, ?)");
        if (!$stmt_answer) {
            throw new Exception("Prepare statement for answers failed: " . $conn->error);
        }

        foreach ($ratings as $question_id => $score) {
            // ตรวจสอบให้แน่ใจว่าเป็น integer
            $q_id = (int)$question_id;
            $rating_score = (int)$score;
            // ดึง comment ที่มี key ตรงกัน (ถ้ามี)
            $comment_text = isset($comments[$q_id]) ? trim($comments[$q_id]) : null;

            $stmt_answer->bind_param("iiis", $session_id, $q_id, $rating_score, $comment_text);
            $stmt_answer->execute();
        }
        $stmt_answer->close();

        // 7. วนลูปบันทึกข้อเสนอแนะเพิ่มเติมลงใน `kpi_indicator_suggestions`
        $stmt_suggestion = $conn->prepare("INSERT INTO kpi_indicator_suggestions (session_id, indicator_id, suggestion_text) VALUES (?, ?, ?)");
        if (!$stmt_suggestion) {
            throw new Exception("Prepare statement for suggestions failed: " . $conn->error);
        }

        foreach ($indicator_suggestions as $indicator_id => $suggestion) {
            $suggestion_text = trim($suggestion);
            if (!empty($suggestion_text)) { // บันทึกเฉพาะข้อเสนอแนะที่มีการกรอกข้อมูล
                $ind_id = (int)$indicator_id;
                $stmt_suggestion->bind_param("iis", $session_id, $ind_id, $suggestion_text);
                $stmt_suggestion->execute();
            }
        }
        $stmt_suggestion->close();

        // 8. ถ้าทุกอย่างสำเร็จ ให้ Commit Transaction
        $conn->commit();

        // ล้างข้อมูล session หลังจากบันทึกสำเร็จ
        unset($_SESSION['supervision_data']);

        // ตั้งค่าข้อความแจ้งเตือนว่าสำเร็จ
        redirect_with_message("บันทึกข้อมูลการประเมินเรียบร้อยแล้ว", "success");

    } catch (Exception $e) {
        // 9. หากเกิดข้อผิดพลาด ให้ Rollback Transaction
        $conn->rollback();
        // แสดงข้อความ Error
        redirect_with_message("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
    }

    // ปิดการเชื่อมต่อ
    $conn->close();

} else {
    // ถ้าไม่ได้เข้ามาหน้านี้ผ่านการ POST
    redirect_with_message("ไม่สามารถเข้าถึงหน้านี้โดยตรงได้");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>กำลังบันทึกข้อมูล...</title>
</head>
<body>
    <p>กำลังประมวลผลข้อมูล กรุณารอสักครู่...</p>
</body>
</html>