<?php
include '../../config/ConnectDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gradeId = $_POST['grade_id'];

    if (!empty($gradeId)) {
        // Check if there are students in the grade
        $stmtCheckStudents = $conn->prepare('SELECT COUNT(*) AS total_students FROM student WHERE id_grade = ?');
        $stmtCheckStudents->bind_param('i', $gradeId);
        $stmtCheckStudents->execute();
        $result = $stmtCheckStudents->get_result();
        $row = $result->fetch_assoc();

        if ($row['total_students'] > 0) {
            // Delete `check_in` records
            $stmtCheckIn = $conn->prepare('DELETE FROM check_in WHERE id_student IN (SELECT id FROM student WHERE id_grade = ?)');
            $stmtCheckIn->bind_param('i', $gradeId);
            $stmtCheckIn->execute();

            // Delete students
            $stmtStudent = $conn->prepare('DELETE FROM student WHERE id_grade = ?');
            $stmtStudent->bind_param('i', $gradeId);
            $stmtStudent->execute();

            echo json_encode([
                'success' => true,
                'deletedStudents' => $stmtStudent->affected_rows,
            ]);
        } else {
            // No students in the grade
            echo json_encode([
                'success' => false,
                'message' => 'ไม่มีนักเรียนในชั้นนี้',
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Grade ID is missing.',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}
?>
