<?php
@session_start();
include '../../config/ConnectDB.php';

if (isset($_POST['current_grade_id'], $_POST['current_room_id'], $_POST['new_grade_id'], $_POST['new_room_id'])) {
    $current_grade_id = $_POST['current_grade_id'];
    $current_room_id = $_POST['current_room_id'];
    $new_grade_id = $_POST['new_grade_id'];
    $new_room_id = $_POST['new_room_id'];

    if ($current_grade_id == $new_grade_id && $current_room_id == $new_room_id) {
        echo json_encode([
            'success' => false,
            'message' => 'ชั้นเรียนและห้องเรียนใหม่ต้องไม่เหมือนกับชั้นเรียนและห้องเรียนปัจจุบัน'
        ]);
        exit;
    }

    $sql = "UPDATE student SET id_grade = ?, id_room = ? WHERE id_grade = ? AND id_room = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iiii", $new_grade_id, $new_room_id, $current_grade_id, $current_room_id);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'เลื่อนชั้นเรียนและห้องเรียนสำเร็จ!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $stmt->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'SQL Error: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
}
?>
