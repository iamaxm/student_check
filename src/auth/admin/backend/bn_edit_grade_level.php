<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<?php
include '../../config/ConnectDB.php';

$grade_id = $_POST['grade_id'];
$grade_level = mysqli_real_escape_string($conn, $_POST['grade_level']);

// ตรวจสอบค่าว่าง
if (empty($grade_level)) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "กรุณากรอกชื่อชั้นเรียน!",
                type: "warning",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
    exit;
}

// ตรวจสอบว่ามีชื่อชั้นเรียนอยู่ในระบบแล้วหรือไม่ (ยกเว้นกรณีที่เป็นชื่อเดียวกับที่กำลังแก้ไข)
$sql_check = "SELECT * FROM grade_level WHERE grade_level = '$grade_level' AND id != '$grade_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "ชื่อชั้นเรียน ' . $grade_level . ' มีอยู่แล้วในระบบ",
                type: "error",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
} else {
    // อัพเดตข้อมูลชั้นเรียน
    $sql_update = "UPDATE grade_level SET grade_level = '$grade_level' WHERE id = '$grade_id'";
    if (mysqli_query($conn, $sql_update)) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "แก้ไขชื่อชั้นเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=grade_level";
                });
            }, 100);
        </script>';
    } else {
        echo "Error: " . $sql_update . "<br>" . mysqli_error($conn);
    }
}

$conn->close();
?>
