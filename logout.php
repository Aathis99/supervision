<?php
session_start();

// ลบข้อมูลทั้งหมดใน Session
session_unset();

// ทำลาย Session
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้าล็อกอิน
header("Location: history.php");
exit();