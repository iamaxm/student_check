<?php
header('Content-Type: application/json');
include '../../config/ConnectDB.php';

// รับข้อมูลจาก AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$student_id = $data['student_id'];
$date = $data['date'];
$new_status = $data['new_status'];
$check_type = $data['check_type'];

// Debug: ตรวจสอบค่าที่ได้รับ
error_log("Student ID: $student_id, Date: $date, New Status: $new_status, Check Type: $check_type");

// ดึงการตั้งค่าเวลา
$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

if (!$time_settings) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการตั้งค่าเวลา']);
    exit;
}

// ตั้งค่าเวลาเริ่มต้น
$check_time = null;

if ($check_type === 'check_in') {
    if ($new_status === 'present') {
        $check_time = $time_settings['check_in_end']; // มาเรียน
    } elseif ($new_status === 'late') {
        $check_time = date('H:i:s', strtotime($time_settings['check_in_end'] . ' +5 minutes')); // มาสาย
    } elseif ($new_status === 'absent') {
        $check_time = null; // ขาดเรียน
    }
    $time_field = 'in_at';
} elseif ($check_type === 'check_out') {
    if ($new_status === 'out') {
        $check_time = $time_settings['check_out_end']; // ออกเรียน
    } elseif ($new_status === 'absent') {
        $check_time = null; // ไม่ออกเรียน
    }
    $time_field = 'out_at';
}

// Debug: แสดง SQL query
if ($check_time) {
    $sql = "UPDATE check_in SET $time_field = '$date $check_time' WHERE id_student = '$student_id' AND (DATE($time_field) = '$date' OR $time_field IS NULL)";
} else {
    $sql = "UPDATE check_in SET $time_field = NULL WHERE id_student = '$student_id' AND (DATE($time_field) = '$date' OR $time_field IS NULL)";
}
error_log("SQL Query: $sql");

// รัน SQL query
if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
