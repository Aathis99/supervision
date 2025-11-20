<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['session_id'])) {
    echo "Session ID is missing.";
    exit;
}

$session_id = $_GET['session_id'];

// ดึงข้อมูล session
$sql = "SELECT s.*, 
               CONCAT(t.PrefixName, t.fname, ' ', t.lname) AS teacher_full_name, 
               CONCAT(sp.PrefixName, sp.fname, ' ', sp.lname) AS supervisor_full_name
        FROM supervision_sessions s
        LEFT JOIN teacher t ON s.teacher_t_pid = t.t_pid
        LEFT JOIN supervisor sp ON s.supervisor_p_id = sp.p_id
        WHERE s.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Session not found.";
    exit;
}

$session = $result->fetch_assoc();

// ตรวจสอบว่า session นี้ถูกประเมินแล้วหรือยัง
if ($session['satisfaction_submitted'] != 1) {
    echo "This session has not been evaluated yet.";
    exit;
}

// ข้อมูลสำหรับ Certificate
$teacher_name = $session['teacher_full_name'];
$supervisor_name = $session['supervisor_full_name'];
$supervision_date_formatted = date("j F Y", strtotime($session['supervision_date'])); // Format date
$issue_date = date("j F Y");

// Include TCPDF library
require_once __DIR__ . '/vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SESA System');
$pdf->SetTitle('Supervision Certificate');
$pdf->SetSubject('Certificate of Supervision');
$pdf->SetKeywords('TCPDF, certificate, supervision');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(10, 10, 10, true);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('freeserif', '', 20, '', false);

// Certificate content
$html = '
<style>
body {
    font-family: freeserif;
}
.certificate {
    text-align: center;
}
.title {
    font-size: 24pt;
    font-weight: bold;
    margin-bottom: 20px;
}
.statement {
    font-size: 18pt;
    margin-bottom: 30px;
}
.name {
    font-size: 22pt;
    font-weight: bold;
}
.date {
    font-size: 16pt;
    margin-top: 20px;
}
</style>
<div class="certificate">
    <div class="title">ทดสอบเกียรติบัตร</div>
    <div class="statement">
        ขอแสดงความยินดีแก่
    </div>
    <div class="name">' . $teacher_name . '</div>
    <div class="statement">
        ที่ได้รับการนิเทศจาก
    </div>
    <div class="name">' . $supervisor_name . '</div>
    <div class="statement">
        เมื่อวันที่ ' . $supervision_date_formatted . '
    </div>
    <div class="date">
        ออกให้ ณ วันที่ ' . $issue_date . '
    </div>
</div>
';

// Print text using HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF to the browser
$pdf->Output('certificate_' . $session_id . '.pdf', 'I');
?>
