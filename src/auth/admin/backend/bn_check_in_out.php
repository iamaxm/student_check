<?php
@session_start();
include '../../config/ConnectDB.php';

// ตั้ง timezone เป็น Asia/Bangkok
date_default_timezone_set("Asia/Bangkok");

if (isset($_POST['student_id']) && isset($_POST['hour'])) {
    $student_id = $_POST['student_id'];
    $hour = (float) $_POST['hour']; // รับค่า hour เป็น float เช่น 6.00, 16.50

    // ตรวจสอบค่า hour ที่ได้รับ
    error_log("Current Hour (Decimal): " . $hour);

    // ตรวจสอบว่านักเรียนมีอยู่ในฐานข้อมูลหรือไม่
    $student_check_sql = "SELECT * FROM student WHERE id = '$student_id'";
    $student_check_result = $conn->query($student_check_sql);

    if ($student_check_result->num_rows == 0) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "เกิดข้อผิดพลาด: ไม่พบรหัสนักเรียน"]);
        exit;
    }

    // ตรวจสอบสถานะล่าสุดของนักเรียน
    $status_check_sql = "SELECT status FROM time_inout WHERE id_student = '$student_id' ORDER BY created_at DESC LIMIT 1";
    $status_result = $conn->query($status_check_sql);
    $latest_status = ($status_result->num_rows > 0) ? $status_result->fetch_assoc()['status'] : 'out';

    // กำหนดสถานะใหม่ตามช่วงเวลาและสถานะล่าสุด โดยใช้เวลาเต็มแบบไทย
    $new_status = null;
    if ($hour >= 6.00 && $hour <= 8.00 && $latest_status == 'out') {
        $new_status = 'in';
    } elseif ($hour >= 16.00 && $hour <= 17.00 && $latest_status == 'in') {
        $new_status = 'out';
    }

    if ($new_status) {
        // บันทึกสถานะใหม่ในฐานข้อมูล
        $insert_sql = "INSERT INTO time_inout (id_student, status, created_at) VALUES ('$student_id', '$new_status', NOW())";
        if ($conn->query($insert_sql) === TRUE) {
            $message = ($new_status === 'in') ? "เช็คชื่อเข้าเรียนเรียบร้อย!" : "เช็คชื่อออกเรียนเรียบร้อย!";
            header('Content-Type: application/json');
            echo json_encode(['message' => $message]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['message' => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['message' => "เกิดข้อผิดพลาด : นักเรียนคนนี้เช็คชื่อไปแล้ว"]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['message' => "เกิดข้อผิดพลาด : ไม่พบรหัสนักเรียน"]);
}

$conn->close();
