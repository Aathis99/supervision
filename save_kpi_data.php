<?php
// ไฟล์: save_kpi_data.php
session_start();
require_once 'db_connect.php';

function redirect_with_message($message, $type = 'danger')
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: kpi_form.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['inspection_data'])) {
        redirect_with_message("Session หมดอายุหรือไม่พบข้อมูลการนิเทศ กรุณาเริ่มต้นใหม่");
    }

    $s_data = $_SESSION['inspection_data'];

    // รับข้อมูลพื้นฐาน
    $supervisor_p_id = $s_data['s_p_id'] ?? '';  // แก้จาก supervisor_p_id เป็น s_p_id
    $teacher_t_pid   = $s_data['t_pid'] ?? '';

    // ⭐️ FIX: รับข้อมูลการนิเทศจาก $_POST โดยตรง (เพราะถูกกรอกในฟอร์ม)
    $subject_code    = trim($_POST['subject_code'] ?? '');
    $subject_name    = trim($_POST['subject_name'] ?? '');
    $inspection_time = $_POST['inspection_time'] ?? 1;
    $inspection_date = $_POST['inspection_date'] ?? date('Y-m-d');

    $ratings = $_POST['ratings'] ?? [];
    $comments = $_POST['comments'] ?? [];
    $indicator_suggestions = $_POST['indicator_suggestions'] ?? [];
    $overall_suggestion = trim($_POST['overall_suggestion'] ?? ''); // ⭐️ รับข้อมูลข้อเสนอแนะภาพรวม

    if (empty($supervisor_p_id) || empty($teacher_t_pid)) {
        redirect_with_message("ข้อมูลไม่ครบถ้วน");
    }

    if (empty($ratings)) {
        redirect_with_message("กรุณาให้คะแนนอย่างน้อยหนึ่งข้อ");
    }

    $conn->begin_transaction();

    try {
        // 1. บันทึกข้อมูล Session ลงในตาราง supervision_sessions พร้อมฟิลด์ใหม่
        $sql_session = "INSERT INTO supervision_sessions 
                        (supervisor_p_id, teacher_t_pid, subject_code, subject_name, inspection_time, inspection_date, overall_suggestion) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt_session = $conn->prepare($sql_session);
        $stmt_session->bind_param(
            "ssssiss",
            $supervisor_p_id,
            $teacher_t_pid,
            $subject_code,
            $subject_name,
            $inspection_time,
            $inspection_date,
            $overall_suggestion // ⭐️ เพิ่มตัวแปรสำหรับ bind
        );
        $stmt_session->execute();
        $session_id = $conn->insert_id;
        $stmt_session->close();

        // 2. บันทึกคะแนนและข้อค้นพบ
        $stmt_answer = $conn->prepare("INSERT INTO kpi_answers (session_id, question_id, rating_score, comment) VALUES (?, ?, ?, ?)");
        foreach ($ratings as $question_id => $score) {
            $q_id = (int)$question_id;
            $rating_score = (int)$score;
            $comment_text = isset($comments[$q_id]) ? trim($comments[$q_id]) : null;
            $stmt_answer->bind_param("iiis", $session_id, $q_id, $rating_score, $comment_text);
            $stmt_answer->execute();
        }
        $stmt_answer->close();

        // 3. บันทึกข้อเสนอแนะเพิ่มเติมรายตัวชี้วัด
        $stmt_suggestion = $conn->prepare("INSERT INTO kpi_indicator_suggestions (session_id, indicator_id, suggestion_text) VALUES (?, ?, ?)");
        foreach ($indicator_suggestions as $indicator_id => $suggestion) {
            $suggestion_text = trim($suggestion);
            if (!empty($suggestion_text)) {
                $ind_id = (int)$indicator_id;
                $stmt_suggestion->bind_param("iis", $session_id, $ind_id, $suggestion_text);
                $stmt_suggestion->execute();
            }
        }
        $stmt_suggestion->close();

        $conn->commit();

        unset($_SESSION['inspection_data']);

        // เปลี่ยนเส้นทางไปยังหน้าประวัติเพื่อแสดงข้อมูลทั้งหมด
        header("Location: history.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        redirect_with_message("เกิดข้อผิดพลาด: " . $e->getMessage());
    }
    $conn->close();
}
