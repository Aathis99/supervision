<?php
// ไฟล์: history.php
require_once 'db_connect.php';

// ตรวจสอบค่า search_name: ถ้ามีค่าเข้ามา ให้ Trim ถ้าไม่มีค่า (หรือเข้าหน้าครั้งแรก) ให้เป็นค่าว่าง
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$results = [];

// SQL พื้นฐานสำหรับดึงข้อมูล
// ⭐️ ดึงข้อมูลที่จำเป็นตามภาพ: วันที่, ชื่อครู, โรงเรียน, ชื่อผู้นิเทศ, รายวิชา, เวลา, ปุ่มดูรายงาน
$sql = "SELECT
            ss.id AS session_id,
            ss.inspection_date,
            ss.subject_name,
            ss.inspection_time,
            CONCAT(t.PrefixName, t.fname, ' ', t.lname) AS teacher_full_name,
            CONCAT(sp.PrefixName, sp.fname, ' ', sp.lname) AS supervisor_full_name,
            s_school.SchoolName AS t_school,
            ss.supervision_date
        FROM
            supervision_sessions ss
        LEFT JOIN
            teacher t ON ss.teacher_t_pid = t.t_pid
        LEFT JOIN
            school s_school ON t.school_id = s_school.school_id
        LEFT JOIN
            supervisor sp ON ss.supervisor_p_id = sp.p_id";

$params = [];
$types = '';

// ⭐️ เงื่อนไขการค้นหา: จะทำการค้นหาก็ต่อเมื่อ $search_name ไม่ใช่ค่าว่างเท่านั้น ⭐️
if (!empty($search_name)) {
    // กรณีมีการค้นหา: เพิ่ม WHERE clause
    $search_term = "%" . $search_name . "%";
    $sql .= " WHERE CONCAT(t.fname, ' ', t.lname) LIKE ? OR CONCAT(sp.fname, ' ', sp.lname) LIKE ?";
    // เพิ่มการค้นหาจาก subject_name ด้วย
    // $sql .= " WHERE CONCAT(t.fname, ' ', t.lname) LIKE ? OR CONCAT(sp.fname, ' ', sp.lname) LIKE ? OR ss.subject_name LIKE ?";
    $params = [$search_term, $search_term];
    $types = "ss";
    // $params = [$search_term, $search_term, $search_term];
    // $types = "sss";
}

// ⭐️ เรียงลำดับจากวันที่ล่าสุด ⭐️
$sql .= " ORDER BY ss.inspection_date DESC, ss.id DESC";


// เตรียมและดำเนินการสอบถาม
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการนิเทศ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* สไตล์สำหรับตาราง (เพื่อให้อ่านง่ายขึ้น) */
        .table-custom th {
            background-color: #007bff;
            color: white;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="card-title text-center mb-4"><i class="fas fa-history"></i> ประวัติการนิเทศ</h2>

            <form method="GET" action="history.php" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="ค้นหาด้วยชื่อครู หรือ ชื่อผู้นิเทศ..." name="search_name" value="<?php echo htmlspecialchars($search_name); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> ค้นหา</button>
                    <a href="history.php" class="btn btn-secondary" title="แสดงรายการทั้งหมด">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
                <small class="form-text text-muted">หากไม่กรอกข้อมูลและกดปุ่ม 'ค้นหา' จะแสดงรายการทั้งหมด</small>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-custom align-middle">
                    <thead>
                        <tr>
                            <!-- <th scope="col" class="text-center" style="width: 15%;">วันที่นิเทศ</th> -->
                            <th scope="col">ชื่อผู้รับนิเทศ</th>
                            <th scope="col">โรงเรียน</th>
                            <th scope="col">ชื่อผู้นิเทศ</th>
                            <th scope="col">ปุ่มดูรายงาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)) : ?>
                            <tr>
                                <td colspan="7" class="text-center text-danger fw-bold">
                                    <?php echo !empty($search_name) ? "ไม่พบข้อมูลการนิเทศที่ตรงกับการค้นหา: \"" . htmlspecialchars($search_name) . "\"" : "ไม่พบประวัติการนิเทศที่บันทึกไว้ในระบบ"; ?>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($results as $row) : ?>
                                <tr>
                                    <!-- <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($row['supervision_date'])); ?></td> -->
                                    <td><?php echo htmlspecialchars($row['teacher_full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['t_school']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_full_name']); ?></td>      
                                    <td>
                                        <a href="supervision_report.php?session_id=<?php echo $row['session_id']; ?>" class="btn btn-primary"><i class="fas fa-file-alt"></i> คลิกดูรายงาน</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> กลับหน้าหลัก</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>