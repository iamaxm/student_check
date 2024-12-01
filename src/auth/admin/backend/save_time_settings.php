<?php
include '../../config/ConnectDB.php';

$check_in_start = $_POST['check_in_start'];
$check_in_end = $_POST['check_in_end'];
$check_out_start = $_POST['check_out_start'];
$check_out_end = $_POST['check_out_end'];

$sql = "UPDATE check_time_settings SET 
        check_in_start = '$check_in_start', 
        check_in_end = '$check_in_end', 
        check_out_start = '$check_out_start', 
        check_out_end = '$check_out_end'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>
