<?php
include '../../config/ConnectDB.php';

// Helper function for JSON response
function jsonResponse($title, $message, $type, $redirect = null, $timer = 1500) {
    header('Content-Type: application/json'); // Ensure JSON output
    echo json_encode([
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'redirect' => $redirect,
        'timer' => $timer
    ]);
}

// Process input
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

// Validation checks
if (empty($username)) {
    jsonResponse("เกิดข้อผิดพลาด!", "กรุณากรอก username!", "warning");
    exit;
} else if (empty($password)) {
    jsonResponse("เกิดข้อผิดพลาด!", "กรุณากรอก password!", "warning");
    exit;
} else if (empty($cpassword)) {
    jsonResponse("เกิดข้อผิดพลาด!", "กรุณายืนยัน password!", "warning");
    exit;
} else if (strlen($password) < 8) {
    jsonResponse("เกิดข้อผิดพลาด!", "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร", "warning");
    exit;
} else if ($password !== $cpassword) {
    jsonResponse("เกิดข้อผิดพลาด!", "รหัสผ่านไม่ตรงกัน!", "warning");
    exit;
}

// Check for existing username
$sqli_username = "SELECT * FROM `admin` WHERE `username` = '$username'";
$result_username = mysqli_query($conn, $sqli_username);

if (mysqli_num_rows($result_username) > 0) {
    jsonResponse("เสียใจด้วย", "username '$username' มีผู้ใช้งานแล้ว", "error");
    exit;
}

// Insert new admin
date_default_timezone_set('Asia/Bangkok');
$created_at = date('Y-m-d H:i:s');
$save_data_sql = "INSERT INTO `admin`(`username`, `password`, `created_at`) VALUES ('$username','$password','$created_at')";

if (mysqli_query($conn, $save_data_sql)) {
    jsonResponse("ยินดีด้วย", "สร้างแอดมินสำเร็จ!", "success", "index.php");
} else {
    jsonResponse("เกิดข้อผิดพลาด", "ไม่สามารถเพิ่มแอดมินได้", "error");
}

$conn->close();
?>
