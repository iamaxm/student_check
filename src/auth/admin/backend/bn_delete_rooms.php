<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<?php
include '../../config/ConnectDB.php';

if (isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    
    // ลบข้อมูลจากตาราง grade_level
    $delete_sql = "DELETE FROM room WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $room_id);

    if ($stmt->execute()) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "ลบห้องเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=rooms";
                });
            }, 100);
        </script>';
    } else {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด!",
                    text: "ไม่สามารถลบห้องเรียนได้",
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
