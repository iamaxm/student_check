<?php
require 'vendor/autoload.php'; // โหลด PHPSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

include '../config/ConnectDB.php'; // เชื่อมต่อฐานข้อมูล

// ตั้งค่า Timezone
date_default_timezone_set('Asia/Bangkok');

// Fetch ข้อมูลการตั้งค่าเวลา
$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

// รับค่าจากฟอร์มหรือใช้ค่าดีฟอลต์
$selected_month = isset($_GET['month']) ? $_GET['month'] : date("Y-m");
$selected_grade = isset($_GET['grade_id']) ? $_GET['grade_id'] : null;
$selected_room = isset($_GET['room_id']) ? $_GET['room_id'] : null;
$selected_check_type = isset($_GET['check_type']) ? $_GET['check_type'] : 'check_in'; // Default: เช็คเข้าเรียน

// แปลงเดือนเป็นภาษาไทย
$thai_months = [
    "01" => "มกราคม",
    "02" => "กุมภาพันธ์",
    "03" => "มีนาคม",
    "04" => "เมษายน",
    "05" => "พฤษภาคม",
    "06" => "มิถุนายน",
    "07" => "กรกฎาคม",
    "08" => "สิงหาคม",
    "09" => "กันยายน",
    "10" => "ตุลาคม",
    "11" => "พฤศจิกายน",
    "12" => "ธันวาคม"
];
list($year, $month) = explode("-", $selected_month);
$thai_month = $thai_months[$month];
$thai_year = (int)$year + 543; // แปลงเป็นปี พ.ศ.
$days_in_month = date("t", strtotime($selected_month . "-01")); // จำนวนวันในเดือน

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
$time_field = $selected_check_type === 'check_in' ? 'in_at' : 'out_at';
$attendance_sql = "
    SELECT id_student, 
           TIME($time_field) AS check_time, 
           DATE($time_field) AS record_date
    FROM check_in 
    WHERE DATE_FORMAT($time_field, '%Y-%m') = '$selected_month'";
$attendance_result = $conn->query($attendance_sql);

// Map attendance data
$attendance = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance[$row['id_student']][$row['record_date']] = $row['check_time'];
}

// สร้าง Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งค่าฟอนต์เริ่มต้นเป็น "Thai Sarabun New"
$spreadsheet->getDefaultStyle()->getFont()->setName('TH Sarabun New')->setSize(16);

// เพิ่มแถวแรกสำหรับเดือน ปี และประเภทการเช็ค
$endColumn = Coordinate::stringFromColumnIndex($days_in_month + 1);
$sheet->setCellValue('A1', "เดือน$thai_month $thai_year");
$sheet->mergeCells("A1:$endColumn" . '1');
$sheet->getStyle("A1:$endColumn" . '1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 20],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
]);

$sheet->setCellValue('A2', "ประเภทการเช็ค: " . ($selected_check_type === 'check_in' ? "เช็คเข้าเรียน" : "เช็คออกเรียน"));
$sheet->mergeCells("A2:$endColumn" . '2');
$sheet->getStyle("A2:$endColumn" . '2')->applyFromArray([
    'font' => ['bold' => true, 'size' => 20],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
]);

// ตั้งค่าหัวตาราง (แถวที่ 3)
$sheet->setCellValue('A3', 'ชื่อ-นามสกุล');

// จัดรูปแบบสำหรับเซลล์ 'A3' (ข้อความตรงกลางและตัวหนา)
$sheet->getStyle('A3')->applyFromArray([
    'font' => [
        'bold' => true, // ตัวหนา
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // จัดข้อความให้อยู่ตรงกลางแนวนอน
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // จัดข้อความให้อยู่ตรงกลางแนวตั้ง
    ],
]);

// ตั้งค่าหัวตารางสำหรับวันที่
for ($i = 1; $i <= $days_in_month; $i++) {
    $columnLetter = Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue($columnLetter . '3', $i);

    // จัดรูปแบบเซลล์ของแต่ละวันที่ (ข้อความตรงกลางและตัวหนา)
    $sheet->getStyle($columnLetter . '3')->applyFromArray([
        'font' => [
            'bold' => true, // ตัวหนา
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // จัดข้อความให้อยู่ตรงกลางแนวนอน
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // จัดข้อความให้อยู่ตรงกลางแนวตั้ง
        ],
    ]);
}


// เพิ่มข้อมูลนักเรียน
$rowIndex = 4;
while ($student = $student_result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowIndex, $student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']);

    for ($day = 1; $day <= $days_in_month; $day++) {
        $current_date = $selected_month . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
        $columnLetter = Coordinate::stringFromColumnIndex($day + 1);
        $cell = $columnLetter . $rowIndex;

        if ($current_date > date("Y-m-d")) {
            $sheet->setCellValue($cell, ""); // ว่าง
        } else {
            $check_time = $attendance[$student['id']][$current_date] ?? null;
            if ($check_time) {
                if ($selected_check_type === 'check_in' && $check_time <= $time_settings['check_in_end']) {
                    $status = "ม"; // มาเรียนปกติ
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['color' => ['rgb' => '259b24'], 'bold' => true], // สีเขียว
                    ]);
                } else {
                    $status = "ส"; // มาสาย
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'f9a825'], 'bold' => true], // สีเหลือง
                    ]);
                }
            } else {
                $status = "ข"; // ขาดเรียน
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true], // สีแดง
                ]);
            }

            $sheet->setCellValue($cell, $status);
        }
    }
    $rowIndex++;
}

// กำหนดความกว้างของคอลัมน์ A (ชื่อ-นามสกุล)
$sheet->getColumnDimension('A')->setWidth(22); // ตั้งความกว้างเป็น 20 หรือปรับได้ตามความเหมาะสม

// กำหนดความกว้างของคอลัมน์วันที่
$columnWidth = 4; // ปรับความกว้างตามต้องการ
for ($i = 2; $i <= $days_in_month + 1; $i++) { // เฉพาะคอลัมน์วันที่
    $columnLetter = Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($columnLetter)->setWidth($columnWidth);
}



// ตั้งชื่อไฟล์
$filename = "Monthly_Report_{$selected_check_type}_{$grade_name}_{$room_name}_{$selected_month}.xlsx";

// กำหนด Header สำหรับดาวน์โหลดไฟล์
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// ส่งออกไฟล์
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
