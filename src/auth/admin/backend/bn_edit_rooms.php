<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<?php
include '../../config/ConnectDB.php';

$room_id = $_POST['room_id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);

// ตรวจสอบค่าว่าง
if (empty($name)) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "กรุณากรอกชื่อห้องเรียน!",
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
$sql_check = "SELECT * FROM room WHERE name = '$name' AND id != '$room_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "ชื่อชั้นเรียน ' . $name . ' มีอยู่แล้วในระบบ",
                type: "error",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
} else {
    // อัพเดตข้อมูลชั้นเรียน
    $sql_update = "UPDATE room SET name = '$name' WHERE id = '$room_id'";
    if (mysqli_query($conn, $sql_update)) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "แก้ไขชื่อห้องเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=rooms";
                });
            }, 100);
        </script>';
    } else {
        echo "Error: " . $sql_update . "<br>" . mysqli_error($conn);
    }
}

$conn->close();
?>
