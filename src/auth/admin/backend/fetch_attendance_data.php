<?php
include '../../config/ConnectDB.php';

$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$gradeId = isset($_POST['grade']) ? $_POST['grade'] : '';
$roomId = isset($_POST['room']) ? $_POST['room'] : '';
$studentId = isset($_POST['student']) ? $_POST['student'] : '';

$queryIn = "SELECT COUNT(*) AS totalIn FROM time_inout ti
            LEFT JOIN student s ON ti.id_student = s.id
            WHERE ti.status = 'in'";

if (!empty($startDate) && !empty($endDate)) {
    $queryIn .= " AND DATE(ti.created_at) BETWEEN '$startDate' AND '$endDate'";
}
if (!empty($gradeId)) {
    $queryIn .= " AND s.id_grade = '$gradeId'";
}
if (!empty($roomId)) {
    $queryIn .= " AND s.id_room = '$roomId'";
}
if (!empty($studentId)) {
    $queryIn .= " AND s.id = '$studentId'";
}

$resultIn = $conn->query($queryIn);
$totalIn = $resultIn->fetch_assoc()['totalIn'];

$queryOut = "SELECT COUNT(*) AS totalOut FROM time_inout ti
             LEFT JOIN student s ON ti.id_student = s.id
             WHERE ti.status = 'out'";

if (!empty($startDate) && !empty($endDate)) {
    $queryOut .= " AND DATE(ti.created_at) BETWEEN '$startDate' AND '$endDate'";
}
if (!empty($gradeId)) {
    $queryOut .= " AND s.id_grade = '$gradeId'";
}
if (!empty($roomId)) {
    $queryOut .= " AND s.id_room = '$roomId'";
}
if (!empty($studentId)) {
    $queryOut .= " AND s.id = '$studentId'";
}

$resultOut = $conn->query($queryOut);
$totalOut = $resultOut->fetch_assoc()['totalOut'];

echo json_encode(['totalIn' => $totalIn, 'totalOut' => $totalOut]);
?>
