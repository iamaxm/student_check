<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
@session_start();
include '../../config/ConnectDB.php';

// รับข้อมูลจากฟอร์ม
$admin_id = $_POST['admin_id'];
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// ตรวจสอบค่าว่าง
if (empty($username)) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "กรุณากรอก username!",
                type: "warning",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
    exit;
}

// ตรวจสอบว่ามีแอดมินที่มีชื่อนี้อยู่ในระบบแล้วหรือยัง (ยกเว้น ID ปัจจุบัน)
$sql_check = "SELECT * FROM `admin` WHERE username = '$username' AND id != '$admin_id'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "ชื่อผู้ใช้ ' . $username . ' มีอยู่แล้วในระบบ",
                type: "error",
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100);
    </script>';
} else {
    // ตรวจสอบว่าผู้ใช้ได้ใส่รหัสผ่านใหม่หรือไม่
    if (!empty($password)) {
        if (strlen($password) < 8) { // เช็คความยาวของรหัสผ่าน
            echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด !",
                text: "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร",
                type: "warning", 
                showConfirmButton: true 
            }, function() {
                window.history.back(); 
              });
        }, 100); 
        </script>';
            exit;
        } else {
            
            // อัปเดตทั้ง username และ password
            $sql_update = "UPDATE admin SET username = '$username', password = '$password' WHERE id = '$admin_id'";
        }
    } else {
        // อัปเดตเฉพาะ username โดยไม่เปลี่ยน password
        $sql_update = "UPDATE admin SET username = '$username' WHERE id = '$admin_id'";
    }

    if (mysqli_query($conn, $sql_update)) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "แก้ไขข้อมูลแอดมินสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=admin";
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
