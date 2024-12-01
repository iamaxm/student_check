<?php

include '../../config/ConnectDB.php';

$sql = "SELECT * FROM check_time_settings LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "ไม่พบข้อมูล"]);
}

$conn->close();
?>
