<?php
@session_start();
include '../config/ConnectDB.php';

// Get current admin id from session username
$username = $_SESSION['username'];
$admin_query = "SELECT id FROM admin WHERE username = '$username'";
$admin_result = $conn->query($admin_query);
$current_admin = mysqli_fetch_assoc($admin_result);
$current_admin_id = $current_admin['id'];

// Fetch rooms for dropdown selection
$room_sql = "SELECT id, name FROM room";
$room_result = $conn->query($room_sql);

// Fetch teachers list for display, joining with room and admin tables
$teacher_sql = "
    SELECT teacher.id, teacher.name, teacher.surname, teacher.created_at, room.name AS room_name, room.id AS id_room, admin.username AS created_by 
    FROM teacher
    LEFT JOIN room ON teacher.id_room = room.id
    LEFT JOIN admin ON teacher.id_admin = admin.id";
$teacher_result = $conn->query($teacher_sql);

// Check if query was successful
if (!$teacher_result) {
    die("Error in SQL query: " . $conn->error);
}
?>
<div class="container-fluid">
    <!-- Title and Add Teacher Button -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">รายชื่อคุณครู</h1>
                </center>
                <div class="card-body p-4">
                    <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                        เพิ่มคุณครู
                    </button>

                    <!-- Teachers Table -->
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
                                        <center>ประจำการห้องเรียน</center>
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
                                <?php while ($teacher_row = $teacher_result->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <center><?php echo $teacher_row['name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $teacher_row['surname']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $teacher_row['room_name']; ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $teacher_row['created_by']; ?></center>
                                        </td>
                                        <td><center><?php echo date("d/m/Y H:i:s", strtotime($teacher_row['created_at'])); ?></center></td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-warning m-1" data-bs-toggle="modal" data-bs-target="#editTeacherModal<?php echo $teacher_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>&nbsp;แก้ไข
                                                </button>
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-danger m-1" data-bs-toggle="modal" data-bs-target="#deleteTeacherModal<?php echo $teacher_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>&nbsp;ลบ
                                                </button>
                                            </center>
                                        </td>
                                    </tr>

                                    <!-- Edit Teacher Modal -->
                                    <div class="modal fade" id="editTeacherModal<?php echo $teacher_row['id']; ?>" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_edit_teacher.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลคุณครู</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="teacher_id" value="<?php echo $teacher_row['id']; ?>">

                                                        <div class="mb-3">
                                                            <label for="editTeacherName<?php echo $teacher_row['id']; ?>" class="form-label">ชื่อ</label>
                                                            <input type="text" class="form-control" id="editTeacherName<?php echo $teacher_row['id']; ?>" name="name" value="<?php echo $teacher_row['name']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editTeacherSurname<?php echo $teacher_row['id']; ?>" class="form-label">นามสกุล</label>
                                                            <input type="text" class="form-control" id="editTeacherSurname<?php echo $teacher_row['id']; ?>" name="surname" value="<?php echo $teacher_row['surname']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editRoom<?php echo $teacher_row['id']; ?>" class="form-label">ห้องเรียน</label>
                                                            <select class="form-select" id="editRoom<?php echo $teacher_row['id']; ?>" name="id_room" required>
                                                                <?php
                                                                // Reset the pointer of $room_result
                                                                mysqli_data_seek($room_result, 0);
                                                                while ($room = $room_result->fetch_assoc()) : ?>
                                                                    <option value="<?php echo $room['id']; ?>" <?php echo ($teacher_row['id_room'] == $room['id']) ? 'selected' : ''; ?>>
                                                                        <?php echo $room['name']; ?>
                                                                    </option>
                                                                <?php endwhile; ?>
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

                                    <!-- Delete Teacher Modal -->
                                    <div class="modal fade" id="deleteTeacherModal<?php echo $teacher_row['id']; ?>" tabindex="-1" aria-labelledby="deleteTeacherModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_delete_teacher.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ยืนยันการลบคุณครู</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณแน่ใจหรือไม่ว่าต้องการลบคุณครู "<strong><?php echo $teacher_row['name'] . " " . $teacher_row['surname']; ?></strong>"?</p>
                                                        <input type="hidden" name="teacher_id" value="<?php echo $teacher_row['id']; ?>">
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

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="backend/bn_add_teacher.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มคุณครู</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_admin" value="<?php echo $current_admin_id; ?>">
                    <div class="mb-3">
                        <label for="teacherName" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="teacherName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="teacherSurname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="teacherSurname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_room" class="form-label">ห้องเรียน</label>
                        <select class="form-select" id="id_room" name="id_room" required>
                            <option selected="" disabled hidden>เลือกห้องเรียน</option>
                            <?php
                            mysqli_data_seek($room_result, 0);
                            while ($room = $room_result->fetch_assoc()) : ?>
                                <option value="<?php echo $room['id']; ?>"><?php echo $room['name']; ?></option>
                            <?php endwhile; ?>
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