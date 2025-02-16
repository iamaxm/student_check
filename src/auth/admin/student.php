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

// Fetch rooms for dropdown selection based on selected grade
$selected_grade = isset($_POST['grade_id']) ? $_POST['grade_id'] : null;

$room_sql = "SELECT * FROM room";
if ($selected_grade) {
    $room_sql .= " WHERE grade_id = '$selected_grade'";
}
$room_result = $conn->query($room_sql);

// Fetch students based on selected grade and room
$selected_room = isset($_POST['room_id']) ? $_POST['room_id'] : null;

// Fetch students list for display, joining with room, grade, and teacher tables
$student_sql = "
    SELECT student.id, student.prefix, student.name, student.surname, student.created_at, student.profile_image,
           student.id_grade, student.id_room, student.id_teacher, -- Add these fields
           room.name AS room_name, grade_level.grade_level AS grade_name, 
           teacher.name AS teacher_name, teacher.surname AS teacher_surname, 
           admin.username AS created_by 
    FROM student
    LEFT JOIN room ON student.id_room = room.id
    LEFT JOIN grade_level ON student.id_grade = grade_level.id
    LEFT JOIN teacher ON student.id_teacher = teacher.id
    LEFT JOIN admin ON student.id_admin = admin.id WHERE 1=1";
if ($selected_grade) {
    $student_sql .= " AND id_grade = '$selected_grade'";
}
if ($selected_room) {
    $student_sql .= " AND student.id_room = '$selected_room'";
}

$student_result = $conn->query($student_sql);


// ตรวจสอบว่าการ query สำเร็จหรือไม่
if (!$student_result) {
    die("Error in SQL query: " . $conn->error);
}
?>
<style>
    @media (max-width: 576px) {
        .card-body button {
            display: block;
            width: 100%;
            /* ปรับปุ่มให้มีความกว้างเต็ม */
            margin-bottom: 10px;
        }
    }
</style>

<!-- ลิงก์ jQuery และ SweetAlert2 -->
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid">
    <!-- Title and Add Student Button -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">รายชื่อนักเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <div class="card-body p-4 d-flex flex-wrap">
                        <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            เพิ่มนักเรียน
                        </button>
                        <button type="button" class="btn btn-outline-info m-1" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                            นำเข้าข้อมูลนักเรียน (Excel)
                        </button>
                        <button id="generateQRCodes" class="btn btn-outline-success m-1">สร้างและดาวน์โหลด QR Code</button>
                    </div>

                    <form method="POST" class="mb-3">
                        <div class="row">
                            <!-- เลือกชั้นเรียน -->
                            <div class="col-md-3">
                                <label for="grade_id" class="form-label">ชั้นเรียน</label>
                                <select class="form-select" id="grade_id" name="grade_id" onchange="this.form.submit()">
                                    <option value="">-- ชั้นเรียนทั้งหมด --</option>
                                    <?php
                                    if ($grade_result && $grade_result->num_rows > 0) {
                                        while ($grade_row = $grade_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $grade_row['id']; ?>" <?php if ($selected_grade == $grade_row['id']) echo 'selected'; ?>>
                                                <?php echo $grade_row['grade_level']; ?>
                                            </option>
                                    <?php endwhile;
                                    } else {
                                        echo '<option value="" disabled>ไม่มีข้อมูลชั้นเรียน</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <!-- เลือกห้องเรียน -->
                            <div class="col-md-3">
                                <label for="room_id" class="form-label">ห้องเรียน</label>
                                <select class="form-select" id="room_id" name="room_id" onchange="this.form.submit()">
                                    <option value="">-- ห้องเรียนทั้งหมด --</option>
                                    <?php
                                    if ($room_result && $room_result->num_rows > 0) {
                                        while ($room_row = $room_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $room_row['id']; ?>" <?php if ($selected_room == $room_row['id']) echo 'selected'; ?>>
                                                <?php echo $room_row['name']; ?>
                                            </option>
                                    <?php endwhile;
                                    } else {
                                        echo '<option value="" disabled>ไม่มีข้อมูลห้องเรียน</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                    </form>
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
                                <?php
                                if ($student_result && $student_result->num_rows > 0) {
                                    while ($student_row = $student_result->fetch_assoc()) : ?>
                                      <tr data-bs-toggle="modal" data-bs-target="#viewStudentModal<?php echo $student_row['id']; ?>">
                                            <td>
                                                <center><?php echo $student_row['prefix'] . $student_row['name']; ?></center>
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
                                            <td>
                                                <center><?php echo date("d/m/Y H:i:s", strtotime($student_row['created_at'])); ?></center>
                                            </td>
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
                                                    <button type="button" class="btn btn-outline-danger m-1 deleteStudentBtn" data-id="<?php echo $student_row['id']; ?>" data-prefix="<?php echo $student_row['prefix']; ?>" data-name="<?php echo $student_row['name']; ?>" data-surname="<?php echo $student_row['surname']; ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                        </svg>&nbsp;ลบ
                                                    </button>
                                                </center>
                                            </td>
                                        </tr>

                                        <!-- Modal แสดงรายละเอียดนักเรียน -->
                                       <div class="modal fade" id="viewStudentModal<?php echo $student_row['id']; ?>" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">รายละเอียดนักเรียน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="text-center mb-3">
                                                            <img src="../../uploads/<?php echo $student_row['profile_image']; ?>" 
                                                                alt="Profile Image" width="150" height="150" 
                                                                style="object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                                                        </div>
                                                        <p><strong>ชื่อ :</strong> <?php echo $student_row['prefix'] . $student_row['name']; ?></p>
                                                        <p><strong>นามสกุล :</strong> <?php echo $student_row['surname']; ?></p>
                                                        <p><strong>ชั้นเรียน :</strong> <?php echo $student_row['grade_name']; ?></p>
                                                        <p><strong>ห้องเรียน :</strong> <?php echo $student_row['room_name']; ?></p>
                                                        <p><strong>ครูที่ดูแล :</strong> <?php echo $student_row['teacher_name'] . ' '.$student_row['teacher_surname']; ?></p>
                                                        <p><strong>สร้างโดย :</strong> <?php echo $student_row['created_by']; ?></p>
                                                        <p><strong>วันที่สร้าง :</strong> <?php echo date("d/m/Y H:i:s", strtotime($student_row['created_at'])); ?></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Student Modal -->
                                        <div class="modal fade" id="editStudentModal<?php echo $student_row['id']; ?>" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form class="editStudentForm" data-id="<?php echo $student_row['id']; ?>" enctype="multipart/form-data">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">แก้ไขข้อมูลนักเรียน</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="student_id" value="<?php echo $student_row['id']; ?>">
                                                            <div class="mb-3 text-center">
                                                                <img id="profilePreview<?php echo $student_row['id']; ?>" 
                                                                    src="../../uploads/<?php echo $student_row['profile_image']; ?>" 
                                                                    alt="Profile Image" 
                                                                    width="150" 
                                                                    height="150" 
                                                                    style="object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                                                            </div>
                                                            <!-- Prefix Field -->
                                                            <div class="mb-3">
                                                                <label for="editPrefix<?php echo $student_row['id']; ?>" class="form-label">คำนำหน้าชื่อ</label>
                                                                <select class="form-select" id="editPrefix<?php echo $student_row['id']; ?>" name="prefix" required>
                                                                    <option selected="" disabled hidden>เลือกคำนำหน้าชื่อ</option>
                                                                    <option value="นาย" <?php echo ($student_row['prefix'] == 'นาย') ? 'selected' : ''; ?>>นาย</option>
                                                                    <option value="นางสาว" <?php echo ($student_row['prefix'] == 'นางสาว') ? 'selected' : ''; ?>>นางสาว</option>
                                                                    <option value="เด็กชาย" <?php echo ($student_row['prefix'] == 'เด็กชาย') ? 'selected' : ''; ?>>เด็กชาย</option>
                                                                    <option value="เด็กหญิง" <?php echo ($student_row['prefix'] == 'เด็กหญิง') ? 'selected' : ''; ?>>เด็กหญิง</option>
                                                                </select>
                                                            </div>

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
                                                            <div class="mb-3">
                                                                <label for="editProfileImage<?php echo $student_row['id']; ?>" class="form-label">อัปโหลดรูปภาพ</label>
                                                                <input type="file" class="form-control" id="editProfileImage<?php echo $student_row['id']; ?>" name="profile_image" accept="image/*">
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
                                <?php endwhile;
                                } else {
                                    echo '<tr><td colspan="9" class="text-center">ไม่มีข้อมูลนักเรียน</td></tr>';
                                }
                                ?>
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
            <form id="addStudentForm">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มนักเรียน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_admin" value="<?php echo $current_admin_id; ?>">
                    <div class="mb-3">
                        <label for="editProfileImage<?php echo $student_row['id']; ?>" class="form-label">อัปโหลดรูปภาพ</label>
                        <input type="file" class="form-control" id="editProfileImage<?php echo $student_row['id']; ?>" name="profile_image" accept="image/*">
                    </div>
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
                    <div class="mb-3">
                        <label for="studentName" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="studentName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentSurname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="studentSurname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_grade" class="form-label">ชั้นเรียน</label>
                        <select class="form-select" id="id_grade" name="id_grade" required>
                            <option selected="" disabled hidden>เลือกชั้นเรียน</option>
                            <?php foreach ($grade_result as $grade) : ?>
                                <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_level']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_room" class="form-label">ห้องเรียน</label>
                        <select class="form-select" id="id_room" name="id_room" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="id_teacher" class="form-label">ครูที่ดูแล</label>
                        <select class="form-select" id="id_teacher" name="id_teacher" required></select>
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

<!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importExcelForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">นำเข้าข้อมูลนักเรียน (Excel)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_admin" value="<?php echo $current_admin_id; ?>">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">เลือกไฟล์ Excel</label>
                        <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls, .xlsx" required>
                    </div>
                    <div class="mb-3">
                        <label for="importGrade" class="form-label">ชั้นเรียน</label>
                        <select class="form-select" id="importGrade" name="id_grade" required>
                            <option selected="" disabled hidden>เลือกชั้นเรียน</option>
                            <?php foreach ($grade_result as $grade) : ?>
                                <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_level']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_room" class="form-label">ห้องเรียน</label>
                        <select class="form-select" id="importRoom" name="id_room" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="id_teacher" class="form-label">ครูที่ดูแล</label>
                        <select class="form-select" id="importTeacher" name="id_teacher" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">นำเข้า</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('generateQRCodes').addEventListener('click', function() {
        // window.location.href = '../qrcode/generate_qr_codes.php?grade_id=<?php echo $selected_grade ?? 0 ?>&room_id=<?php echo $selected_room ?? 0 ?>';
        $.ajax({
            url: '../qrcode/generate_qr_codes.php?grade_id=<?php echo $selected_grade ?? 0 ?>&room_id=<?php echo $selected_room ?? 0 ?>',
            type: 'GET',
            xhrFields: {
                responseType: 'blob' // Important for handling binary data
            },
            success: function(response) {
                try {
                    // Validate that the response is a Blob
                    if (!(response instanceof Blob)) {
                        throw new Error('ไม่พบนักเรียนในชั้นหรือห้องที่เลือก');
                    }

                    // Use FileReader to validate if it's a ZIP file
                    const fileReader = new FileReader();
                    fileReader.onload = function() {
                        const arr = new Uint8Array(this.result);
                        const isZip = arr[0] === 0x50 && arr[1] === 0x4B; // Check for ZIP magic number "PK"

                        if (isZip) {
                            // Create a Blob from the response
                            const blob = new Blob([response], {
                                type: 'application/zip'
                            });

                            // Create a link to download the Blob
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = 'qrcode_students.zip';
                            link.click();

                            // Swal.fire({
                            //     title: 'สำเร็จ!',
                            //     text: 'ไฟล์ ZIP ถูกดาวน์โหลดเรียบร้อยแล้ว',
                            //     icon: 'success',
                            //     confirmButtonText: 'ตกลง'
                            // });
                        } else {
                            throw new Error('The file is not a valid ZIP file.');
                        }
                    };
                    fileReader.readAsArrayBuffer(response);
                } catch (error) {
                    Swal.fire({
                        title: 'ข้อผิดพลาด!',
                        text: error.message || 'เกิดข้อผิดพลาดในการประมวลผล',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: 'เกิดข้อผิดพลาดในการส่งคำขอหรือการตอบกลับ',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        });

    });
</script>


<script>
    function fetchRoomAndTeacher(gradeId, roomSelector, teacherSelector) {
        $.ajax({
            url: 'backend/fetch_room_teacher.php',
            type: 'POST',
            data: {
                grade_id: gradeId
            },
            dataType: 'json',
            success: function(response) {
                $(roomSelector).empty().append('<option selected disabled hidden>เลือกห้องเรียน</option>');
                $(teacherSelector).empty().append('<option selected disabled hidden>เลือกครูที่ดูแล</option>');
                $.each(response.rooms, function(index, room) {
                    $(roomSelector).append('<option value="' + room.id + '">' + room.name + '</option>');
                });
                $.each(response.teachers, function(index, teacher) {
                    $(teacherSelector).append('<option value="' + teacher.id + '">' + teacher.name + ' ' + teacher.surname + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    // Add Student Modal
    $('#id_grade').change(function() {
        fetchRoomAndTeacher($(this).val(), '#id_room', '#id_teacher');
    });

    // Import Excel Modal
    $('#importGrade').change(function() {
        fetchRoomAndTeacher($(this).val(), '#importRoom', '#importTeacher');
    });


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

<script>
    // เพิ่มนักเรียน
    $('#addStudentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this); // Create FormData object
        $.ajax({
            url: 'backend/bn_add_student.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false, // Important: Prevent jQuery from processing the data
            contentType: false, // Important: Prevent jQuery from setting content type
            success: function(response) {
                Swal.fire({
                    title: response.title,
                    text: response.message,
                    icon: response.type,
                    timer: 1500,
                    showConfirmButton: response.type !== 'success'
                }).then(() => {
                    if (response.type === 'success') location.reload();
                });
            },
            error: function(xhr, status, error) {
                Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถเพิ่มข้อมูลได้", "error");
            }
        });
    });

    // แก้ไขนักเรียน
    $('.editStudentForm').on('submit', function(e) { 
        e.preventDefault();
        const formData = new FormData(this); // Create FormData object
        
        $.ajax({
            url: 'backend/bn_edit_student.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false, // Important: Prevent jQuery from processing the data
            contentType: false, // Important: Prevent jQuery from setting content type
            success: function(response) {
                Swal.fire({
                    title: response.title,
                    text: response.message,
                    icon: response.type,
                    timer: 1500,
                    showConfirmButton: response.type !== 'success'
                }).then(() => {
                    if (response.type === 'success') location.reload();
                });
            },
            error: function() {
                Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถแก้ไขข้อมูลได้", "error");
            }
        });
    });


    // ลบนักเรียน
    $('.deleteStudentBtn').on('click', function() {
        const studentId = $(this).data('id');
        const studentPrefix = $(this).data('prefix');
        const studentName = $(this).data('name');
        const studentSurname = $(this).data('surname');
        Swal.fire({
            title: "ยืนยันการลบ",
            text: `คุณต้องการลบนักเรียน "${studentPrefix}${studentName}  ${studentSurname}" หรือไม่?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FA896B",
            confirmButtonText: "ใช่, ลบเลย!",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'backend/bn_delete_student.php',
                    type: 'POST',
                    data: {
                        student_id: studentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: response.type,
                            timer: 1500,
                            showConfirmButton: response.type !== 'success'
                        }).then(() => {
                            if (response.type === 'success') location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถลบข้อมูลได้", "error");
                    }
                });
            }
        });
    });

    // Handle Import Excel Form Submission
    $('#importExcelForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '../excel/import_excel.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const res = JSON.parse(response); // แปลง response เป็น JSON Object
                    if (res.type === 'success') {
                        Swal.fire({
                            title: "สำเร็จ!",
                            text: res.message,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else if (res.type === 'error' || res.type === 'warning') {
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด!",
                            text: res.message,
                            icon: res.type === 'error' ? "error" : "warning",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        title: "ผิดพลาด!",
                        text: "เกิดข้อผิดพลาดในการประมวลผลข้อมูล",
                        icon: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: "ผิดพลาด!",
                    text: "ไม่สามารถนำเข้าข้อมูลได้",
                    icon: "error",
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
</script>