<?php
include '../../config/ConnectDB.php';

$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$surname = isset($_POST['surname']) ? mysqli_real_escape_string($conn, $_POST['surname']) : '';
$id_room = isset($_POST['id_room']) ? $_POST['id_room'] : '';
$id_admin = isset($_POST['id_admin']) ? $_POST['id_admin'] : '';

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_room) || empty($id_admin)) {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!',
        'type' => 'warning'
    ]);
    exit;

} else {
    // ตรวจสอบว่ามีชื่อคุณครูในฐานข้อมูลแล้วหรือยัง
    $sql_check = "SELECT * FROM `teacher` WHERE name = '$name' AND surname = '$surname'";
    $result_check = mysqli_query($conn, $sql_check);

    if (!$result_check) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ข้อผิดพลาดในการตรวจสอบข้อมูลคุณครู: ' . mysqli_error($conn),
            'type' => 'error'
        ]);
        exit;
    }

    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => "ชื่อคุณครู '$name $surname' มีอยู่แล้วในระบบ",
            'type' => 'error'
        ]);
        exit;
    } else {
        date_default_timezone_set('Asia/Bangkok');
        $created_at = date('Y-m-d H:i:s');

        // เพิ่มข้อมูลครูใหม่
        $sql_insert = "INSERT INTO teacher (name, surname, id_room, id_admin, created_at) VALUES ('$name', '$surname', '$id_room', '$id_admin', '$created_at')";
        $insert_result = mysqli_query($conn, $sql_insert);

        if ($insert_result) {
            echo json_encode([
                'title' => 'สำเร็จ!',
                'message' => 'เพิ่มข้อมูลคุณครูสำเร็จ!',
                'type' => 'success'
            ]);
        } else {
            echo json_encode([
                'title' => 'เกิดข้อผิดพลาด!',
                'message' => 'ไม่สามารถเพิ่มข้อมูลคุณครูได้: ' . mysqli_error($conn),
                'type' => 'error'
            ]);
        }
    }
}

$conn->close();
?>
