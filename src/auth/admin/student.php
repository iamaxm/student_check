<?php
@session_start();
include '../config/ConnectDB.php';

// Get current admin id from session username
$username = $_SESSION['username'];
$admin_query = "SELECT id FROM admin WHERE username = '$username'";
$admin_result = $conn->query($admin_query);
$current_admin = mysqli_fetch_assoc($admin_result);
$current_admin_id = $current_admin['id'];

// Fetch grades for dropdown selection
$grade_sql = "SELECT id, grade_level FROM grade_level";
$grade_result = $conn->query($grade_sql);

// Fetch rooms for dropdown selection
$room_sql = "SELECT id, name FROM room";
$room_result = $conn->query($room_sql);

// Fetch teachers for dropdown selection
$teacher_sql = "SELECT * FROM teacher";
$teacher_result = $conn->query($teacher_sql);

// Fetch students list for display, joining with room, grade, and teacher tables
$student_sql = "
    SELECT student.id, student.prefix, student.name, student.surname, student.created_at,
           student.id_grade, student.id_room, student.id_teacher, -- Add these fields
           room.name AS room_name, grade_level.grade_level AS grade_name, 
           teacher.name AS teacher_name, teacher.surname AS teacher_surname, 
           admin.username AS created_by 
    FROM student
    LEFT JOIN room ON student.id_room = room.id
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    LEFT JOIN teacher ON student.id_teacher = teacher.id
    LEFT JOIN admin ON student.id_admin = admin.id";
$student_result = $conn->query($student_sql);


// ตรวจสอบว่าการ query สำเร็จหรือไม่
if (!$student_result) {
    die("Error in SQL query: " . $conn->error);
}
?>
<div class="container-fluid">
    <!-- Title and Add Student Button -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">รายชื่อนักเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        เพิ่มนักเรียน
                    </button>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>
                                        <center>ชื่อ</center>
                                    </th>
                                    <th>
                                        <center>นามสกุล</center>
                                    </th>
                                    <th>
                                        <center>ชั้นเรียน</center>
                                    </th>
                                    <th>
                                        <center>ห้องเรียน</center>
                                    </th>
                                    <th>
                                        <center>ครูที่ดูแล</center>
                                    </th>
                                    <th>
                                        <center>สร้างโดย</center>
                                    </th>
                                    <th>
                                        <center>สร้างเมื่อ</center>
                                    </th>
                                    <th>
                                        <center>แก้ไข</center>
                                    </th>
                                    <th>
                                        <center>ลบ</center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($student_row = $student_result->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <center><?php echo $student_row['prefix'] . '' . $student_row['name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $student_row['surname']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $student_row['grade_name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $student_row['room_name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $student_row['teacher_name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $student_row['created_by']; ?></center>
                                        </td>
                                        <td><center><?php echo date("d/m/Y H:i:s", strtotime($student_row['created_at'])); ?></center></td>

                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-warning m-1" data-bs-toggle="modal" data-bs-target="#editStudentModal<?php echo $student_row['id']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>&nbsp;แก้ไข
                                                </button>
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-danger m-1" data-bs-toggle="modal" data-bs-target="#deleteStudentModal<?php echo $student_row['id']; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>&nbsp;ลบ
                                                </button>
                                            </center>
                                        </td>
                                    </tr>

                                    <!-- Edit Student Modal -->
                                    <div class="modal fade" id="editStudentModal<?php echo $student_row['id']; ?>" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_edit_student.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลนักเรียน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="student_id" value="<?php echo $student_row['id']; ?>">

                                                        <!-- Name Fields -->
                                                        <div class="mb-3">
                                                            <label for="editStudentName<?php echo $student_row['id']; ?>" class="form-label">ชื่อ</label>
                                                            <input type="text" class="form-control" id="editStudentName<?php echo $student_row['id']; ?>" name="name" value="<?php echo $student_row['name']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editStudentSurname<?php echo $student_row['id']; ?>" class="form-label">นามสกุล</label>
                                                            <input type="text" class="form-control" id="editStudentSurname<?php echo $student_row['id']; ?>" name="surname" value="<?php echo $student_row['surname']; ?>" required>
                                                        </div>

                                                        <!-- Grade Level Field -->
                                                        <div class="mb-3">
                                                            <label for="editGrade<?php echo $student_row['id']; ?>" class="form-label">ชั้นเรียน</label>
                                                            <select class="form-select" id="editGrade<?php echo $student_row['id']; ?>" name="id_grade" required onchange="fetchRoomAndTeacherForEdit(this.value, <?php echo $student_row['id']; ?>)">
                                                                <option selected="" disabled hidden>เลือกชั้นเรียน</option>
                                                                <?php foreach ($grade_result as $grade) : ?>
                                                                    <option value="<?php echo $grade['id']; ?>" <?php echo ($student_row['id_grade'] == $grade['id']) ? 'selected' : ''; ?>><?php echo $grade['grade_level']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <!-- Room and Teacher Fields -->
                                                        <div class="mb-3">
                                                            <label for="editRoom<?php echo $student_row['id']; ?>" class="form-label">ห้องเรียน</label>
                                                            <select class="form-select" id="editRoom<?php echo $student_row['id']; ?>" name="id_room" required onchange="fetchTeacherByRoomForEdit(this.value, <?php echo $student_row['id']; ?>)">
                                                                <option selected="" disabled hidden>เลือกห้องเรียน</option>
                                                                <?php foreach ($room_result as $room) : ?>
                                                                    <option value="<?php echo $room['id']; ?>" <?php echo ($student_row['id_room'] == $room['id']) ? 'selected' : ''; ?>><?php echo $room['name']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="editTeacher<?php echo $student_row['id']; ?>" class="form-label">ครูที่ดูแล</label>
                                                            <select class="form-select" id="editTeacher<?php echo $student_row['id']; ?>" name="id_teacher" required>
                                                                <option selected="" disabled hidden>เลือกครูที่ดูแล</option>
                                                                <?php foreach ($teacher_result as $teacher) : ?>
                                                                    <option value="<?php echo $teacher['id']; ?>" <?php echo ($student_row['id_teacher'] == $teacher['id']) ? 'selected' : ''; ?>><?php echo $teacher['name'] . ' ' . $teacher['surname']; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Delete Student Modal -->
                                    <div class="modal fade" id="deleteStudentModal<?php echo $student_row['id']; ?>" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_delete_student.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ยืนยันการลบนักเรียน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณแน่ใจหรือไม่ว่าต้องการลบนักเรียน "<strong><?php echo $student_row['prefix'] . '' . $student_row['name'] . " " . $student_row['surname']; ?></strong>"?</p>
                                                        <input type="hidden" name="student_id" value="<?php echo $student_row['id']; ?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="backend/bn_add_student.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มนักเรียน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_admin" value="<?php echo $current_admin_id; ?>">

                    <!-- Prefix Field -->
                    <div class="mb-3">
                        <label for="prefix" class="form-label">คำนำหน้าชื่อ</label>
                        <select class="form-select" id="prefix" name="prefix" required>
                            <option selected="" disabled hidden>เลือกคำนำหน้าชื่อ</option>
                            <option value="นาย">นาย</option>
                            <option value="นางสาว">นางสาว</option>
                            <option value="เด็กชาย">เด็กชาย</option>
                            <option value="เด็กหญิง">เด็กหญิง</option>
                        </select>
                    </div>

                    <!-- Name and Surname Fields -->
                    <div class="mb-3">
                        <label for="studentName" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="studentName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentSurname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="studentSurname" name="surname" required>
                    </div>

                    <!-- Grade Level Field -->
                    <div class="mb-3">
                        <label for="id_grade" class="form-label">ชั้นเรียน</label>
                        <select class="form-select" id="id_grade" name="id_grade" required onchange="fetchRoomAndTeacher(this.value)">
                            <option selected="" disabled hidden>เลือกชั้นเรียน</option>
                            <?php foreach ($grade_result as $grade) : ?>
                                <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_level']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Room and Teacher Fields (Populated by JavaScript) -->
                    <div class="mb-3">
                        <label for="id_room" class="form-label">ห้องเรียน</label>
                        <select class="form-select" id="id_room" name="id_room" required onchange="fetchTeacherByRoom(this.value)">
                            <option selected="" disabled hidden>เลือกห้องเรียน</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_teacher" class="form-label">ครูที่ดูแล</label>
                        <select class="form-select" id="id_teacher" name="id_teacher" required>
                            <option selected="" disabled hidden>เลือกครูที่ดูแล</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function fetchRoomAndTeacher(gradeId) {
        $.ajax({
            url: 'backend/fetch_room_teacher.php',
            type: 'POST',
            data: {
                grade_id: gradeId
            },
            dataType: 'json',
            success: function(response) {
                // Clear existing options
                $('#id_room').empty().append('<option selected disabled hidden>เลือกห้องเรียน</option>');
                $('#id_teacher').empty().append('<option selected disabled hidden>เลือกครูที่ดูแล</option>');

                // Populate rooms
                $.each(response.rooms, function(index, room) {
                    $('#id_room').append('<option value="' + room.id + '">' + room.name + '</option>');
                });

                // Populate teachers for the grade level initially
                $.each(response.teachers, function(index, teacher) {
                    $('#id_teacher').append('<option value="' + teacher.id + '">' + teacher.name + ' ' + teacher.surname + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + error);
            }
        });
    }

    // Fetch teachers by room selection
    function fetchTeacherByRoom(roomId) {
        $.ajax({
            url: 'backend/fetch_teacher_by_room.php', // Ensure this path is correct
            type: 'POST',
            data: {
                room_id: roomId
            },
            dataType: 'json',
            success: function(response) {
                // Clear existing teachers
                $('#id_teacher').empty().append('<option selected disabled hidden>เลือกครูที่ดูแล</option>');

                // Populate teachers for the selected room
                $.each(response.teachers, function(index, teacher) {
                    $('#id_teacher').append('<option value="' + teacher.id + '">' + teacher.name + ' ' + teacher.surname + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + error);
            }
        });
    }

    // Call fetchTeacherByRoom on room selection change
    $('#id_room').change(function() {
        const roomId = $(this).val();
        fetchTeacherByRoom(roomId);
    });
</script>

<script>
    // Function to fetch rooms and teachers based on grade for the edit modal
    function fetchRoomAndTeacherForEdit(gradeId, studentId) {
        $.ajax({
            url: 'backend/fetch_room_teacher.php',
            type: 'POST',
            data: {
                grade_id: gradeId
            },
            dataType: 'json',
            success: function(response) {
                // Clear existing options for the specific student
                $('#editRoom' + studentId).empty().append('<option selected disabled hidden>เลือกห้องเรียน</option>');
                $('#editTeacher' + studentId).empty().append('<option selected disabled hidden>เลือกครูที่ดูแล</option>');

                // Populate rooms
                $.each(response.rooms, function(index, room) {
                    $('#editRoom' + studentId).append('<option value="' + room.id + '">' + room.name + '</option>');
                });

                // Populate teachers
                $.each(response.teachers, function(index, teacher) {
                    $('#editTeacher' + studentId).append('<option value="' + teacher.id + '">' + teacher.name + ' ' + teacher.surname + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + error);
            }
        });
    }

    // Function to fetch teachers based on room selection for the edit modal
    function fetchTeacherByRoomForEdit(roomId, studentId) {
        $.ajax({
            url: 'backend/fetch_teacher_by_room.php',
            type: 'POST',
            data: {
                room_id: roomId
            },
            dataType: 'json',
            success: function(response) {
                // Clear existing teachers for the specific student
                $('#editTeacher' + studentId).empty().append('<option selected disabled hidden>เลือกครูที่ดูแล</option>');

                // Populate teachers
                $.each(response.teachers, function(index, teacher) {
                    $('#editTeacher' + studentId).append('<option value="' + teacher.id + '">' + teacher.name + ' ' + teacher.surname + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + error);
            }
        });
    }
</script>