<?php
session_start();
include('config/ConnectDB.php');

// if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
//     $redirectUrl = "admin/index.php?id=dashboard";
//     echo json_encode([
//         'title' => 'เกิดข้อผิดพลาด!',
//         'message' => 'คุณได้เข้าสู่ระบบแล้ว!',
//         'type' => 'warning',
//         'redirectUrl' => $redirectUrl
//     ]);
//     exit();
// }

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ค้นหาผู้ใช้ในฐานข้อมูลโดยค้นหาจาก username เท่านั้น
    $sql = "SELECT * FROM `admin` WHERE `username` = '$username'";
    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        // ตรวจสอบรหัสผ่านที่ผู้ใช้กรอกเข้ามากับรหัสผ่านที่ถูกแฮชในฐานข้อมูล
        if (password_verify($password, $row['password'])) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            echo json_encode([
                'title' => 'สำเร็จ!',
                'message' => 'เข้าสู่ระบบสำเร็จ!',
                'type' => 'success'
            ]);
            exit();
        } else {
            echo json_encode([
                'title' => 'เกิดข้อผิดพลาด!',
                'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!',
                'type' => 'warning'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!',
            'type' => 'warning'
        ]);
        exit();
    }
}
?>