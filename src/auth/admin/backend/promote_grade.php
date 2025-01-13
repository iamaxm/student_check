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

    // Step 1: Find the teacher where id_room = new_room_id
    $teacherQuery = "SELECT id FROM teacher WHERE id_room = ?";
    $teacherStmt = $conn->prepare($teacherQuery);

    if ($teacherStmt) {
        $teacherStmt->bind_param("i", $new_room_id);
        $teacherStmt->execute();
        $teacherResult = $teacherStmt->get_result();

        if ($teacherResult->num_rows > 0) {
            $teacher = $teacherResult->fetch_assoc();
            $teacher_id = $teacher['id'];

            // Step 2: Update the student table with the teacher_id
            $studentUpdateQuery = "UPDATE student SET id_grade = ?, id_room = ?, id_teacher = ? WHERE id_grade = ? AND id_room = ?";
            $studentStmt = $conn->prepare($studentUpdateQuery);

            if ($studentStmt) {
                $studentStmt->bind_param("iiiii", $new_grade_id, $new_room_id, $teacher_id, $current_grade_id, $current_room_id);
                if ($studentStmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'เลื่อนชั้นเรียนและห้องเรียนสำเร็จ พร้อมอัปเดตครูสำเร็จ!'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'เกิดข้อผิดพลาดในการอัปเดตนักเรียน: ' . $studentStmt->error
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
                'message' => 'ไม่พบครูที่เกี่ยวข้องในห้องเรียนใหม่'
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
