<?php
include '../../config/ConnectDB.php';

function jsonResponse($title, $message, $type, $timer = 1500) {
    echo json_encode([
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'timer' => $timer
    ]);
}

if (isset($_POST['admin_id'])) {
    $admin_id = $_POST['admin_id'];

    $delete_sql = "DELETE FROM admin WHERE id = $admin_id";
    if ($conn->query($delete_sql) === TRUE) {
        jsonResponse("สำเร็จ!", "ลบแอดมินสำเร็จ!", "success");
    } else {
        jsonResponse("เกิดข้อผิดพลาด!", "ไม่สามารถลบแอดมินได้ กรุณาลองใหม่อีกครั้ง", "error");
    }
} else {
    jsonResponse("เกิดข้อผิดพลาด!", "ไม่มีข้อมูลแอดมินที่ต้องการลบ", "error");
}

$conn->close();
?>
