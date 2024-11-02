<?php
include '../../config/ConnectDB.php';

header('Content-Type: application/json');

$name = mysqli_real_escape_string($conn, $_POST['name']);
$grade_id = mysqli_real_escape_string($conn, $_POST['grade_id']);

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

$sql_check = "SELECT * FROM room WHERE name = '$name' AND grade_id = '$grade_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => "ชื่อห้องเรียน '$name' มีอยู่แล้วในระบบ",
        'type' => 'error'
    ]);
    exit;
}

date_default_timezone_set('Asia/Bangkok');
$created_at = date('Y-m-d H:i:s');
$sql_insert = "INSERT INTO room (name, grade_id, created_at) VALUES ('$name', '$grade_id', '$created_at')";

if (mysqli_query($conn, $sql_insert)) {
    echo json_encode([
        'title' => 'สำเร็จ!',
        'message' => 'สร้างห้องเรียนสำเร็จ!',
        'type' => 'success'
    ]);
} else {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'ไม่สามารถเพิ่มห้องเรียนได้: ' . mysqli_error($conn),
        'type' => 'error'
    ]);
}

$conn->close();
?>
