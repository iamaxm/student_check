<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

// รับข้อมูลจากฟอร์ม
$student_id = $_POST['student_id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$surname = mysqli_real_escape_string($conn, $_POST['surname']);
@$id_grade = $_POST['id_grade'];
@$id_room = $_POST['id_room'];
@$id_teacher = $_POST['id_teacher'];

// ตรวจสอบค่าว่าง
if (empty($name) || empty($surname) || empty($id_grade) || empty($id_room) || empty($id_teacher)) {
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

// ตรวจสอบว่ามีนักเรียนที่มีชื่อนี้อยู่ในระบบแล้วหรือยัง (ยกเว้น ID ปัจจุบัน)
$sql_check = "SELECT * FROM `student` WHERE name = '$name' AND surname = '$surname' AND id != '$student_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "ชื่อนักเรียน ' . $name . ' ' . $surname . ' มีอยู่แล้วในระบบ",
                type: "error",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
} else {
    // อัปเดตข้อมูลนักเรียน
    $sql_update = "UPDATE student SET name = '$name', surname = '$surname', id_grade = '$id_grade', id_room = '$id_room', id_teacher = '$id_teacher' WHERE id = '$student_id'";
    
    if (mysqli_query($conn, $sql_update)) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "แก้ไขข้อมูลนักเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=student";
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
