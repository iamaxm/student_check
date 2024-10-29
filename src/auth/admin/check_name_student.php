<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch time_inout data with student name and surname
$time_inout_sql = "
    SELECT time_inout.id, time_inout.status, time_inout.created_at, 
           student.prefix, student.name AS student_name, student.surname , grade_level.grade_level, room.name AS room_name
    FROM time_inout
    LEFT JOIN student ON time_inout.id_student = student.id
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    LEFT JOIN room ON student.id_room = room.id
    ORDER BY time_inout.created_at";
$time_inout_result = $conn->query($time_inout_sql);

if (!$time_inout_result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<style>
    .head {
        display: flex;
        flex-direction: row;
        align-items: center;
    }
</style>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

<div class="container-fluid">
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ตารางการเช็คชื่อเข้าเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <div class="head">
                        <!-- ใช้ input แทน select เพื่อกรอก ID นักเรียน -->
                        <div class="">
                            <input type="number" class="form-control" id="studentIdInput" name="studentIdInput" min="1" placeholder="กรอก ID นักเรียน" required>
                        </div>
                        <button type="button" class="btn btn-outline-success m-1" onclick="toggleCheckInOut()">
                            เช็คชื่อ
                        </button>
                    </div>

                    <!-- Time In/Out Table -->
                    <div class="table-responsive mt-4">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                <th><center>ชื่อ นามสกุล</center></th>
                                    <th><center>ชั้นเรียน</center></th>
                                    <th><center>ห้องเรียน</center></th>
                                    <th><center>สถานะ</center></th>
                                    <th><center>วันที่และเวลา</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($time_inout_row = $time_inout_result->fetch_assoc()) : ?>
                                    <tr>
                                    <td><center><?php echo $time_inout_row['prefix'] . ' ' . $time_inout_row['student_name'] . ' ' . $time_inout_row['surname']; ?></center></td>
                                        <td><center><?php echo $time_inout_row['grade_level']; ?></center></td>
                                        <td><center><?php echo $time_inout_row['room_name']; ?></center></td>
                                        <td><center><?php echo ($time_inout_row['status'] == 'in') ? 'เข้าเรียน' : 'ออกเรียน'; ?></center></td>
                                        <td><center><?php echo date("d/m/Y H:i:s", strtotime($time_inout_row['created_at'])); ?></center></td>

                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ให้ cursor อยู่ในช่อง input เมื่อโหลดหน้า
$(document).ready(function() {
    $('#studentIdInput').focus(); // ทำให้ช่อง input โฟกัส
});

function toggleCheckInOut() {
    const studentId = $('#studentIdInput').val();

    if (!studentId) {
        swal("เกิดข้อผิดพลาด!", "กรุณากรอก ID นักเรียนก่อน", "warning");
        return;
    }

    $.ajax({
        url: 'backend/bn_check_in_out.php',
        type: 'POST',
        dataType: 'json',
        data: {
            student_id: studentId
        },
        success: function(response) {
            swal({
                title: "สำเร็จ!",
                text: response.message,
                type: "success",
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(function() {
                window.location.reload(); // Reload page to show updated records
            }, 1500);
        },
        error: function() {
            swal("เกิดข้อผิดพลาด!", "ไม่สามารถบันทึกข้อมูลได้", "error");
        }
    });
}

// เพิ่ม event listener ให้ input ทำงานเมื่อกด Enter
$('#studentIdInput').on('keypress', function(event) {
    if (event.which === 13) { // ตรวจสอบว่าปุ่ม Enter ถูกกด (keycode 13)
        toggleCheckInOut(); // เรียกใช้ฟังก์ชัน toggleCheckInOut
    }
});
</script>
