<?php
include '../../config/ConnectDB.php';

// บังคับให้ output เป็น JSON
header('Content-Type: application/json');

if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];

    // Query ดึงข้อมูลครูที่เชื่อมกับห้องเรียนที่เลือก
    $teacher_sql = "SELECT id, name, surname FROM teacher WHERE id_room = ?";
    $stmt = $conn->prepare($teacher_sql);
    if ($stmt) {
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $teacher_result = $stmt->get_result();
        $teachers = [];
        while ($row = $teacher_result->fetch_assoc()) {
            $teachers[] = $row;
        }
        $stmt->close();

        // ส่งข้อมูลกลับในรูปแบบ JSON
        echo json_encode(['teachers' => $teachers]);
    } else {
        echo json_encode(['error' => 'Teacher Query Prepare failed: ' . $conn->error]);
    }
} else {
    // กรณีไม่ได้ส่ง room_id มาจาก Client
    echo json_encode(['error' => 'Room ID not set']);
}

$conn->close();
?>
