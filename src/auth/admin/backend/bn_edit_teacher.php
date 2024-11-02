<?php
include '../../config/ConnectDB.php';
header('Content-Type: application/json');
$teacher_id = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['teacher_id']) : '';
$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']): '';
$surname = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['surname']): '';
$id_room = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['id_room']): '';

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_room)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!',
        'type' => 'warning'
    ]);
    exit;
}

// ตรวจสอบว่ามีชื่อชั้นเรียนในฐานข้อมูลแล้วหรือยัง
$sql_check = "SELECT * FROM `teacher` WHERE  name = '$name' && surname = '$surname'AND id != '$teacher_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => "ชื่อคุณครู '$name $surname' มีอยู่แล้วในระบบ",
        'type' => 'error'
    ]);
    exit;
} else {
// แก้ไขข้อมูลครู
$sql_update = "UPDATE teacher SET name = '$name', surname = '$surname', id_room = '$id_room' WHERE id = '$teacher_id'";
if (mysqli_query($conn, $sql_update)) {
    echo json_encode([
        'title' => 'สำเร็จ!',
        'message' => 'แก้ไขข้อมูลคุณครูสำเร็จ!',
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
