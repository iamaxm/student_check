<?php
require 'vendor/autoload.php'; // โหลด PHPSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include '../config/ConnectDB.php'; // เชื่อมต่อฐานข้อมูล

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// Fetch ข้อมูลการตั้งค่าเวลา
$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

// รับค่าจากฟอร์มหรือใช้ค่าดีฟอลต์
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
$selected_grade = isset($_GET['grade_id']) ? $_GET['grade_id'] : null;
$selected_room = isset($_GET['room_id']) ? $_GET['room_id'] : null;

// ดึงชื่อชั้นเรียน
$grade_name = "All_Grades";
if ($selected_grade) {
    $grade_sql = "SELECT grade_level FROM grade_level WHERE id = '$selected_grade' LIMIT 1";
    $grade_result = $conn->query($grade_sql);
    $grade_row = $grade_result->fetch_assoc();
    $grade_name = $grade_row ? $grade_row['grade_level'] : "Grade_$selected_grade";
}

// ดึงชื่อห้องเรียน
$room_name = "All_Rooms";
if ($selected_room) {
    $room_sql = "SELECT name FROM room WHERE id = '$selected_room' LIMIT 1";
    $room_result = $conn->query($room_sql);
    $room_row = $room_result->fetch_assoc();
    $room_name = $room_row ? $room_row['name'] : "Room_$selected_room";
}

// Query ข้อมูลนักเรียน
$student_sql = "SELECT * FROM student WHERE 1=1";
if ($selected_grade) {
    $student_sql .= " AND id_grade = '$selected_grade'";
}
if ($selected_room) {
    $student_sql .= " AND id_room = '$selected_room'";
}
$student_result = $conn->query($student_sql);

// Query ข้อมูลการเช็คชื่อ
$attendance_sql = "
    SELECT id_student, 
           TIME(in_at) AS check_in_time, 
           TIME(out_at) AS check_out_time,
           DATE(in_at) AS record_date
    FROM check_in 
    WHERE DATE(in_at) = '$selected_date' OR DATE(out_at) = '$selected_date'";
$attendance_result = $conn->query($attendance_sql);

// Map attendance data
$attendance = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance[$row['id_student']] = [
        'check_in' => $row['check_in_time'],
        'check_out' => $row['check_out_time']
    ];
}

// สร้าง Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งค่าฟอนต์เริ่มต้นเป็น "Thai Sarabun New"
$spreadsheet->getDefaultStyle()->getFont()->setName('TH Sarabun New')->setSize(16);

// ตั้งค่าหัวตาราง
$sheet->setCellValue('A1', 'ชื่อ-นามสกุล');
$sheet->setCellValue('B1', 'มาเรียน (ปกติ)');
$sheet->setCellValue('C1', 'มาเรียน (สาย)');
$sheet->setCellValue('D1', 'เลิกเรียน (ปกติ)');

// เพิ่มสไตล์ให้หัวตาราง (เน้นตัวหนาและตั้งฟอนต์)
$sheet->getStyle('A1:D1')->getFont()->setBold(true);
$sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// เพิ่มข้อมูลนักเรียน
$rowIndex = 2;
while ($student = $student_result->fetch_assoc()) {
    $check_in_time = $attendance[$student['id']]['check_in'] ?? null;
    $check_out_time = $attendance[$student['id']]['check_out'] ?? null;

    $sheet->setCellValue('A' . $rowIndex, $student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']);
    $sheet->setCellValue('B' . $rowIndex, ($check_in_time && $check_in_time <= $time_settings['check_in_end']) ? $check_in_time : "-");
    $sheet->setCellValue('C' . $rowIndex, ($check_in_time && $check_in_time > $time_settings['check_in_end']) ? $check_in_time : "-");
    $sheet->setCellValue('D' . $rowIndex, $check_out_time ?? "-");
    $rowIndex++;
}

// กำหนดความกว้างของคอลัมน์
foreach (range('A', 'D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// ตั้งชื่อไฟล์
$filename = "Report_{$grade_name}_{$room_name}_{$selected_date}.xlsx";

// กำหนด Header สำหรับดาวน์โหลดไฟล์
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// ส่งออกไฟล์
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
