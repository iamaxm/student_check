<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<?php
include '../../config/ConnectDB.php';

if (isset($_POST['grade_id'])) {
    $grade_id = $_POST['grade_id'];
    
    // ลบข้อมูลจากตาราง grade_level
    $delete_sql = "DELETE FROM grade_level WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $grade_id);

    if ($stmt->execute()) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "สำเร็จ!",
                    text: "ลบชั้นเรียนสำเร็จ!",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                }, function() {
                    window.location.href = "../index.php?id=grade_level";
                });
            }, 100);
        </script>';
    } else {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด!",
                    text: "ไม่สามารถลบชั้นเรียนได้",
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
