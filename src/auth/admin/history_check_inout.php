<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch grades for dropdown selection
$grade_sql = "SELECT * FROM grade_level";
$grade_result = $conn->query($grade_sql);

// Fetch rooms for dropdown selection based on selected grade
$selected_grade = isset($_POST['grade_id']) ? $_POST['grade_id'] : null;
$room_sql = "SELECT * FROM room";
if ($selected_grade) {
    $room_sql .= " WHERE grade_id = '$selected_grade'";
}
$room_result = $conn->query($room_sql);

// Fetch students based on selected grade and room
$selected_room = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$student_sql = "SELECT * FROM student WHERE 1=1";
if ($selected_grade) {
    $student_sql .= " AND id_grade = '$selected_grade'";
}
if ($selected_room) {
    $student_sql .= " AND id_room = '$selected_room'";
}
$student_result = $conn->query($student_sql);

// Fetch time_inout data based on selected filters
$selected_student = isset($_POST['student_id']) ? $_POST['student_id'] : null;
$selected_date = isset($_POST['date']) ? $_POST['date'] : null;

$time_inout_sql = "
    SELECT time_inout.id, time_inout.status, time_inout.created_at, 
           student.prefix, student.name AS student_name, student.surname, grade_level.grade_level, room.name AS room_name
    FROM time_inout
    LEFT JOIN student ON time_inout.id_student = student.id
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    LEFT JOIN room ON student.id_room = room.id
    WHERE 1=1";
if ($selected_date) {
    $time_inout_sql .= " AND DATE(time_inout.created_at) = '$selected_date'";
}
if ($selected_grade) {
    $time_inout_sql .= " AND student.id_grade = '$selected_grade'";
}
if ($selected_room) {
    $time_inout_sql .= " AND student.id_room = '$selected_room'";
}
if ($selected_student) {
    $time_inout_sql .= " AND time_inout.id_student = '$selected_student'";
}
$time_inout_sql .= " ORDER BY time_inout.created_at DESC";
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
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ประวัติการเช็คชื่อเข้าเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <!-- Form for filtering by grade, room, student, and date -->
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="date" class="form-label">วันที่</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
                            </div>
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
                                <label for="student_id" class="form-label">นักเรียน</label>
                                <select class="form-select" id="student_id" name="student_id" onchange="this.form.submit()">
                                    <option value="">-- นักเรียนทั้งหมด --</option>
                                    <?php while ($student_row = $student_result->fetch_assoc()) : ?>
                                        <option value="<?php echo $student_row['id']; ?>" <?php if ($selected_student == $student_row['id']) echo 'selected'; ?>>
                                            <?php echo $student_row['prefix'] . ' ' . $student_row['name'] . ' ' . $student_row['surname']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                        </div>
                    </form>

                    <!-- Time In/Out Table -->
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
