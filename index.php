<?php
// 1. นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db_connect.php'; 

// 2. ส่วนเลือกข้อมูลผู้นิเทศ (เปิด form tag)
require_once 'supervisor.php'; 

// 3. ส่วนเลือกข้อมูลผู้รับนิเทศ (มีปุ่มบันทึกและปิด form tag)
require_once 'teacher.php'; 

// // 4. ส่วนเลือกแบบฟรอมนิเทศ (นำออกตามโครงสร้างใหม่)
// require_once 'form_selector.php'; 
?>
    </div> <script>
        // ⭐️ เรียกฟังก์ชัน populateNameDropdown เมื่อหน้าโหลดเสร็จ (จาก supervisor.php)
        window.onload = populateNameDropdown;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>