<?php
// File: form_2.php (Revised)
session_start(); // ⭐️ เพิ่ม session_start() ⭐️
$inspection_data = $_SESSION['inspection_data'] ?? [];

echo "<h1>นี่คือหน้าแบบกรอกข้อมูลนิทเทศตามนโยบาย</h1>";

// **ตัวอย่างการแสดงผลข้อมูลที่ดึงมาจาก Session**
echo "<p><strong>ผู้นิเทศ:</strong> " . htmlspecialchars($inspection_data['supervisor_name'] ?? 'N/A') . "</p>";
echo "<p><strong>ผู้รับนิเทศ:</strong> " . htmlspecialchars($inspection_data['teacher_name'] ?? 'N/A') . "</p>";
echo "<p><strong>รหัสวิชา:</strong> " . htmlspecialchars($inspection_data['subject_code'] ?? 'N/A') . "</p>";

// ใส่โค้ดแบบประเมินจริงของคุณที่นี่
?>