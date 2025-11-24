<?php
require_once 'db_connect.php';

// --- การดึงข้อมูลสำหรับ Dashboard ---
// ตาราง school_group มี: group_id, GroupName
// ตาราง school มี: school_id, SchoolName, SchoolGroup (เชื่อมกับ group_id)

$sql = "SELECT 
            sg.GroupName AS school_group_name,
            COUNT(s.school_id) AS school_count,
            GROUP_CONCAT(s.SchoolName ORDER BY s.SchoolName SEPARATOR ', ') AS school_list
        FROM 
            school_group sg
        LEFT JOIN 
            school s ON sg.group_id = s.SchoolGroup
        GROUP BY 
            sg.group_id, sg.GroupName
        ORDER BY 
            sg.GroupName ASC";

$result = $conn->query($sql);

$dashboard_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dashboard_data[] = $row;
    }
}

$conn->close();

// เตรียมข้อมูลสำหรับ Chart.js
$chart_labels = json_encode(array_column($dashboard_data, 'school_group_name'));
$chart_values = json_encode(array_column($dashboard_data, 'school_count'));

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - สรุปข้อมูลโรงเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-header-custom {
            background-color: #007bff;
            color: white;
        }

        .list-group-item-school {
            border: none;
            padding: .5rem 1.25rem;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header card-header-custom text-center">
                <h2 class="h4 mb-0"><i class="fas fa-chart-bar"></i> Dashboard สรุปข้อมูลโรงเรียนในสังกัด</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- ส่วนของกราฟ -->
                    <div class="col-lg-7 mb-4">
                        <h5 class="card-title text-center mb-3">จำนวนโรงเรียนในแต่ละกลุ่ม</h5>
                        <canvas id="schoolGroupChart"></canvas>
                    </div>

                    <!-- ส่วนของรายการโรงเรียน -->
                    <div class="col-lg-5">
                        <h5 class="card-title text-center mb-3">รายชื่อโรงเรียน</h5>
                        <?php if (empty($dashboard_data)): ?>
                            <div class="alert alert-warning">ไม่พบข้อมูลกลุ่มโรงเรียน</div>
                        <?php else: ?>
                            <div class="accordion" id="schoolListAccordion">
                                <?php foreach ($dashboard_data as $index => $group): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $index; ?>">
                                                <strong><?php echo htmlspecialchars($group['school_group_name']); ?></strong>&nbsp;
                                                <span class="badge bg-primary rounded-pill"><?php echo $group['school_count']; ?> โรงเรียน</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $index; ?>" data-bs-parent="#schoolListAccordion">
                                            <div class="accordion-body p-0">
                                                <ul class="list-group list-group-flush">
                                                    <?php
                                                    $schools = !empty($group['school_list']) ? explode(', ', $group['school_list']) : [];
                                                    foreach ($schools as $school):
                                                    ?>
                                                        <li class="list-group-item list-group-item-school"><?php echo htmlspecialchars($school); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-home"></i> กลับหน้าหลัก</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('schoolGroupChart').getContext('2d');
        const schoolGroupChart = new Chart(ctx, {
            type: 'bar', // สามารถเปลี่ยนเป็น 'pie', 'doughnut' ได้
            data: {
                labels: <?php echo $chart_labels; ?>,
                datasets: [{
                    label: 'จำนวนโรงเรียน',
                    data: <?php echo $chart_values; ?>,
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.5)', // ม่วง
                        'rgba(75, 0, 130, 0.5)',    // คราม
                        'rgba(54, 162, 235, 0.5)',  // น้ำเงิน
                        'rgba(75, 192, 192, 0.5)',  // เขียว
                        'rgba(255, 206, 86, 0.5)',  // เหลือง
                        'rgba(255, 159, 64, 0.5)',  // แสด
                        'rgba(255, 99, 132, 0.5)'   // แดง
                    ],
                    borderColor: [
                        'rgba(153, 102, 255, 1)',
                        'rgba(75, 0, 130, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // ให้แกน Y นับทีละ 1
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: false // ซ่อน legend เพราะมี label เดียว
                    }
                }
            }
        });
    </script>
</body>

</html>