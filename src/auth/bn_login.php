<?php
session_start();


include('config/ConnectDB.php');
echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // ผู้ใช้เข้าสู่ระบบแล้ว
    $redirectUrl = "admin/index.php"; // ค่าเริ่มต้นสำหรับผู้ใช้ทั่วไป

    echo '<script>
    setTimeout(function() {
        swal({
            title: "เกิดข้อผิดพลาด!",
            text: "คุณได้เข้าสู่ระบบแล้ว!",
            type: "warning",
            showConfirmButton: true
        }, function() {
            window.location.href = "' . $redirectUrl . '";
        });
    }, 100);
    </script>';
    exit();
}

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ค้นหาผู้ใช้ในฐานข้อมูล
    $sql = "SELECT * FROM `admin` WHERE  `username` = '$username' AND `password` = '$password'";
    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        // เข้าสู่ระบบสำเร็จ
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];


        echo '<script>
            setTimeout(function() {
                swal({
                    title: "ยินดีด้วย!",
                    text: "เข้าสู่ระบบสำเร็จ!",
                    type: "success", 
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "admin/index.php";
                });
            }, 100); 
            </script>';

        exit();
    } else {
        // เข้าสู่ระบบไม่สำเร็จ
        echo '<script>
        setTimeout(function() {
            swal({
                title: "เกิดข้อผิดพลาด!", 
                text: "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!",
                type: "warning", 
                showConfirmButton: true
            }, function() {
                window.history.back();
            });
        }, 100); 
        </script>';
        exit();
    }
}
