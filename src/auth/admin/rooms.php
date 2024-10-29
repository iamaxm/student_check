<?php
@session_start();
include '../config/ConnectDB.php';

// Fetch grade levels for dropdown selection
$grade_sql = "SELECT id, grade_level FROM grade_level";
$grade_result = $conn->query($grade_sql);

// Fetch room list along with associated grade level
$room_sql = "SELECT room.*, grade_level.grade_level AS grade_name FROM room 
             LEFT JOIN grade_level ON room.grade_id = grade_level.id";
$room_result = $conn->query($room_sql);
?>
<div class="container-fluid">
    <!-- Row 1 -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ห้องเรียน</h1>
                </center>
                <div class="card-body p-4">
                    <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        สร้างห้องเรียน
                    </button>
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>
                                        <center>ชั้นเรียน</center>
                                    </th>
                                    <th>
                                        <center>ชื่อห้องเรียน</center>
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
                                <?php while ($room_row = mysqli_fetch_array($room_result)) : ?>
                                    <tr>
                                        <td>
                                            <center><?php echo $room_row['grade_name'] ?></center>
                                        </td>
                                        <td>
                                            <center><?php echo $room_row['name'] ?></center>
                                        </td>

                                        <td>
                                            <center><?php echo date("d/m/Y H:i:s", strtotime($room_row['created_at'])); ?></center>
                                        </td>

                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-warning m-1" data-bs-toggle="modal" data-bs-target="#editRoomModal<?php echo $room_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>&nbsp;แก้ไข
                                                </button>
                                            </center>
                                        </td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-danger m-1" data-bs-toggle="modal" data-bs-target="#deleteRoomModal<?php echo $room_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>&nbsp;ลบ
                                                </button>
                                            </center>
                                        </td>
                                    </tr>

                                    <!-- Modal for editing room -->
                                    <div class="modal fade" id="editRoomModal<?php echo $room_row['id']; ?>" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_edit_rooms.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลห้องเรียน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="room_id" value="<?php echo $room_row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="editGrade" class="form-label">ชั้นเรียน</label>
                                                            <select class="form-select" id="editGrade" name="grade_id" required>
                                                                <?php foreach ($grade_result as $grade) : ?>
                                                                    <option value="<?php echo $grade['id']; ?>" <?php echo ($room_row['grade_id'] == $grade['id']) ? 'selected' : ''; ?>>
                                                                        <?php echo $grade['grade_level']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editRoom" class="form-label">ชื่อห้องเรียน</label>
                                                            <input type="text" class="form-control" id="editRoom" name="name" value="<?php echo $room_row['name']; ?>" required>
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

                                    <!-- Confirmation modal for delete -->
                                    <div class="modal fade" id="deleteRoomModal<?php echo $room_row['id']; ?>" tabindex="-1" aria-labelledby="deleteRoomModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_delete_rooms.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ยืนยันการลบห้องเรียน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณแน่ใจหรือไม่ว่าต้องการลบห้องเรียน "<strong><?php echo $room_row['name']; ?></strong>"?</p>
                                                        <input type="hidden" name="room_id" value="<?php echo $room_row['id']; ?>">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding a new room -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="backend/bn_add_rooms.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">สร้างห้องเรียน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addGrade" class="form-label">ชั้นเรียน</label>
                        <select class="form-select" id="addGrade" name="grade_id" required>
                            <option selected="" disabled hidden>เลือกชั้นเรียน</option>
                            <?php foreach ($grade_result as $grade) : ?>
                                <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_level']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="Room" class="form-label">ชื่อห้องเรียน (เช่น 3/1)</label>
                        <input type="text" class="form-control" id="Room" name="name" required>
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