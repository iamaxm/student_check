<?php
include '../../config/ConnectDB.php';

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // ตรวจสอบสถานะล่าสุดของนักเรียน
    $status_check_sql = "SELECT status FROM time_inout WHERE id_student = '$student_id' ORDER BY created_at DESC LIMIT 1";
    $status_result = $conn->query($status_check_sql);
    $latest_status = ($status_result->num_rows > 0) ? $status_result->fetch_assoc()['status'] : 'out';

    // สลับสถานะ
    $new_status = ($latest_status === 'in') ? 'out' : 'in';

    // บันทึกสถานะใหม่ในฐานข้อมูล
    $insert_sql = "INSERT INTO time_inout (id_student, status, created_at) VALUES ('$student_id', '$new_status', NOW())";
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
    echo json_encode(['message' => "เกิดข้อผิดพลาด: ไม่พบรหัสนักเรียน"]);
}

$conn->close();
?>
