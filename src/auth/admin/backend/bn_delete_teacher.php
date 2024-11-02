<?php
include '../../config/ConnectDB.php';
header('Content-Type: application/json');
if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];

    // ลบข้อมูลครู
    $sql_delete = "DELETE FROM teacher WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $teacher_id);

    if ($stmt->execute()) {
        echo json_encode([
            'title' => 'สำเร็จ!',
            'message' => 'ลบข้อมูลคุณครูสำเร็จ!',
            'type' => 'success'
        ]);
    } else {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ไม่สามารถลบข้อมูลได้'. mysqli_error($conn),
            'type' => 'error'
        ]);
    }

    $stmt->close();
}

$conn->close();
?>
