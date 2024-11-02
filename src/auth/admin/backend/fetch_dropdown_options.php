<?php
include '../../config/ConnectDB.php';

$data = [];

// Fetch Grades
$gradeQuery = "SELECT id, grade_level FROM grade_level";
$gradeResult = $conn->query($gradeQuery);
$data['grades'] = [];
while ($row = $gradeResult->fetch_assoc()) {
    $data['grades'][] = $row;
}

// Fetch Rooms
$roomQuery = "SELECT id, name FROM room";
$roomResult = $conn->query($roomQuery);
$data['rooms'] = [];
while ($row = $roomResult->fetch_assoc()) {
    $data['rooms'][] = $row;
}

// Fetch Students
$studentQuery = "SELECT id, prefix, name, surname FROM student";
$studentResult = $conn->query($studentQuery);
$data['students'] = [];
while ($row = $studentResult->fetch_assoc()) {
    $data['students'][] = $row;
}

echo json_encode($data);
?>
