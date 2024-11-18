<?php
@session_start();
include '../../config/ConnectDB.php';

if (isset($_POST['grade_id'])) {
    $grade_id = $_POST['grade_id'];

    $sql = "SELECT * FROM room WHERE grade_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $grade_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rooms = [];

        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }

        echo json_encode($rooms);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'SQL Error: ' . $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>
