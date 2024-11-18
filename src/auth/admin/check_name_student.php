<?php
@session_start();
include '../config/ConnectDB.php';

date_default_timezone_set("Asia/Bangkok");

$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

if (!$time_settings) {
    die("ไม่พบการตั้งค่าเวลาสำหรับการเช็คชื่อ");
}


$check_in_start = $time_settings['check_in_start'];
$check_in_end = $time_settings['check_in_end'];
$check_out_start = $time_settings['check_out_start'];
$check_out_end = $time_settings['check_out_end'];

$current_time = date("H:i");
$is_check_in_time = $current_time >= $check_in_start && $current_time <= $check_out_start;
$is_check_out_time = $current_time >= $check_out_start && $current_time <= $check_out_end;

$today = date("Y-m-d");
$check_existing_data_sql = "
    SELECT COUNT(*) AS total FROM check_in 
    WHERE DATE(created_at) = '$today'";
$check_existing_data_result = $conn->query($check_existing_data_sql);
$row = $check_existing_data_result->fetch_assoc();
$has_check_in_data = $row['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_check'])) {
    $students_sql = "SELECT id FROM student";
    $students_result = $conn->query($students_sql);

    if ($students_result) {
        while ($student = $students_result->fetch_assoc()) {
            $student_id = $student['id'];
            // เพิ่มข้อมูลใหม่เฉพาะเมื่อยังไม่มีข้อมูลในวันนี้
            $check_existing_sql = "
                SELECT id FROM check_in
                WHERE id_student = $student_id AND DATE(created_at) = '$today'";
            $check_existing_result = $conn->query($check_existing_sql);

            if ($check_existing_result->num_rows === 0) {
                $insert_sql = "
                    INSERT INTO check_in (id_student, in_at, out_at, created_at)
                    VALUES ($student_id, NULL, NULL, NOW())";
                $conn->query($insert_sql);
            }
        }
    }

    // อัปเดตตัวแปร $has_check_in_data หลังจากเพิ่มข้อมูล
    $check_existing_data_sql = "
        SELECT COUNT(*) AS total FROM check_in 
        WHERE DATE(created_at) = '$today'";
    $check_existing_data_result = $conn->query($check_existing_data_sql);
    $row = $check_existing_data_result->fetch_assoc();
    $has_check_in_data = $row['total'] > 0;
}
?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบเช็คชื่อ</title>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<style>
    .hidden {
        display: none;
    }

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
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ระบบเช็คชื่อ</h1>
                </center>
                <div class="card-body p-4">
                    <?php if ($is_check_in_time || $is_check_out_time): ?>
                        <?php if ($is_check_in_time && !$has_check_in_data): ?>
                            <!-- แสดงปุ่มเริ่มเช็คเข้าเรียน -->
                            <center>
                                <form method="POST">
                                    <input type="hidden" name="start_check" value="1">
                                    <button type="submit" class="btn btn-primary">
                                        เริ่มการเช็คชื่อ เข้าเรียน
                                    </button>
                                </form>
                            </center>
                        <?php else: ?>
                            <!-- แสดงฟอร์มเช็คชื่อ -->
                            <div id="checkForm">
                                <div class="head">
                                    <div class="">
                                        <input type="number" class="form-control" id="studentIdInput" name="studentIdInput" min="1" placeholder="กรอก ID นักเรียน" required>
                                    </div>
                                    <button type="button" class="btn btn-outline-success m-1" onclick="toggleCheckInOut()">เช็คชื่อ</button>
                                </div>
                            </div>

                            <!-- แสดงข้อมูลการเช็คชื่อ -->
                            <div class="table-responsive mt-4">
                                <table class="table text-nowrap mb-0 align-middle">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th>
                                                <center>ชื่อ นามสกุล</center>
                                            </th>
                                            <th>
                                                <center>เวลาเข้า</center>
                                            </th>
                                            <th>
                                                <center>เวลาออก</center>
                                            </th>
                                            <th>
                                                <center>วันที่และเวลา</center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // ดึงข้อมูลจากตาราง check_in
                                        $check_in_sql = "
                SELECT check_in.in_at, check_in.out_at, check_in.created_at,
                       student.prefix, student.name AS student_name, student.surname
                FROM check_in
                LEFT JOIN student ON check_in.id_student = student.id
                WHERE DATE(check_in.created_at) = '$today'
                ORDER BY check_in.created_at DESC";

                                        $check_in_result = $conn->query($check_in_sql);

                                        while ($check_in_row = $check_in_result->fetch_assoc()) :
                                        ?>
                                            <tr>
                                                <td>
                                                    <center><?php echo $check_in_row['prefix'] . ' ' . $check_in_row['student_name'] . ' ' . $check_in_row['surname']; ?></center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php
                                                        if ($check_in_row['in_at']) {
                                                            echo date("H:i:s", strtotime($check_in_row['in_at']));
                                                        } else {
                                                            echo "ยังไม่เข้าเรียน";
                                                        }
                                                        ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php
                                                        if ($check_in_row['out_at']) {
                                                            echo date("H:i:s", strtotime($check_in_row['out_at']));
                                                        } else {
                                                            echo "ยังไม่ออกเรียน";
                                                        }
                                                        ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center><?php echo date("d/m/Y H:i:s", strtotime($check_in_row['created_at'])); ?></center>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>
                    <?php else: ?>
                        <center>
                            <h4>ยังไม่ถึงเวลาเช็คชื่อ</h4>
                        </center>
                    <?php endif; ?>
                </div>
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

        // เพิ่ม Event Listener สำหรับการกด Enter
        $('#studentIdInput').on('keypress', function(event) {
            if (event.which === 13) { // ตรวจสอบว่ากดปุ่ม Enter (keycode 13)
                toggleCheckInOut(); // เรียกใช้ฟังก์ชันเช็คชื่อ
            }
        });
    });

    function toggleCheckInOut() {
        const studentId = $('#studentIdInput').val();
        const currentHour = new Date().getHours();
        const currentMinutes = new Date().getMinutes() / 100;
        const currentTimeDecimal = currentHour + currentMinutes;

        $.ajax({
            url: 'backend/bn_check_in_out.php',
            type: 'POST',
            dataType: 'json',
            data: {
                student_id: studentId,
                hour: currentTimeDecimal // ส่งค่าเวลาไปที่ backend
            },
            success: function(response) {
                const messageType = response.message.includes("สำเร็จ") ? "success" : "error";
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
                swal("เกิดข้อผิดพลาด!", "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้", "error");
                $('#studentIdInput').val('');
            }
        });
    }
</script>