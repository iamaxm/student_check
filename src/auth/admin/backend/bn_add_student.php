<?php
include '../../config/ConnectDB.php';

$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$surname = isset($_POST['surname']) ? mysqli_real_escape_string($conn, $_POST['surname']) : '';
$prefix = isset($_POST['prefix']) ? mysqli_real_escape_string($conn, $_POST['prefix']) : '';
$id_grade = isset($_POST['id_grade']) ? mysqli_real_escape_string($conn, $_POST['id_grade']) : '';
$id_room = isset($_POST['id_room']) ? mysqli_real_escape_string($conn, $_POST['id_room']) : '';
$id_teacher = isset($_POST['id_teacher']) ? mysqli_real_escape_string($conn, $_POST['id_teacher']) : '';
$id_admin = isset($_POST['id_admin']) ? mysqli_real_escape_string($conn, $_POST['id_admin']) : '';

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_grade) || empty($id_room)|| empty($id_teacher) || empty($id_admin)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!',
        'type' => 'warning'
    ]);
    exit;
} else {
    // ตรวจสอบว่ามีชื่อในฐานข้อมูลแล้วหรือยัง
    $sql_check = "SELECT * FROM `student` WHERE name = '$name' && surname = '$surname'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => "ชื่อนักเรียน '$name $surname' มีอยู่แล้วในระบบ",
            'type' => 'error'
        ]);
    } else {
        // วันที่และเวลาปัจจุบัน (ในโซนเวลาของไทย)
        date_default_timezone_set('Asia/Bangkok');
        $created_at = date('Y-m-d H:i:s');

        // เพิ่มข้อมูลใหม่
        $sql_insert = "INSERT INTO student (prefix, name, surname,id_grade, id_room,id_teacher, id_admin, created_at) VALUES ('$prefix','$name', '$surname','$id_grade', '$id_room','$id_teacher', '$id_admin', '$created_at')";
        if (mysqli_query($conn, $sql_insert)) {
            echo json_encode([
                'title' => 'สำเร็จ!',
                'message' => 'เพิ่มข้อมูลนักเรียนสำเร็จ!',
                'type' => 'success'
            ]);
        } else {
            echo json_encode([
                'title' => 'เกิดข้อผิดพลาด!',
                'message' => 'ไม่สามารถเพิ่มข้อมูลได้: ' . mysqli_error($conn),
                'type' => 'error'
            ]);
        }
    }
}


$conn->close();
?>