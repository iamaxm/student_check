<?php
@session_start();
include '../config/ConnectDB.php';

// Query ข้อมูลแอดมินทั้งหมดจากฐานข้อมูล
$admin_sql = "SELECT * FROM `admin`";
$admin_result = $conn->query($admin_sql);

// กำหนดตัวแปรสำหรับชื่อผู้ใช้ปัจจุบัน
$current_username = $_SESSION['username'];
?>


<div class="container-fluid">
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <center>
                    <h1 class="card-title fw-semibold mb-4" style="margin-top: 2rem; font-size:25px">จัดการข้อมูลแอดมิน</h1>
                </center>
                <div class="card-body p-4">
                    <button type="button" class="btn btn-outline-success m-1" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        เพิ่มแอดมิน
                    </button>

                    <!-- Table แสดงข้อมูลแอดมิน -->
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>
                                        <center>ชื่อผู้ใช้</center>
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
                                <?php while ($admin_row = mysqli_fetch_assoc($admin_result)) : ?>
                                    <tr>
                                        <td>
                                            <center>
                                                <?php
                                                echo $admin_row['username'];
                                                if ($admin_row['username'] === $current_username) { ?>
                                                    &nbsp;<span class="badge bg-success rounded-3 fw-semibold">(คุณ)</span>
                                                <?php  }
                                                ?>
                                            </center>
                                        </td>
                                        <td><center><?php echo date("d/m/Y H:i:s", strtotime($admin_row['created_at'])); ?></center></td>
                                        <td>
                                            <center>
                                                <button type="button" class="btn btn-outline-warning m-1" data-bs-toggle="modal" data-bs-target="#editAdminModal<?php echo $admin_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                    </svg>&nbsp;แก้ไข
                                                </button>
                                            </center>
                                        </td>
                                        <td>
                                            <?php if ($admin_row['username'] !== $current_username) { ?>
                                            <center>
                                                <button type="button" class="btn btn-outline-danger m-1" data-bs-toggle="modal" data-bs-target="#deleteAdminModal<?php echo $admin_row['id']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                                                    </svg>&nbsp;ลบ
                                                </button>
                                            </center>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <!-- Modal สำหรับแก้ไขแอดมิน -->
                                    <div class="modal fade" id="editAdminModal<?php echo $admin_row['id']; ?>" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_edit_admin.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">แก้ไขข้อมูลแอดมิน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="admin_id" value="<?php echo $admin_row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="editUsername" class="form-label">ชื่อผู้ใช้</label>
                                                            <input type="text" class="form-control" id="editUsername" name="username" value="<?php echo $admin_row['username']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="editPassword" class="form-label">รหัสผ่าน (ไม่จำเป็นต้องเปลี่ยน)</label>
                                                            <input type="password" class="form-control" id="editPassword" name="password">
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

                                    <!-- Modal ยืนยันการลบแอดมิน -->
                                    <div class="modal fade" id="deleteAdminModal<?php echo $admin_row['id']; ?>" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="backend/bn_delete_admin.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">ยืนยันการลบแอดมิน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>คุณแน่ใจหรือไม่ว่าต้องการลบแอดมิน "<strong><?php echo $admin_row['username']; ?></strong>"?</p>
                                                        <input type="hidden" name="admin_id" value="<?php echo $admin_row['id']; ?>">
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

<!-- Modal สำหรับเพิ่มแอดมินใหม่ -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="backend/bn_add_admin.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มแอดมินใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">ยืนยันรหัสผ่าน</label>
                        <input type="password" class="form-control" id="password" name="cpassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-success">สร้าง</button>
                </div>
            </form>
        </div>
    </div>
</div>