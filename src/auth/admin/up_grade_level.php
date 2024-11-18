<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch grades for dropdown selection
$grade_sql = "SELECT * FROM grade_level";
$grade_result = $conn->query($grade_sql);

// Fetch rooms for current and new grade selection
$current_rooms = [];
$new_rooms = [];

$selected_grade = isset($_POST['current_grade_id']) ? $_POST['current_grade_id'] : null;
$selected_room = isset($_POST['current_room_id']) ? $_POST['current_room_id'] : null;

// Fetch rooms based on selected current grade
if (!empty($selected_grade)) {
    $room_sql = "SELECT * FROM room WHERE grade_id = ?";
    $stmt = $conn->prepare($room_sql);
    if ($stmt) {
        $stmt->bind_param("i", $selected_grade);
        $stmt->execute();
        $current_rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch students based on selected room
$student_result = null;
if (!empty($selected_room)) {
    $student_sql = "SELECT * FROM student WHERE id_room = ?";
    $stmt = $conn->prepare($student_sql);
    if ($stmt) {
        $stmt->bind_param("i", $selected_room);
        $stmt->execute();
        $student_result = $stmt->get_result();
    }
}

// Handle grade promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote'])) {
    $new_grade_id = isset($_POST['new_grade_id']) ? $_POST['new_grade_id'] : null;
    $new_room_id = isset($_POST['new_room_id']) ? $_POST['new_room_id'] : null;

    // Validate inputs
    if (empty($selected_grade) || empty($selected_room)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาเลือกชั้นเรียนและห้องเรียนปัจจุบัน']);
        exit;
    } elseif (empty($new_grade_id) || empty($new_room_id)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาเลือกชั้นเรียนใหม่และห้องเรียนใหม่']);
        exit;
    } else {
        // Update students' grade and room
        $update_sql = "UPDATE student SET id_grade = ?, id_room = ? WHERE id_room = ?";
        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("iii", $new_grade_id, $new_room_id, $selected_room);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'เลื่อนชั้นเรียนสำเร็จ!']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $stmt->error]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลื่อนชั้นเรียน</title>
    <style>
        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .name-column {
            text-align: left !important;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">เลื่อนชั้นเรียน</h1>
                    </center>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="current_grade_id" class="form-label">ชั้นเรียนปัจจุบัน</label>
                                    <select class="form-select" id="current_grade_id" name="current_grade_id" onchange="loadRooms('current')">
                                        <option value="">-- เลือกชั้นเรียนปัจจุบัน --</option>
                                        <?php while ($grade_row = $grade_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $grade_row['id']; ?>" <?php if ($selected_grade == $grade_row['id']) echo 'selected'; ?>>
                                                <?php echo $grade_row['grade_level']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="current_room_id" class="form-label">ห้องเรียนปัจจุบัน</label>
                                    <select class="form-select" id="current_room_id" name="current_room_id" onchange="this.form.submit()">
                                        <option value="">-- เลือกห้องเรียนปัจจุบัน --</option>
                                        <?php foreach ($current_rooms as $room): ?>
                                            <option value="<?php echo $room['id']; ?>" <?php if ($selected_room == $room['id']) echo 'selected'; ?>>
                                                <?php echo $room['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="new_grade_id" class="form-label">ชั้นเรียนใหม่</label>
                                    <select class="form-select" id="new_grade_id" name="new_grade_id" onchange="loadRooms('new')">
                                        <option value="">-- เลือกชั้นเรียนใหม่ --</option>
                                        <?php $grade_result->data_seek(0);
                                        while ($grade_row = $grade_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $grade_row['id']; ?>">
                                                <?php echo $grade_row['grade_level']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="new_room_id" class="form-label">ห้องเรียนใหม่</label>
                                    <select class="form-select" id="new_room_id" name="new_room_id">
                                        <option value="">-- เลือกห้องเรียนใหม่ --</option>
                                        <?php foreach ($new_rooms as $room): ?>
                                            <option value="<?php echo $room['id']; ?>">
                                                <?php echo $room['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button type="button" name="promote" class="btn btn-success" onclick="promoteStudents()">เลื่อนชั้นเรียน</button>
                                </div>
                            </div>
                        </form>

                        <!-- แสดงรายชื่อนักเรียน -->
                        <?php if (!empty($selected_room)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ชื่อ-นามสกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($student_result && $student_result->num_rows > 0): ?>
                                            <?php while ($student = $student_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="name-column">
                                                        <?php echo htmlspecialchars($student['prefix'] . ' ' . $student['name'] . ' ' . $student['surname']); ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="1">ไม่มีนักเรียนในห้องเรียนนี้</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center mt-3">กรุณาเลือกชั้นเรียนและห้องเรียนปัจจุบันเพื่อแสดงรายชื่อนักเรียน</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadRooms(type) {
            const gradeId = type === 'current' ? $("#current_grade_id").val() : $("#new_grade_id").val();
            $.ajax({
                url: "backend/get_rooms.php",
                type: "POST",
                data: {
                    grade_id: gradeId
                },
                success: function(response) {
                    const rooms = JSON.parse(response);
                    const roomSelect = type === 'current' ? $("#current_room_id") : $("#new_room_id");
                    roomSelect.empty().append('<option value="">-- เลือกห้องเรียน --</option>');
                    rooms.forEach(room => {
                        roomSelect.append(`<option value="${room.id}">${room.name}</option>`);
                    });
                },
                error: function() {
                    Swal.fire("ข้อผิดพลาด", "ไม่สามารถโหลดห้องเรียนได้", "error");
                }
            });
        }


        function promoteStudents() {
            const currentGrade = $("#current_grade_id").val();
            const currentRoom = $("#current_room_id").val();
            const newGrade = $("#new_grade_id").val();
            const newRoom = $("#new_room_id").val();

            if (!currentGrade || !currentRoom) {
                Swal.fire("ข้อผิดพลาด", "กรุณาเลือกชั้นเรียนและห้องเรียนปัจจุบัน", "warning");
                return;
            }

            if (!newGrade || !newRoom) {
                Swal.fire("ข้อผิดพลาด", "กรุณาเลือกชั้นเรียนและห้องเรียนใหม่", "warning");
                return;
            }

            $.ajax({
                url: "backend/promote_grade.php",
                type: "POST",
                data: {
                    current_grade_id: currentGrade,
                    current_room_id: currentRoom,
                    new_grade_id: newGrade,
                    new_room_id: newRoom
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            title: "สำเร็จ!",
                            text: result.message,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire("ข้อผิดพลาด", result.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("ข้อผิดพลาด", "เกิดข้อผิดพลาดในการส่งข้อมูล", "error");
                }
            });
        }
    </script>
</body>

</html>