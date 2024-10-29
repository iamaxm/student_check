<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

if (isset($_POST['admin_id'])) {
    $admin_id = $_POST['admin_id'];

    // ตรวจสอบก่อนว่าหากต้องการเงื่อนไขเพิ่มเติม เช่น ตรวจสอบความสัมพันธ์อื่นๆ
    // $check_sql = "SELECT COUNT(*) AS related_count FROM some_table WHERE admin_id = $admin_id";
    // $check_result = $conn->query($check_sql);
    // $check_row = $check_result->fetch_assoc();

    // if ($check_row['related_count'] > 0) {
    //     echo '<script>
    //         setTimeout(function() {
    //             swal({
    //                 title: "เกิดข้อผิดพลาด!",
    //                 text: "ไม่สามารถลบแอดมินนี้ได้เนื่องจากมีความสัมพันธ์อื่นๆ อยู่",
    //                 type: "error",
    //                 showConfirmButton: true
    //             }, function() {
    //                 window.history.back();
    //             });
    //         }, 100);
    //     </script>';
    // } else {

    // ลบแอดมินโดยไม่มีข้อจำกัดอื่น
    $delete_sql = "DELETE FROM admin WHERE id = $admin_id";

    if ($conn->query($delete_sql) === TRUE) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "ลบแอดมินสำเร็จ!",
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
                    text: "ไม่สามารถลบแอดมินได้ กรุณาลองใหม่อีกครั้ง",
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
