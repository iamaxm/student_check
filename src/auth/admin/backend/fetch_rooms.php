<?php
include '../../config/ConnectDB.php';

$grade_id = isset($_POST['grade_id']) ? $_POST['grade_id'] : '';

if ($grade_id !== '') {
    $roomQuery = "SELECT id, name FROM room WHERE grade_id = '$grade_id'";
} else {
    $roomQuery = "SELECT id, name FROM room"; // ดึงห้องเรียนทั้งหมดเมื่อ grade_id ว่าง
}

$rooms = [];
$roomResult = $conn->query($roomQuery);
while ($row = $roomResult->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['rooms' => $rooms]);
?>
