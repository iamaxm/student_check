<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch time settings
$time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
$time_settings_result = $conn->query($time_settings_sql);
$time_settings = $time_settings_result->fetch_assoc();

if (!$time_settings) {
    header('Content-Type: application/json');
    echo json_encode(['message' => "ไม่พบข้อมูลการตั้งค่าเวลา"]);
    exit;
}

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

// Fetch check_in data based on selected month
$selected_month = isset($_POST['month']) ? $_POST['month'] : date("Y-m"); // Default current month
$selected_check_type = isset($_POST['check_type']) ? $_POST['check_type'] : 'check_in'; // Default to check_in
$time_field = $selected_check_type == 'check_in' ? 'in_at' : 'out_at';

// Query for check_in or check_out
$check_sql = "
    SELECT id_student, TIME($time_field) as check_time, DATE($time_field) as check_date 
    FROM check_in 
    WHERE DATE_FORMAT($time_field, '%Y-%m') = '$selected_month'";
$check_result = $conn->query($check_sql);

if (!$check_result) {
    die("Query failed: " . $conn->error . " | Query: " . $check_sql);
}

// Map attendance data to an array
$attendance = [];
while ($row = $check_result->fetch_assoc()) {
    $attendance[$row['id_student']][$row['check_date']] = $row['check_time'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการเช็คชื่อ</title>
    <style>
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .name-column {
            text-align: left !important;
            white-space: nowrap;
            max-width: 200px;
        }

        .table td:not(.name-column),
        .table th:not(.name-column) {
            text-align: center;
            vertical-align: middle;
            width: 30px;
        }

        .table th,
        .table td {
            font-size: 12px;
            padding: 5px;
        }

        .icon-check {
            color: black;
            font-weight: bold;
        }

        .icon-late {
            color: black;
            font-weight: bold;
        }

        .icon-cross {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">รายงานการเช็คชื่อรายเดือน</h1>
                    </center>
                    <div class="card-body p-4">
                        <form method="POST" class="mb-3">
                            <div class="row">
                                <!-- เลือกปี -->
                                <div class="col-md-2">
                                    <label for="year" class="form-label">เลือกปี</label>
                                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                                        <?php
                                        $current_year = date("Y") + 543;
                                        $selected_year = isset($_POST['year']) ? $_POST['year'] : $current_year;
                                        for ($year = $current_year - 10; $year <= $current_year; $year++) {
                                            $selected = ($selected_year == $year) ? 'selected' : '';
                                            echo "<option value='$year' $selected>$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- เลือกเดือน -->
                                <div class="col-md-2">
                                    <label for="month" class="form-label">เลือกเดือน</label>
                                    <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                                        <?php
                                        $thai_months = [
                                            'มกราคม',
                                            'กุมภาพันธ์',
                                            'มีนาคม',
                                            'เมษายน',
                                            'พฤษภาคม',
                                            'มิถุนายน',
                                            'กรกฎาคม',
                                            'สิงหาคม',
                                            'กันยายน',
                                            'ตุลาคม',
                                            'พฤศจิกายน',
                                            'ธันวาคม'
                                        ];
                                        $selected_year_ce = $selected_year - 543; // แปลง พ.ศ. เป็น ค.ศ.
                                        for ($month = 1; $month <= 12; $month++) {
                                            $month_option = $selected_year_ce . "-" . str_pad($month, 2, "0", STR_PAD_LEFT);
                                            $selected = ($selected_month == $month_option) ? 'selected' : '';
                                            echo "<option value='$month_option' $selected>" . $thai_months[$month - 1] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>


                                <!-- เลือกชั้นเรียน -->
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

                                <!-- เลือกห้องเรียน -->
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

                                <!-- เลือกประเภทการเช็ค -->
                                <div class="col-md-2">
                                    <label for="check_type" class="form-label">ประเภทการเช็ค</label>
                                    <select class="form-select" id="check_type" name="check_type" onchange="this.form.submit()">
                                        <option value="check_in" <?php if ($selected_check_type == 'check_in') echo 'selected'; ?>>เช็คเข้าเรียน</option>
                                        <option value="check_out" <?php if ($selected_check_type == 'check_out') echo 'selected'; ?>>เช็คออกเรียน</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="name-column">ชื่อ-นามสกุล</th>
                                        <?php
                                        $days_in_month = date("t", strtotime($selected_month . "-01"));
                                        for ($i = 1; $i <= $days_in_month; $i++) {
                                            echo "<th>$i</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($student = $student_result->fetch_assoc()) : ?>
                                        <tr>
                                            <td class="name-column"><?php echo $student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']; ?></td>
                                            <?php
                                            $today = date("Y-m-d");
                                            for ($day = 1; $day <= $days_in_month; $day++) {
                                                $current_date = $selected_month . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);

                                                if ($current_date > $today) {
                                                    echo "<td></td>";
                                                } else {
                                                    $check_time = $attendance[$student['id']][$current_date] ?? null;
                                                    $status = "absent";
                                                    $icon_html = "<td class='icon-cross' data-student-id='{$student['id']}' data-date='{$current_date}' data-status='absent'>ข</td>";

                                                    if ($selected_check_type == 'check_in') {
                                                        if ($check_time) {
                                                            if ($check_time <= $time_settings['check_in_end']) {
                                                                $status = "present";
                                                                $icon_html = "<td class='icon-check' data-student-id='{$student['id']}' data-date='{$current_date}' data-status='present'>✔</td>";
                                                            } else {
                                                                $status = "late";
                                                                $icon_html = "<td class='icon-late' data-student-id='{$student['id']}' data-date='{$current_date}' data-status='late'>
                                                                    ส
                                                                </td>";
                                                            }
                                                        }
                                                    } elseif ($selected_check_type == 'check_out') {
                                                        if (!is_null($check_time)) {
                                                            $status = "out";
                                                            $icon_html = "<td class='icon-check' data-student-id='{$student['id']}' data-date='{$current_date}' data-status='out'>✔</td>";
                                                        }
                                                    }

                                                    echo $icon_html;
                                                }
                                            }
                                            ?>
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

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('td[data-status]').forEach(td => {
        td.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            const date = this.dataset.date;
            const status = this.dataset.status;
            const checkType = document.getElementById('check_type').value;

            let options = '';
            if (checkType === 'check_in') {
                // เงื่อนไขสำหรับ "เช็คเข้าเรียน"
                if (status === 'absent' || status === 'null') { // ถ้า absent หรือ null
                    options = `
                        <option value="present">มาเรียน</option>
                        <option value="late">มาสาย</option>
                    `;
                } else if (status === 'present') {
                    options = `
                        <option value="late">มาสาย</option>
                        <option value="absent">ขาดเรียน</option>
                    `;
                } else if (status === 'late') {
                    options = `
                        <option value="present">มาเรียน</option>
                        <option value="absent">ขาดเรียน</option>
                    `;
                }
            } else if (checkType === 'check_out') {
                // เงื่อนไขสำหรับ "เช็คออกเรียน"
                if (status === 'absent' || status === 'null') { // ถ้า absent หรือ null
                    options = `<option value="out">ออกเรียน</option>`;
                } else if (status === 'out') {
                    options = `<option value="absent">ไม่ออกเรียน</option>`;
                }
            }

            // ใช้ SweetAlert2 สำหรับ Modal
            Swal.fire({
                title: checkType === 'check_in' ? 'เปลี่ยนสถานะการมาเรียน' : 'เปลี่ยนสถานะการออกเรียน',
                html: `<select id="statusSelect" class="swal2-select">${options}</select>`,
                confirmButtonText: 'บันทึก',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก',
                preConfirm: () => {
                    const statusSelect = Swal.getPopup().querySelector('#statusSelect');
                    if (!statusSelect.value) {
                        Swal.showValidationMessage('กรุณาเลือกสถานะ');
                    }
                    return statusSelect.value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newStatus = result.value;

                    fetch('backend/update_attendance.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            student_id: studentId,
                            date: date,
                            new_status: newStatus,
                            check_type: checkType
                        })
                    }).then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    }).then(data => {
                        console.log('Response:', data); // ตรวจสอบการตอบกลับ
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'อัปเดตสำเร็จ',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message
                            });
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message
                        });
                    });

                }
            });
        });
    });
</script>