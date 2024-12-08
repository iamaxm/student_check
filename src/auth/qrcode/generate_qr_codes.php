<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once '../config/ConnectDB.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

// รับข้อมูลจาก AJAX
$selectedGrade = isset($_GET['grade_id']) ? $_GET['grade_id'] : null;
$selectedRoom = isset($_GET['room_id']) ? $_GET['room_id'] : null;

// สร้าง SQL Query
$studentQuery = "
    SELECT 
        student.id, 
        student.name, 
        student.surname, 
        grade_level.grade_level AS grade_name
    FROM student
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    WHERE 1=1
";

if (!empty($selectedGrade)) {
    $studentQuery .= " AND student.id_grade = '$selectedGrade'";
}

if (!empty($selectedRoom)) {
    $studentQuery .= " AND student.id_room = '$selectedRoom'";
}

$studentResult = $conn->query($studentQuery);

if (!$studentResult || $studentResult->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "ไม่พบนักเรียนในชั้นหรือห้องที่เลือก"]);
    exit();
}

// สร้างไฟล์ ZIP ในหน่วยความจำ
$zip = new ZipArchive();
$zipFileName = tempnam(sys_get_temp_dir(), 'qrcodes') . '.zip';

if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    echo json_encode(["success" => false, "error" => "ไม่สามารถสร้างไฟล์ ZIP ได้"]);
    exit();
}

while ($row = $studentResult->fetch_assoc()) {
    $studentId = $row['id'];
    $studentName = $row['name'];
    $studentSurname = $row['surname'];
    $gradeName = $row['grade_name'];

    // สร้าง QR Code
    $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data("student_id: $studentId\nname: $studentName $studentSurname\ngrade: $gradeName")
        ->size(300)
        ->margin(10)
        ->build();

    // ตั้งชื่อไฟล์โดยใช้ชื่อชั้นเรียนและชื่อนักเรียน
    $fileName = "{$gradeName}_{$studentName}_{$studentSurname}.png";

    // เพิ่มไฟล์ QR Code เข้า ZIP โดยตรง
    $zip->addFromString($fileName, $qrCode->getString());
}

$zip->close();

// ส่งไฟล์ ZIP ให้ผู้ใช้ดาวน์โหลด
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="qrcodes.zip"');
header('Content-Length: ' . filesize($zipFileName));
readfile($zipFileName);

// ลบไฟล์ ZIP ชั่วคราว
unlink($zipFileName);
exit();
