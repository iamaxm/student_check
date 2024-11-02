<?php
include '../../config/ConnectDB.php';

header('Content-Type: application/json');

$room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$grade_id = mysqli_real_escape_string($conn, $_POST['grade_id']);

// ตรวจสอบค่าว่าง
if (empty($name)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกชื่อห้องเรียน!',
        'type' => 'warning'
    ]);
    exit;
} elseif (empty($grade_id)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณาเลือกชั้นเรียน!',
        'type' => 'warning'
    ]);
    exit;
}

// ตรวจสอบว่าห้องเรียนซ้ำในชั้นเรียนเดียวกันหรือไม่ (ยกเว้นห้องที่กำลังแก้ไข)
$sql_check = "SELECT * FROM room WHERE name = '$name' AND grade_id = '$grade_id' AND id != '$room_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => "ชื่อห้องเรียน '$name' ในชั้นเรียนนี้มีอยู่แล้วในระบบ",
        'type' => 'error'
    ]);
    exit;
}

// อัพเดตข้อมูลห้องเรียน
$sql_update = "UPDATE room SET name = '$name', grade_id = '$grade_id' WHERE id = '$room_id'";
if (mysqli_query($conn, $sql_update)) {
    echo json_encode([
        'title' => 'สำเร็จ!',
        'message' => 'แก้ไขห้องเรียนสำเร็จ!',
        'type' => 'success'
    ]);
} else {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'ไม่สามารถแก้ไขห้องเรียนได้'. mysqli_error($conn),
        'type' => 'error'
    ]);
}

$conn->close();
?>
