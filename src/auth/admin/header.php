<?php
@session_start();
include '../config/ConnectDB.php';
$username = $_SESSION['username'];

?>

<!--  Header Start -->
<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                    <!-- <i class="ti ti-bell-ringing"></i> -->
                    <!-- <div class="notification bg-primary rounded-circle"></div> -->
                </a>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                <li class="nav-item dropdown">
                    <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <!-- <img src="../../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle"> -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-fill-lock" viewBox="0 0 16 16">
                            <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5v-1a2 2 0 0 1 .01-.2 4.49 4.49 0 0 1 1.534-3.693Q8.844 9.002 8 9c-5 0-6 3-6 4m7 0a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1" />
                        </svg>
                        &nbsp;<?php echo $username; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <!-- <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">My Profile</p>
                            </a>
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-mail fs-6"></i>
                                <p class="mb-0 fs-3">My Account</p>
                            </a> -->
                            <!-- <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#clearStudentModal">

                                <h4 class="mb-0 fs-3 mx-3 mt-2 d-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stars" viewBox="0 0 16 16">
                                        <path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.73 1.73 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.73 1.73 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.73 1.73 0 0 0 3.407 2.31zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z" />
                                    </svg>
                                    เคลียร์รายชื่อนักเรียน
                                </h4>
                            </a> -->
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#timeSettingsModal">

                                <h4 class="mb-0 fs-3 mx-3 mt-2 d-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                                    </svg>
                                    ตั้งเวลาการเข้าออกเรียน
                                </h4>
                            </a>
                            <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">ออกจากระบบ</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--  Header End -->

<!-- Modal -->
<div style="margin-top: 7rem;" class="modal fade" id="timeSettingsModal" tabindex="-1" aria-labelledby="timeSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timeSettingsModalLabel">ตั้งเวลาการเข้าออกเรียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="timeSettingsForm">
                    <div class="mb-3">
                        <label for="check_in_start" class="form-label">เวลาเริ่มเข้าเรียน</label>
                        <input type="time" class="form-control" id="check_in_start" name="check_in_start">
                    </div>
                    <div class="mb-3">
                        <label for="check_in_end" class="form-label">เวลาสิ้นสุดเข้าเรียน</label>
                        <input type="time" class="form-control" id="check_in_end" name="check_in_end">
                    </div>
                    <div class="mb-3">
                        <label for="check_out_start" class="form-label">เวลาเริ่มออกเรียน</label>
                        <input type="time" class="form-control" id="check_out_start" name="check_out_start">
                    </div>
                    <div class="mb-3">
                        <label for="check_out_end" class="form-label">เวลาสิ้นสุดออกเรียน</label>
                        <input type="time" class="form-control" id="check_out_end" name="check_out_end">
                    </div>
                    <center><button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button></center>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal เคลียร์นักเรียน -->
<!-- Modal -->
<div class="modal fade" id="clearStudentModal" tabindex="-1" aria-labelledby="clearStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearStudentModalLabel">เคลียร์รายชื่อนักเรียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clearStudentForm">
                    <div class="mb-3">
                        <label for="grade" class="form-label">เลือกชั้น</label>
                        <select class="form-select" id="grade" name="grade" required>
                            <option value="" disabled selected>-- เลือกชั้น --</option>
                            <?php foreach ($grades as $grade): ?>
                                <option value="<?= $grade['id'] ?>"><?= $grade['grade_level'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="room" class="form-label">เลือกห้อง</label>
                        <select class="form-select" id="room" name="room" required>
                            <option value="" disabled selected>-- เลือกห้อง --</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['id'] ?>" data-grade="<?= $room['grade_id'] ?>"><?= $room['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="studentList"></div>
                    <button type="button" class="btn btn-danger" id="clearButton">ลบข้อมูลทั้งหมด</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ปุ่มเปิด Modal -->
<!-- <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#clearStudentModal">
    <h4 class="mb-0 fs-3 mx-3 mt-2 d-block">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stars" viewBox="0 0 16 16">
            <path d="..."></path>
        </svg>
        เคลียร์รายชื่อนักเรียน
    </h4>
</a> -->


<!-- ลิงก์ jQuery และ SweetAlert2 -->
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('timeSettingsModal').addEventListener('show.bs.modal', function() {
        fetch('backend/get_time_settings.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('การโหลดข้อมูลล้มเหลว');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('check_in_start').value = data.check_in_start || '';
                document.getElementById('check_in_end').value = data.check_in_end || '';
                document.getElementById('check_out_start').value = data.check_out_start || '';
                document.getElementById('check_out_end').value = data.check_out_end || '';
            })
            .catch(error => {
                console.error('เกิดข้อผิดพลาดในการโหลดข้อมูล:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถโหลดข้อมูลการตั้งเวลาได้',
                });
            });
    });


    document.getElementById('timeSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณต้องการบันทึกการเปลี่ยนแปลงหรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, บันทึกเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);

                fetch('backend/save_time_settings.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('การบันทึกข้อมูลล้มเหลว');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ',
                                text: 'การตั้งเวลาได้ถูกบันทึกเรียบร้อยแล้ว',
                            }).then(() => {
                                bootstrap.Modal.getInstance(document.getElementById('timeSettingsModal')).hide();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถบันทึกข้อมูลได้: ' + (data.error || 'ข้อผิดพลาดไม่ทราบสาเหตุ'),
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'เกิดข้อผิดพลาดขณะบันทึกข้อมูล',
                        });
                        console.error('เกิดข้อผิดพลาด:', error);
                    });
            }
        });
    });



    
</script>