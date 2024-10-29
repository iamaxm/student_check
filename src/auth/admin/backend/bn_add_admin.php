<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<?php

include '../../config/ConnectDB.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

if (empty($username)) {
    echo '<script>
    setTimeout(function() {
        swal({
            title: "เกิดข้อผิดพลาด !",
            text: "กรุณากรอก username !",
            type: "warning",
           
            showConfirmButton: true 
        }, function() {
            window.history.back(); 
          });
    }, 100); 
    </script>';
} else if (empty($password)) {
    echo '<script>
  setTimeout(function() {
      swal({
          title: "เกิดข้อผิดพลาด !",
          text: "กรุณากรอก password !",
          type: "warning",
         
          showConfirmButton: true 
      }, function() {
          window.history.back(); 
        });
  }, 100); 
  </script>';
} else if (empty($cpassword)) {
    echo '<script>
    setTimeout(function() {
        swal({
            title: "เกิดข้อผิดพลาด !",
            text: "กรุณา Confirm password !",
            type: "warning", 
           
            showConfirmButton: true 
        }, function() {
            window.history.back(); 
          });
    }, 100); 
    </script>';
} else {
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
    } else {
        if ($password == $cpassword) {
            // ตรวจสอบรหัสผ่านถูกต้องและดำเนินการต่อไป
            $sqli_username = "SELECT * FROM `admin` WHERE `username` = '$username'";
            $result_username = mysqli_query($conn, $sqli_username);


            if (mysqli_num_rows($result_username) > 0) {
                echo '<script>
    setTimeout(function() {
        swal({
            title: "เสียใจด้วย",
            text: "username ' . $username . ' มีผู้ใช้งานแล้ว",
            type: "error", 
           
            showConfirmButton: true 
        }, function() {
            window.history.back(); 
          });
    }, 100); 
    </script>';
            } else {
                // วันที่และเวลาปัจจุบัน (ในโซนเวลาของไทย)
                date_default_timezone_set('Asia/Bangkok');
                $user_created_at = date('Y-m-d H:i:s');
                $save_data_sql = "INSERT INTO `admin`(`username`, `password`, `created_at`) 
                VALUES ('$username','$password','$user_created_at')";
                $result = mysqli_query($conn, $save_data_sql);
                if ($result) {
                    echo '<script>
    setTimeout(function() {
        swal({
            title: "ยินดีด้วย",
            text: "สร้างแอดมินสำเร็จ!",
            type: "success", 
            timer:1500,
            showConfirmButton: false 
        }, function() {
            window.location.href = "../index.php?id=admin";
          });
    }, 100); 
    </script>';
                } else {
                    echo "Error: " . $save_data_sql . "<br>" . $conn->error;
                }
            }
        } else {
            echo '<script>
    setTimeout(function() {
        swal({
            title: "เกิดข้อผิดพลาด !",
            text: "รหัสผ่านไม่ตรงกัน !", 
            type: "warning", 
           
            showConfirmButton: true 
        }, function() {
            window.history.back(); 
          });
    }, 100); 
    </script>';
        }
    }
}

$conn->close();
?>