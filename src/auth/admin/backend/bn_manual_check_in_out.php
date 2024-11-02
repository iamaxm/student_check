<?php
@session_start();
include '../../config/ConnectDB.php';

// ตั้ง timezone เป็น Asia/Bangkok
date_default_timezone_set("Asia/Bangkok");

if (isset($_POST['student_id']) && isset($_POST['status']) && isset($_POST['datetime'])) {
    $student_id = $_POST['student_id'];
    $new_status = $_POST['status'];
    $selected_datetime = $_POST['datetime']; // รูปแบบวันที่และเวลา เช่น "2023-10-30 13:30"

    // ตรวจสอบว่านักเรียนมีอยู่ในฐานข้อมูลหรือไม่
    $student_check_sql = "SELECT * FROM student WHERE id = '$student_id'";
    $student_check_result = $conn->query($student_check_sql);

    if ($student_check_result->num_rows == 0) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "เกิดข้อผิดพลาด: ไม่พบรหัสนักเรียน"]);
        exit;
    }

    // เพิ่มวินาทีปัจจุบันเข้าไปในเวลาเลือก
    $seconds = date("s"); // วินาทีปัจจุบัน
    $created_at = $selected_datetime . ':' . $seconds; // รวมวินาทีในรูปแบบ "Y-m-d H:i:s"

    // บันทึกสถานะใหม่ในฐานข้อมูล
    $insert_sql = "INSERT INTO time_inout (id_student, status, created_at) VALUES ('$student_id', '$new_status', '$created_at')";
    if ($conn->query($insert_sql) === TRUE) {
        $message = ($new_status === 'in') ? "เช็คชื่อเข้าเรียนเรียบร้อย!" : "เช็คชื่อออกเรียนเรียบร้อย!";
        header('Content-Type: application/json');
        echo json_encode(['message' => $message]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['message' => "เกิดข้อผิดพลาดในการบันทึกข้อมูล"]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['message' => "เกิดข้อผิดพลาด: ไม่พบข้อมูลนักเรียนหรือสถานะ"]);
}

$conn->close();
