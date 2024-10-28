<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

$teacher_id = $_POST['teacher_id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$surname = mysqli_real_escape_string($conn, $_POST['surname']);
$id_room = $_POST['id_room'];

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_room)) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "กรุณากรอกข้อมูลให้ครบถ้วน!",
                type: "warning",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
    exit;
}

// ตรวจสอบว่ามีชื่อชั้นเรียนในฐานข้อมูลแล้วหรือยัง
$sql_check = "SELECT * FROM `teacher` WHERE  name = '$name' && surname = '$surname'AND id != '$teacher_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo '<script>
    setTimeout(function() {
        swal({
            title: "เกิดข้อผิดพลาด!",
            text: "ชื่อคุณครู ' . $name . ' ' . $surname . ' มีอยู่แล้วในระบบ",
            type: "error",
            showConfirmButton: true
        }, function() {
            window.history.back();
        });
    }, 100);
    </script>';
} else {
// แก้ไขข้อมูลครู
$sql_update = "UPDATE teacher SET name = '$name', surname = '$surname', id_room = '$id_room' WHERE id = '$teacher_id'";
if (mysqli_query($conn, $sql_update)) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "สำเร็จ!",
                text: "แก้ไขข้อมูลคุณครูสำเร็จ!",
                type: "success",
                timer: 1500,
                showConfirmButton: false
            }, function() {
                window.location.href = "../index.php?id=teacher";
            });
        }, 100);
    </script>';
} else {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "ไม่สามารถแก้ไขข้อมูลได้",
                type: "error",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
}
}



$conn->close();
?>
