<?php
include '../../config/ConnectDB.php';

header('Content-Type: application/json'); // กำหนดให้เป็น JSON

if (isset($_POST['grade_id'])) {
    $grade_id = $_POST['grade_id'];

    $check_sql = "SELECT COUNT(*) AS student_count FROM student WHERE id_grade = $grade_id";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();

    if ($check_row['student_count'] > 0) {
        echo json_encode([
            'title' => 'เกิดข้อผิดพลาด!',
            'message' => 'ไม่สามารถลบชั้นเรียนได้เนื่องจากมีนักเรียนที่อยู่ชั้นเรียนนี้',
            'type' => 'error'
        ]);
        exit;
    } else {
        $delete_sql = "DELETE FROM grade_level WHERE id = $grade_id";

        if ($conn->query($delete_sql) === TRUE) {
            echo json_encode([
                'title' => 'สำเร็จ!',
                'message' => 'ลบชั้นเรียนสำเร็จ!',
                'type' => 'success'
            ]);
        } else {
            echo json_encode([
                'title' => 'เกิดข้อผิดพลาด!',
                'message' => 'ไม่สามารถลบข้อมูลได้',
                'type' => 'error'
            ]);
        }
        exit;
    }
} else {
    echo json_encode([
        'title' => 'เกิดข้อผิดพลาด!',
        'message' => 'ไม่สามารถระบุชั้นเรียนได้',
        'type' => 'error'
    ]);
    exit;
}

$conn->close();
