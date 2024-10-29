<?php
include '../../config/ConnectDB.php';

// บังคับให้ output เป็น JSON
header('Content-Type: application/json');

if (isset($_POST['grade_id'])) {
    $grade_id = $_POST['grade_id'];

    $rooms = [];
    $teachers = [];

    // Query ดึงข้อมูลห้องเรียนที่เชื่อมกับชั้นเรียนที่เลือก
    $room_sql = "SELECT id, name FROM room WHERE grade_id = ?";
    $stmt = $conn->prepare($room_sql);
    if ($stmt) {
        $stmt->bind_param("i", $grade_id);
        $stmt->execute();
        $room_result = $stmt->get_result();
        while ($row = $room_result->fetch_assoc()) {
            $rooms[] = $row;
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Room Query Prepare failed: ' . $conn->error]);
        exit;
    }

    // Query ดึงข้อมูลครูที่เชื่อมกับห้องเรียนในชั้นเรียนที่เลือก
    $teacher_sql = "SELECT teacher.id, teacher.name, teacher.surname FROM teacher 
                    INNER JOIN room ON teacher.id_room = room.id 
                    WHERE room.grade_id = ?";
    $stmt = $conn->prepare($teacher_sql);
    if ($stmt) {
        $stmt->bind_param("i", $grade_id);
        $stmt->execute();
        $teacher_result = $stmt->get_result();
        while ($row = $teacher_result->fetch_assoc()) {
            $teachers[] = $row;
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Teacher Query Prepare failed: ' . $conn->error]);
        exit;
    }

    // ส่งข้อมูลกลับในรูปแบบ JSON
    echo json_encode(['rooms' => $rooms, 'teachers' => $teachers]);
} else {
    // กรณีไม่ได้ส่ง grade_id มาจาก Client
    echo json_encode(['error' => 'Grade ID not set']);
}

$conn->close();
?>
