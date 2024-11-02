<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการชั้นเรียน</title>
    <!-- ลิงก์ jQuery และ SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php
    @session_start();
    include '../config/ConnectDB.php';

    $grade_sql = "SELECT * FROM `grade_level`";
    $grade_result = $conn->query($grade_sql);
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col d-flex align-items-stretch">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ชั้นเรียน</h1>
                    </center>
                    <div class="card-body p-4">
                        <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addGradeLevelModal">
                            สร้างชั้นเรียน
                        </button>
                        <div class="table-responsive">
                            <table class="table text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th><center>ชั้นเรียน</center></th>
                                        <th><center>สร้างเมื่อ</center></th>
                                        <th><center>แก้ไข</center></th>
                                        <th><center>ลบ</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($grade_row = mysqli_fetch_array($grade_result)) : ?>
                                        <tr>
                                            <td><center><?php echo $grade_row['grade_level'] ?></center></td>
                                            <td><center><?php echo date("d/m/Y H:i:s", strtotime($grade_row['created_at'])); ?></center></td>
                                            <td>
                                                <center>
                                                    <button type="button" class="btn btn-outline-warning m-1 editGradeBtn" data-id="<?php echo $grade_row['id']; ?>" data-level="<?php echo $grade_row['grade_level']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>&nbsp;แก้ไข
                                                    </button>
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    <button type="button" class="btn btn-outline-danger m-1 deleteGradeBtn" data-id="<?php echo $grade_row['id']; ?>" data-level="<?php echo $grade_row['grade_level']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                        </svg>&nbsp;ลบ
                                                    </button>
                                                </center>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับเพิ่มชั้นเรียนใหม่ -->
    <div class="modal fade" id="addGradeLevelModal" tabindex="-1" aria-labelledby="addGradeLevelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addGradeForm">
                    <div class="modal-header">
                        <h5 class="modal-title">สร้างชั้นเรียน</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อชั้นเรียน (เช่น ม.3)</label>
                            <input type="text" class="form-control" id="gradeLevel" name="grade_level" required>
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
        // เพิ่มชั้นเรียน
        $('#addGradeForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'backend/bn_add_grade_level.php',
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
                        if (response.type === 'success') {
                            location.reload();
                        }
                    });
                },
                error: function() {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถเพิ่มข้อมูลได้", "error");
                }
            });
        });

        // ลบชั้นเรียน
        $('.deleteGradeBtn').on('click', function() {
            var gradeId = $(this).data('id');
            var gradeLevel = $(this).data('level');

            Swal.fire({
                title: "ยืนยันการลบ",
                text: "คุณต้องการลบชั้นเรียน '" + gradeLevel + "' หรือไม่?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "ใช่, ลบเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'backend/bn_delete_grade_level.php',
                        type: 'POST',
                        data: { grade_id: gradeId },
                        dataType: 'json',
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: response.type,
                                timer: response.type === 'success' ? 1500 : null,
                                showConfirmButton: response.type !== 'success'
                            }).then(() => {
                                if (response.type === 'success') {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                            Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถลบข้อมูลได้", "error");
                        }
                    });
                }
            });
        });

        // แก้ไขชั้นเรียน
        $('.editGradeBtn').on('click', function() {
            var gradeId = $(this).data('id');
            var gradeLevel = $(this).data('level');

            Swal.fire({
                title: "แก้ไขชั้นเรียน",
                input: 'text',
                inputValue: gradeLevel,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'backend/bn_edit_grade_level.php',
                        type: 'POST',
                        data: { grade_id: gradeId, grade_level: result.value },
                        dataType: 'json',
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: response.type,
                                timer: 1500,
                                showConfirmButton: response.type !== 'success'
                            }).then(() => {
                                if (response.type === 'success') {
                                    location.reload();
                                }
                            });
                        },
                        error: function() {
                            Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถแก้ไขข้อมูลได้", "error");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
