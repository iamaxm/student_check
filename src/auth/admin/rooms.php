<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการห้องเรียน</title>
    <!-- ลิงก์ jQuery และ SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    @session_start();
    include '../config/ConnectDB.php';

    // ดึงข้อมูลชั้นเรียนสำหรับเลือกชั้นเรียนใน dropdown
    $grade_sql = "SELECT id, grade_level FROM grade_level";
    $grade_result = $conn->query($grade_sql);

    // ดึงข้อมูลห้องเรียนพร้อมชั้นเรียนที่เกี่ยวข้อง
    $room_sql = "SELECT room.*, grade_level.grade_level AS grade_name FROM room 
                 LEFT JOIN grade_level ON room.grade_id = grade_level.id";
    $room_result = $conn->query($room_sql);
    ?>

    <div class="container-fluid">
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
                                        <th><center>ชั้นเรียน</center></th>
                                        <th><center>ชื่อห้องเรียน</center></th>
                                        <th><center>สร้างเมื่อ</center></th>
                                        <th><center>แก้ไข</center></th>
                                        <th><center>ลบ</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($room_row = mysqli_fetch_array($room_result)) : ?>
                                        <tr>
                                            <td><center><?php echo $room_row['grade_name'] ?></center></td>
                                            <td><center><?php echo $room_row['name'] ?></center></td>
                                            <td><center><?php echo date("d/m/Y H:i:s", strtotime($room_row['created_at'])); ?></center></td>
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
                                                    <button type="button" class="btn btn-outline-danger m-1 deleteRoomBtn" data-id="<?php echo $room_row['id']; ?>" data-name="<?php echo $room_row['name']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                        </svg>&nbsp;ลบ
                                                    </button>
                                                </center>
                                            </td>
                                        </tr>

                                        <!-- Modal สำหรับแก้ไขห้องเรียน -->
                                        <div class="modal fade" id="editRoomModal<?php echo $room_row['id']; ?>" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form class="editRoomForm" data-id="<?php echo $room_row['id']; ?>">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">แก้ไขห้องเรียน</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="room_id" value="<?php echo $room_row['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">ชั้นเรียน</label>
                                                                <select class="form-select" name="grade_id" required>
                                                                    <?php foreach ($grade_result as $grade) : ?>
                                                                        <option value="<?php echo $grade['id']; ?>" <?php echo ($room_row['grade_id'] == $grade['id']) ? 'selected' : ''; ?>>
                                                                            <?php echo $grade['grade_level']; ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">ชื่อห้องเรียน</label>
                                                                <input type="text" class="form-control" name="name" value="<?php echo $room_row['name']; ?>" required>
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
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับเพิ่มห้องเรียนใหม่ -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addRoomForm">
                    <div class="modal-header">
                        <h5 class="modal-title">สร้างห้องเรียน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชั้นเรียน</label>
                            <select class="form-select" id="grade_id" name="grade_id" required>
                                <option selected disabled hidden>เลือกชั้นเรียน</option>
                                <?php foreach ($grade_result as $grade) : ?>
                                    <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_level']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อห้องเรียน</label>
                            <input type="text" class="form-control" id="name" name="name" required>
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
        // เพิ่มห้องเรียน
        $('#addRoomForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'backend/bn_add_rooms.php',
                type: 'POST',
                data: $(this).serialize(),
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
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถเพิ่มข้อมูลได้", "error");
                }
            });
        });

        // ลบห้องเรียน
        $('.deleteRoomBtn').on('click', function() {
            const roomId = $(this).data('id');
            const roomName = $(this).data('name');
            Swal.fire({
                title: "ยืนยันการลบ",
                text: `คุณต้องการลบห้องเรียน "${roomName}" หรือไม่?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FA896B",
                confirmButtonText: "ใช่, ลบเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'backend/bn_delete_rooms.php',
                        type: 'POST',
                        data: { room_id: roomId },
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

        // แก้ไขห้องเรียน
        $('.editRoomForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const roomId = form.data('id');

            $.ajax({
                url: 'backend/bn_edit_rooms.php',
                type: 'POST',
                data: form.serialize(),
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
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถแก้ไขข้อมูลได้", "error");
                }
            });
        });
    </script>
</body>
</html>
