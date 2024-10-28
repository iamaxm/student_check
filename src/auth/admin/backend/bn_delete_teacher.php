<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];

    // ลบข้อมูลครู
    $sql_delete = "DELETE FROM teacher WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $teacher_id);

    if ($stmt->execute()) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "ลบข้อมูลคุณครูสำเร็จ!",
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
                    text: "ไม่สามารถลบข้อมูลได้",
                    type: "error",
                    showConfirmButton: true
                }, function() {
                    window.history.back();
                });
            }, 100);
        </script>';
    }

    $stmt->close();
}

$conn->close();
?>
