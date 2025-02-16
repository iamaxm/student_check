<?php
include '../../config/ConnectDB.php';
header('Content-Type: application/json');

$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$surname = isset($_POST['surname']) ? mysqli_real_escape_string($conn, $_POST['surname']) : '';
$prefix = isset($_POST['prefix']) ? mysqli_real_escape_string($conn, $_POST['prefix']) : '';
$id_grade = isset($_POST['id_grade']) ? mysqli_real_escape_string($conn, $_POST['id_grade']) : '';
$id_room = isset($_POST['id_room']) ? mysqli_real_escape_string($conn, $_POST['id_room']) : '';
$id_teacher = isset($_POST['id_teacher']) ? mysqli_real_escape_string($conn, $_POST['id_teacher']) : '';
$id_admin = isset($_POST['id_admin']) ? mysqli_real_escape_string($conn, $_POST['id_admin']) : '';

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_grade) || empty($id_room) || empty($id_teacher) || empty($id_admin)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!',
        'type' => 'warning'
    ]);
    exit;
}

// ตรวจสอบว่ามีชื่อในฐานข้อมูลแล้วหรือยัง
$sql_check = "SELECT * FROM `student` WHERE name = '$name' AND surname = '$surname'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => "ชื่อนักเรียน '$name $surname' มีอยู่แล้วในระบบ",
        'type' => 'error'
    ]);
    exit;
}

// ตั้งค่ารูปแบบเวลา
date_default_timezone_set('Asia/Bangkok');
$created_at = date('Y-m-d H:i:s');

$profile_image = '';
if (!empty($_FILES['profile_image']['name'])) {
    $target_dir = "../../../uploads/";  // โฟลเดอร์อัปโหลด
    $file_name = basename($_FILES['profile_image']['name']);
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    // ตรวจสอบว่านามสกุลไฟล์ถูกต้องหรือไม่
    if (!in_array(strtolower($file_ext), $allowed_exts)) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'รูปภาพต้องเป็นไฟล์ JPG, JPEG, PNG หรือ GIF เท่านั้น',
            'type' => 'error'
        ]);
        exit;
    }

    // ตั้งชื่อไฟล์ใหม่ให้ไม่ซ้ำกัน
    $new_file_name = "student_" . time() . "." . $file_ext;
    $target_file = $target_dir . $new_file_name;

    // อัปโหลดไฟล์ไปยังโฟลเดอร์
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
        $profile_image = $new_file_name;
    } else {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ไม่สามารถอัปโหลดรูปภาพได้',
            'type' => 'error'
        ]);
        exit;
    }
}

// เพิ่มข้อมูลใหม่พร้อมรูปภาพ
$sql_insert = "INSERT INTO student (prefix, name, surname, id_grade, id_room, id_teacher, id_admin, profile_image, created_at) VALUES ('$prefix', '$name', '$surname', '$id_grade', '$id_room', '$id_teacher', '$id_admin', '$profile_image', '$created_at')";

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

$conn->close();
?>
