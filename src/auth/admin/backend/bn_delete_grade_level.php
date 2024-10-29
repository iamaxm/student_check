<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<?php
include '../../config/ConnectDB.php';

if (isset($_POST['grade_id'])) {
    $grade_id = $_POST['grade_id'];

    // Check if there are students associated with this grade level
    $check_sql = "SELECT COUNT(*) AS student_count FROM student WHERE id_grade = $grade_id";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();

    if ($check_row['student_count'] > 0) {
        // If there are students associated, show an error message
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด!",
                    text: "ไม่สามารถลบชั้นเรียนได้เนื่องจากมีนักเรียนที่อยู่ชั้นเรียนนี้",
                    type: "error",
                    showConfirmButton: true
                }, function() {
                    window.history.back();
                });
            }, 100);
        </script>';
    } else {
        // If no students are associated, proceed with deletion
        $delete_sql = "DELETE FROM grade_level WHERE id = $grade_id";

        if ($conn->query($delete_sql) === TRUE) {
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
            echo "Error: " . $delete_sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>