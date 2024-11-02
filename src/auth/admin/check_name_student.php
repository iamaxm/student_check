<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch time_inout data with student name and surname
$time_inout_sql = "
    SELECT time_inout.id, time_inout.status, time_inout.created_at, 
           student.prefix, student.name AS student_name, student.surname, 
           grade_level.grade_level, room.name AS room_name
    FROM time_inout
    LEFT JOIN student ON time_inout.id_student = student.id
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    LEFT JOIN room ON student.id_room = room.id
    ORDER BY time_inout.created_at DESC";
$time_inout_result = $conn->query($time_inout_sql);

if (!$time_inout_result) {
    die("Error in SQL query: " . $conn->error);
}
// ตั้ง timezone เป็น Asia/Bangkok
date_default_timezone_set("Asia/Bangkok");
?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ตารางการเช็คชื่อเข้าเรียน</title>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<style>
    .head {
        display: flex;
        flex-direction: row;
        align-items: center;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ตารางการเช็คชื่อเข้าเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <div class="head">
                        <div class="">
                            <input type="number" class="form-control" id="studentIdInput" name="studentIdInput" min="1" placeholder="กรอก ID นักเรียน" required>
                        </div>
                        <button type="button" class="btn btn-outline-success m-1" onclick="toggleCheckInOut()">เช็คชื่อ</button>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>
                                        <center>ชื่อ นามสกุล</center>
                                    </th>
                                    <th>
                                        <center>ชั้นเรียน</center>
                                    </th>
                                    <th>
                                        <center>ห้องเรียน</center>
                                    </th>
                                    <th>
                                        <center>สถานะ</center>
                                    </th>
                                    <th>
                                        <center>วันที่และเวลา</center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($time_inout_row = $time_inout_result->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <center><?php echo $time_inout_row['prefix'] . ' ' . $time_inout_row['student_name'] . ' ' . $time_inout_row['surname']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $time_inout_row['grade_level']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $time_inout_row['room_name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo ($time_inout_row['status'] == 'in') ? 'เข้าเรียน' : 'ออกเรียน'; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo date("d/m/Y H:i:s", strtotime($time_inout_row['created_at'])); ?></center>
                                        </td>
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
    $(document).ready(function() {
        $('#studentIdInput').val(''); // ล้างค่า input ทุกครั้งที่โหลดหน้าใหม่
        $('#studentIdInput').focus(); // โฟกัสที่ input เมื่อโหลดหน้า
    });

    function toggleCheckInOut() {
        const studentId = $('#studentIdInput').val();
        const currentTime = new Date();
        const currentHour = currentTime.getHours();
        const currentMinutes = currentTime.getMinutes() / 100; // แบ่ง 100 เพื่อให้ได้ทศนิยมแบบ 0.27
        const currentTimeDecimal = currentHour + currentMinutes;
        console.log("Current Hour (Decimal):", currentTimeDecimal);


        if ((currentTimeDecimal >= 6.00 && currentTimeDecimal <= 8.00) ||
            (currentTimeDecimal >= 16.00 && currentTimeDecimal <= 17.00)) {
            $.ajax({
                url: 'backend/bn_check_in_out.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    student_id: studentId,
                    hour: currentTimeDecimal
                },
                success: function(response) {
                    const messageType = response.message.includes("เกิดข้อผิดพลาด") ? "warning" :
                        response.message.includes("ไม่สามารถ") ? "error" : "success";
                    swal({
                        title: messageType === "success" ? "สำเร็จ!" : "เกิดข้อผิดพลาด!",
                        text: response.message,
                        type: messageType,
                        timer: 2000,
                        showConfirmButton: true
                    });
                    $('#studentIdInput').val('');
                    if (messageType === "success") {
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
                },
                error: function() {
                    swal("เกิดข้อผิดพลาด!", "ไม่สามารถบันทึกข้อมูลได้", "error");
                    $('#studentIdInput').val('');
                }
            });
        } else {
            swal({
                title: "เกิดข้อผิดพลาด!",
                text: "สามารถเช็คเข้าได้เวลา 6:00-8:00 และเช็คออกได้เวลา 16:00-17:00 เท่านั้น",
                type: "warning",
                timer: 2000,
                showConfirmButton: true
            });
            $('#studentIdInput').val('');
        }
    }


    $('#studentIdInput').on('keypress', function(event) {
        if (event.which === 13) {
            toggleCheckInOut();
        }
    });
</script>