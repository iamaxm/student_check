<?php
include '../../config/ConnectDB.php';
header('Content-Type: application/json');
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // ลบข้อมูล
    $sql_delete = "DELETE FROM student WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        echo json_encode([
            'title' => 'สำเร็จ!',
            'message' => 'ลบข้อมูลนักเรียนสำเร็จ!',
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
