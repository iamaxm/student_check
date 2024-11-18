<?php
@session_start();
include '../config/ConnectDB.php';

// ตั้ง timezone เป็น Asia/Bangkok
date_default_timezone_set("Asia/Bangkok");

// ดึงข้อมูลชั้นเรียน
$grade_sql = "SELECT * FROM grade_level";
$grade_result = $conn->query($grade_sql);

// ดึงข้อมูลห้องเรียนตามชั้นเรียนที่เลือก
$selected_grade = isset($_POST['grade_id']) ? $_POST['grade_id'] : null;
$room_sql = "SELECT * FROM room";
if ($selected_grade) {
    $room_sql .= " WHERE grade_id = '$selected_grade'";
}
$room_result = $conn->query($room_sql);

// ดึงข้อมูลนักเรียนตามชั้นเรียนและห้องที่เลือก
$selected_room = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$student_sql = "SELECT * FROM student WHERE 1=1";
if ($selected_grade) {
    $student_sql .= " AND id_grade = '$selected_grade'";
}
if ($selected_room) {
    $student_sql .= " AND id_room = '$selected_room'";
}
$student_result = $conn->query($student_sql);

// เก็บวันที่ที่ผู้ใช้เลือกไว้หรือใช้วันที่ปัจจุบันหากยังไม่มีการเลือก
$selected_date = isset($_POST['datetime']) ? $_POST['datetime'] : date("Y-m-d H:i");
$display_date = date("Y-m-d", strtotime($selected_date));

// ฟังก์ชันดึงสถานะล่าสุดของนักเรียน
function getStudentStatus($conn, $student_id, $display_date)
{
    $status_sql = "SELECT status FROM time_inout WHERE id_student = '$student_id' AND DATE(created_at) = '$display_date' ORDER BY created_at DESC LIMIT 1";
    $status_result = $conn->query($status_sql);
    return ($status_result && $status_result->num_rows > 0) ? $status_result->fetch_assoc()['status'] : 'out';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เช็คชื่อเข้าเรียน</title>
    <style>
        .status-btn {
            width: 100px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col d-flex align-items-stretch">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">เช็คชื่อเข้าเรียน</h1>
                    </center>
                    <div class="card-body p-4">
                        <!-- ฟอร์มเลือกชั้นเรียน ห้องเรียน และวันที่ -->
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="grade_id" class="form-label">ชั้นเรียน</label>
                                    <select class="form-select" id="grade_id" name="grade_id" onchange="this.form.submit()">
                                        <option value="">-- ชั้นเรียนทั้งหมด --</option>
                                        <?php while ($grade_row = $grade_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $grade_row['id']; ?>" <?php if ($selected_grade == $grade_row['id']) echo 'selected'; ?>>
                                                <?php echo $grade_row['grade_level']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="room_id" class="form-label">ห้องเรียน</label>
                                    <select class="form-select" id="room_id" name="room_id" onchange="this.form.submit()">
                                        <option value="">-- ห้องเรียนทั้งหมด --</option>
                                        <?php while ($room_row = $room_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $room_row['id']; ?>" <?php if ($selected_room == $room_row['id']) echo 'selected'; ?>>
                                                <?php echo $room_row['name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="datetime" class="form-label">วันที่และเวลา</label>
                                    <input type="text" id="datetime" name="datetime" class="form-control" placeholder="เลือกวันที่และเวลา" value="<?php echo $selected_date; ?>">
                                    <script>
                                        flatpickr("#datetime", {
                                            enableTime: true,
                                            dateFormat: "Y-m-d H:i",
                                            time_24hr: true,
                                            defaultDate: "<?php echo $selected_date; ?>",
                                            onChange: function() {
                                                document.forms[0].submit();
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </form>

                        <!-- แสดงรายชื่อนักเรียนและปุ่มเช็คสถานะ -->
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">ชื่อ-นามสกุล</th>
                                        <th style="text-align: center; vertical-align: middle;">สถานะ</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <?php while ($student = $student_result->fetch_assoc()) : ?>
                                        <?php
                                        $current_status = getStudentStatus($conn, $student['id'], $display_date);
                                        ?>
                                        <tr>
                                            <td><?php echo $student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']; ?></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button type="button" class="btn btn-outline-<?php echo ($current_status == 'in') ? 'danger' : 'success'; ?> status-btn"
                                                    onclick="toggleCheckInOut(<?php echo $student['id']; ?>, '<?php echo $current_status == 'in' ? 'out' : 'in'; ?>')">
                                                    <?php echo ($current_status == 'in') ? 'ออกเรียน' : 'เข้าเรียน'; ?>
                                                </button>
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
        function toggleCheckInOut(studentId, newStatus) {
            const selectedDateTime = $('#datetime').val(); // วันที่และเวลาเลือกโดยผู้ใช้
            $.ajax({
                url: 'backend/bn_manual_check_in_out.php',
                type: 'POST',
                data: {
                    student_id: studentId,
                    status: newStatus,
                    datetime: selectedDateTime
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
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function() {
                    swal("เกิดข้อผิดพลาด!", "ไม่สามารถบันทึกข้อมูลได้", "error");
                }
            });
        }
    </script>

</body>

</html>