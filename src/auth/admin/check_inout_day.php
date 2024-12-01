<?php
include '../config/ConnectDB.php';

// ตั้งค่า Timezone ให้ตรงกัน
date_default_timezone_set('Asia/Bangkok');

// Fetch time settings
$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

if (!$time_settings) {
    die("ไม่พบข้อมูลการตั้งค่าเวลา");
}

// Fetch grades for dropdown selection
$grade_sql = "SELECT * FROM grade_level";
$grade_result = $conn->query($grade_sql);

// Fetch rooms based on selected grade
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

// Fetch attendance data for selected date
$selected_date = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d");
$attendance_sql = "
    SELECT id_student, 
           TIME(in_at) AS check_in_time, 
           TIME(out_at) AS check_out_time,
           DATE(in_at) AS record_date
    FROM check_in 
    WHERE DATE(in_at) = '$selected_date' OR DATE(out_at) = '$selected_date'";
$attendance_result = $conn->query($attendance_sql);

// Map attendance data
$attendance = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance[$row['id_student']] = [
        'check_in' => $row['check_in_time'],
        'check_out' => $row['check_out_time']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการเช็คชื่อ (รายวัน)</title>
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            padding: 8px;
        }

        .name-column {
            text-align: left;
            white-space: nowrap;
            max-width: 200px;
        }

        .table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">รายงานการเช็คชื่อ (รายวัน)</h1>
                    </center>
                    <div class="card-body p-4">
                        <form method="POST" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="date" class="form-label">เลือกวันที่</label>
                                    <input type="date" id="date" name="date" class="form-control" value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
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
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="name-column">ชื่อ-นามสกุล</th>
                                        <th colspan="2">มาเรียน</th>
                                        <th rowspan="2">เลิกเรียน (ปกติ)</th>
                                    </tr>
                                    <tr>
                                        <th>ปกติ</th>
                                        <th>สาย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($student = $student_result->fetch_assoc()) : ?>
                                        <tr>
                                            <td class="name-column"><?php echo $student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']; ?></td>
                                            <td>
                                                <?php
                                                $check_in_time = $attendance[$student['id']]['check_in'] ?? null;
                                                echo ($check_in_time && $check_in_time <= $time_settings['check_in_end']) ? $check_in_time : "-";
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo ($check_in_time && $check_in_time > $time_settings['check_in_end']) ? $check_in_time : "-";
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $check_out_time = $attendance[$student['id']]['check_out'] ?? null;
                                                echo $check_out_time ?? "-";
                                                ?>
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
</body>

</html>
