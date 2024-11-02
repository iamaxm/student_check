<?php
include '../../config/ConnectDB.php';

$grade_level = mysqli_real_escape_string($conn, $_POST['grade_level']);

$response = [
    "title" => "เกิดข้อผิดพลาด!",
    "message" => "ไม่สามารถเพิ่มข้อมูลได้",
    "type" => "error"
];

if (!empty($grade_level)) {
    $sql_check = "SELECT * FROM grade_level WHERE grade_level = '$grade_level'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) == 0) {
        date_default_timezone_set('Asia/Bangkok');
        $created_at = date('Y-m-d H:i:s');
        $sql_insert = "INSERT INTO grade_level (grade_level, created_at) VALUES ('$grade_level', '$created_at')";

        if (mysqli_query($conn, $sql_insert)) {
            $response = [
                "title" => "สำเร็จ!",
                "message" => "เพิ่มชั้นเรียนสำเร็จ",
                "type" => "success"
            ];
        }
    } else {
        $response["message"] = "ชื่อชั้นเรียนนี้มีอยู่แล้ว";
    }
}

echo json_encode($response);
$conn->close();
?>
