<?php
include '../../config/ConnectDB.php';

$data = [];

// Fetch total admins
$adminQuery = "SELECT COUNT(*) AS totalAdmins FROM admin";
$adminResult = $conn->query($adminQuery);
$data['totalAdmins'] = $adminResult->fetch_assoc()['totalAdmins'];

// Fetch total teachers
$teacherQuery = "SELECT COUNT(*) AS totalTeachers FROM teacher";
$teacherResult = $conn->query($teacherQuery);
$data['totalTeachers'] = $teacherResult->fetch_assoc()['totalTeachers'];

// Fetch total students
$studentQuery = "SELECT COUNT(*) AS totalStudents FROM student";
$studentResult = $conn->query($studentQuery);
$data['totalStudents'] = $studentResult->fetch_assoc()['totalStudents'];

echo json_encode($data);
?>
