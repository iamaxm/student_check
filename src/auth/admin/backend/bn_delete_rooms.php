<?php
include '../../config/ConnectDB.php';

header('Content-Type: application/json');

if (isset($_POST['room_id'])) {
    $room_id = mysqli_real_escape_string($conn, $_POST['room_id']);

    // ตรวจสอบว่ามีนักเรียนอยู่ในห้องนี้หรือไม่
    $check_sql = "SELECT COUNT(*) AS student_count FROM student WHERE id_room = $room_id";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();

    if ($check_row['student_count'] > 0) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ไม่สามารถลบห้องเรียนได้เนื่องจากมีนักเรียนที่อยู่ในห้องนี้',
            'type' => 'error'
        ]);
    } else {
        // ลบข้อมูลห้องเรียน
        $delete_sql = "DELETE FROM room WHERE id = $room_id";
        if ($conn->query($delete_sql) === TRUE) {
            echo json_encode([
                'title' => 'สำเร็จ!',
                'message' => 'ลบห้องเรียนสำเร็จ!',
                'type' => 'success'
            ]);
        } else {
            echo json_encode([
                'title' => 'เกิดข้อผิดพลาด!',
                'message' => 'ไม่สามารถลบห้องเรียนได้',
                'type' => 'error'
            ]);
        }
    }
} else {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'ไม่สามารถระบุห้องเรียนได้',
        'type' => 'error'
    ]);
}

$conn->close();
?>
