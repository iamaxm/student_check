<?php
@session_start();
include '../../config/ConnectDB.php';

// รับข้อมูลจากฟอร์ม
$admin_id = $_POST['admin_id'];
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// ฟังก์ชันสำหรับตอบกลับ JSON
function jsonResponse($title, $message, $type, $timer = 1500) {
    echo json_encode([
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'timer' => $timer
    ]);
}

// ตรวจสอบค่าว่าง
if (empty($username)) {
    jsonResponse("เกิดข้อผิดพลาด!", "กรุณากรอก username!", "warning");
    exit;
}

// ตรวจสอบว่ามีแอดมินที่มีชื่อนี้อยู่ในระบบแล้วหรือยัง (ยกเว้น ID ปัจจุบัน)
$sql_check = "SELECT * FROM `admin` WHERE username = '$username' AND id != '$admin_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    jsonResponse("เกิดข้อผิดพลาด!", "ชื่อผู้ใช้ '$username' มีอยู่แล้วในระบบ", "error");
    exit;
} else {
    // ตรวจสอบว่าผู้ใช้ได้ใส่รหัสผ่านใหม่หรือไม่
    if (!empty($password)) {
        if (strlen($password) < 8) {
            jsonResponse("เกิดข้อผิดพลาด!", "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร", "warning");
            exit;
        } else {
            // แฮชรหัสผ่านใหม่ก่อนที่จะอัปเดต
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // อัปเดตทั้ง username และ password
            $sql_update = "UPDATE admin SET username = '$username', password = '$hashed_password' WHERE id = '$admin_id'";
        }
    } else {
        // อัปเดตเฉพาะ username โดยไม่เปลี่ยน password
        $sql_update = "UPDATE admin SET username = '$username' WHERE id = '$admin_id'";
    }

    if (mysqli_query($conn, $sql_update)) {
        jsonResponse("สำเร็จ!", "แก้ไขข้อมูลแอดมินสำเร็จ!", "success");
    } else {
        jsonResponse("เกิดข้อผิดพลาด!", "ไม่สามารถแก้ไขข้อมูลได้", "error");
    }
}

$conn->close();
?>