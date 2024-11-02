<?php
include '../../config/ConnectDB.php';
header('Content-Type: application/json');
// รับข้อมูลจากฟอร์ม
$student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';
$prefix = isset($_POST['prefix']) ? mysqli_real_escape_string($conn, $_POST['prefix']) : '';
$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$surname = isset($_POST['surname']) ? mysqli_real_escape_string($conn, $_POST['surname']) : '';
$id_grade = isset($_POST['id_grade']) ? mysqli_real_escape_string($conn, $_POST['id_grade']) : '';
$id_room = isset($_POST['id_room']) ? mysqli_real_escape_string($conn, $_POST['id_room']) : '';
$id_teacher = isset($_POST['id_teacher']) ? mysqli_real_escape_string($conn, $_POST['id_teacher']) : '';

// ตรวจสอบค่าว่าง
if (empty($prefix) || empty($name) || empty($surname) || empty($id_grade) || empty($id_room) || empty($id_teacher)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!',
        'type' => 'warning'
    ]);
    exit;
}

// ตรวจสอบว่ามีนักเรียนที่มีชื่อนี้อยู่ในระบบแล้วหรือยัง (ยกเว้น ID ปัจจุบัน)
$sql_check = "SELECT * FROM `student` WHERE name = '$name' AND surname = '$surname' AND id != '$student_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => "ชื่อนักเรียน '$name $surname' มีอยู่แล้วในระบบ",
        'type' => 'error'
    ]);
    exit;
} else {
    // อัปเดตข้อมูลนักเรียน
    $sql_update = "UPDATE student SET prefix = '$prefix', name = '$name', surname = '$surname', id_grade = '$id_grade', id_room = '$id_room', id_teacher = '$id_teacher' WHERE id = '$student_id'";
    
    if (mysqli_query($conn, $sql_update)) {
        echo json_encode([
            'title' => 'สำเร็จ!',
            'message' => 'แก้ไขข้อมูลนักเรียนสำเร็จ!',
            'type' => 'success'
        ]);
    } else {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ไม่สามารถแก้ไขข้อมูลได้'. mysqli_error($conn),
            'type' => 'error'
        ]);
    }
}

$conn->close();
?>
