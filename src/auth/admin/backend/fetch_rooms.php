<?php
include '../../config/ConnectDB.php';

$grade_id = $_POST['grade_id'];
$rooms = [];

$roomQuery = "SELECT id, name FROM room WHERE grade_id = '$grade_id'";
$roomResult = $conn->query($roomQuery);
while ($row = $roomResult->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['rooms' => $rooms]);
?>
