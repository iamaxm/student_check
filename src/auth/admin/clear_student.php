<?php
include '../config/ConnectDB.php';

// Fetch grades for dropdown
$grade_sql = "SELECT * FROM grade_level";
$grade_result = $conn->query($grade_sql);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบข้อมูลนักเรียน</title>
    <style>
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 5px 0;
        }

        #studentList {
            display: block;
            /* ต้องแน่ใจว่าไม่ได้ซ่อน */
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card w-100">
                    <center>
                        <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">ลบข้อมูลนักเรียน</h1>
                    </center>
                    <div class="card-body p-4">
                        <form id="clearStudentForm">
                            <div class="mb-3">
                                <label for="grade_id" class="form-label">เลือกชั้นเรียน</label>
                                <select class="form-select" id="grade_id" name="grade_id" required>
                                    <option value="" disabled selected>-- เลือกชั้นเรียน --</option>
                                    <?php while ($grade_row = $grade_result->fetch_assoc()) : ?>
                                        <option value="<?php echo $grade_row['id']; ?>"><?php echo $grade_row['grade_level']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                                <center><button type="button" class="btn btn-danger" id="clear_button">ลบข้อมูลทั้งหมด</button></center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#clear_button').on('click', function() {
                const gradeId = $('#grade_id').val();
                if (!gradeId) {
                    Swal.fire('ข้อผิดพลาด', 'กรุณาเลือกชั้นเรียนก่อน', 'error');
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล?',
                    text: 'คุณต้องการลบข้อมูลนักเรียนทั้งหมดในชั้นเรียนนี้หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'ยืนยันการลบ'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'backend/bn_clear_students.php',
                            type: 'POST',
                            data: {
                                grade_id: gradeId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('สำเร็จ', `ลบข้อมูลนักเรียนจำนวน ${response.deletedStudents} คนสำเร็จ`, 'success');
                                } else {
                                    Swal.fire('ข้อผิดพลาด', response.message, 'warning');
                                }
                            },
                            error: function() {
                                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถลบข้อมูลนักเรียนได้', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>