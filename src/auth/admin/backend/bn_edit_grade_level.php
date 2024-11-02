<?php
include '../../config/ConnectDB.php';

$grade_id = $_POST['grade_id'];
$grade_level = mysqli_real_escape_string($conn, $_POST['grade_level']);

$response = [
    "title" => "เกิดข้อผิดพลาด!",
    "message" => "ไม่สามารถแก้ไขข้อมูลได้",
    "type" => "error"
];

if (!empty($grade_level)) {
    $sql_check = "SELECT * FROM grade_level WHERE grade_level = '$grade_level' AND id != $grade_id";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) == 0) {
        $update_sql = "UPDATE grade_level SET grade_level = '$grade_level' WHERE id = $grade_id";
        if (mysqli_query($conn, $update_sql)) {
            $response = [
                "title" => "สำเร็จ!",
                "message" => "แก้ไขชั้นเรียนสำเร็จ",
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
