<?php
include '../../config/ConnectDB.php';

$grade_id = isset($_POST['grade_id']) ? $_POST['grade_id'] : '';
$room_id = isset($_POST['room_id']) ? $_POST['room_id'] : '';
$students = [];

$studentQuery = "SELECT id, prefix, name, surname FROM student WHERE 1=1";
if (!empty($grade_id)) {
    $studentQuery .= " AND id_grade = '$grade_id'";
}
if (!empty($room_id)) {
    $studentQuery .= " AND id_room = '$room_id'";
}

$studentResult = $conn->query($studentQuery);
while ($row = $studentResult->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode(['students' => $students]);
?>
