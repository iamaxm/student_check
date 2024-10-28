<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

$grade_level = mysqli_real_escape_string($conn, $_POST['grade_level']);

// ตรวจสอบว่าฟิลด์ว่างหรือไม่
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
} else {
    // ตรวจสอบว่ามีชื่อชั้นเรียนในฐานข้อมูลแล้วหรือยัง
    $sql_check = "SELECT * FROM grade_level WHERE grade_level = '$grade_level'";
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
        date_default_timezone_set('Asia/Bangkok');
        $created_at = date('Y-m-d H:i:s');
        // บันทึกข้อมูลลงในฐานข้อมูล
        $sql_insert = "INSERT INTO `grade_level`(`grade_level`, `created_at`) VALUES ('$grade_level','$created_at')";
        if (mysqli_query($conn, $sql_insert)) {
            echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "สร้างชั้นเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=grade_level";
                });
            }, 100);
            </script>';
        } else {
            echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
        }
    }
}

$conn->close();
?>