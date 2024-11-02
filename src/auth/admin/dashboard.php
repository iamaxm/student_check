<!-- ลิงก์ jQuery และ SweetAlert2 -->
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .large-number {
        font-size: 3rem;
        /* ปรับขนาดฟอนต์ตามต้องการ */
        font-weight: bold;
        text-align: center;
    }
</style>

<div class="container-fluid">
    <!--  Row 1 -->
    <div class="row">
        <div class="col-lg-12 d-flex align-items-strech">
            <div class="w-100">
                <div class="card-body">
                    <!-- Row สำหรับจำนวนแอดมิน ครู และนักเรียน -->
                    <div class="row d-flex justify-content-center gap-3 mt-4">
                        <!-- การ์ดจำนวนแอดมินทั้งหมด -->
                        <div class="col-lg-3">
                            <div class="card overflow-hidden" style="border: 2px solid #FFA500;">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold text-center">จำนวนแอดมินทั้งหมด</h5>
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <h1 class="fw-semibold mb-3 large-number" id="totalAdminCount" style="color:#FFA500;">0</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- การ์ดจำนวนคุณครูทั้งหมด -->
                        <div class="col-lg-3">
                            <div class="card overflow-hidden" style="border: 2px solid #007BFF;">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold text-center">จำนวนคุณครูทั้งหมด</h5>
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <h1 class="fw-semibold mb-3 large-number" id="totalTeacherCount" style="color:#007BFF;">0</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- การ์ดจำนวนนักเรียนทั้งหมด -->
                        <div class="col-lg-3">
                            <div class="card overflow-hidden" style="border: 2px solid #28A745;">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold text-center">จำนวนนักเรียนทั้งหมด</h5>
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <h1 class="fw-semibold mb-3 large-number" id="totalStudentCount" style="color:#28A745;">0</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <!--  Row 1 -->
        <div class="row">
            <div class="col-lg-12 d-flex align-items-strech">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                            <div class="mb-3 mb-sm-0">
                                <h1>สรุปการเข้าเรียน</h1>
                            </div>
                        </div>
                        <form id="filterForm" class="my-4">
                            <div class="row">
                                <!-- วันที่ -->
                                <div class="col-md-2">
                                    <label for="startDate" class="form-label">วันที่เริ่มต้น</label>
                                    <input type="date" id="startDate" name="startDate" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="endDate" class="form-label">วันที่สิ้นสุด</label>
                                    <input type="date" id="endDate" name="endDate" class="form-control">
                                </div>
                                <!-- ชั้นเรียน -->
                                <div class="col-md-2">
                                    <label for="grade" class="form-label">ชั้นเรียน</label>
                                    <select id="grade" name="grade" class="form-select">
                                        <option value="">เลือกชั้นเรียน</option>
                                    </select>
                                </div>
                                <!-- ห้องเรียน -->
                                <div class="col-md-2">
                                    <label for="room" class="form-label">ห้องเรียน</label>
                                    <select id="room" name="room" class="form-select">
                                        <option value="">เลือกห้องเรียน</option>
                                    </select>
                                </div>
                                <!-- นักเรียน -->
                                <div class="col-md-4">
                                    <label for="student" class="form-label">นักเรียน</label>
                                    <select id="student" name="student" class="form-select">
                                        <option value="">เลือกนักเรียน</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">ค้นหา</button>
                            </div> -->
                        </form>
                        <div id="attendanceSummary" class="mt-4">
                            <div class="row d-flex justify-content-center gap-3">
                                <!-- การ์ดจำนวนการเข้าเรียนทั้งหมด -->
                                <div class="col-lg-4">
                                    <div class="card overflow-hidden" style="border: 2px solid #13DEB9;">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                            <h5 class="card-title mb-3 fw-semibold text-center">จำนวนการเข้าเรียนทั้งหมด</h5>
                                            <h1 class="fw-semibold mb-3 large-number" id="attendanceInCount" style="color:#13DEB9;"></h1>
                                        </div>
                                    </div>
                                </div>

                                <!-- การ์ดจำนวนการออกเรียนทั้งหมด -->
                                <div class="col-lg-4">
                                    <div class="card overflow-hidden" style="border: 2px solid #FFAE1F;">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                            <h5 class="card-title mb-3 fw-semibold text-center">จำนวนการออกเรียนทั้งหมด</h5>
                                            <h1 class="fw-semibold mb-3 large-number" id="attendanceOutCount" style="color:#FFAE1F ;"></h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- <div id="chart"></div> -->
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');

            // กำหนดวันที่ปัจจุบัน
            const today = new Date();
            today.setHours(today.getHours() + 7); // ปรับเวลาเป็น GMT+7 (Time Zone ไทย)
            const formattedToday = today.toISOString().split('T')[0];

            // กำหนดวันที่เริ่มต้นให้เป็นวันที่ 1 ของเดือนปัจจุบัน
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            firstDayOfMonth.setHours(firstDayOfMonth.getHours() + 7); // ปรับเวลาเป็น GMT+7
            const formattedFirstDayOfMonth = firstDayOfMonth.toISOString().split('T')[0];

            // ตั้งค่าให้ช่องวันที่เริ่มต้นและวันที่สิ้นสุด
            startDateInput.value = formattedFirstDayOfMonth;
            endDateInput.value = formattedToday;

            // กำหนดวันที่สูงสุดเป็นวันที่ปัจจุบัน
            startDateInput.max = formattedToday;
            endDateInput.max = formattedToday;

            // เรียกใช้ฟังก์ชันโหลดข้อมูลอัตโนมัติ
            fetchAttendanceData();
            fetchTotalCounts();

            // ทำให้ fetchAttendanceData ทำงานอัตโนมัติเมื่อมีการเปลี่ยนแปลงวันที่เริ่มต้นและวันที่สิ้นสุด
            startDateInput.addEventListener('change', fetchAttendanceData);
            endDateInput.addEventListener('change', fetchAttendanceData);
        });

        // เรียกฟังก์ชันเมื่อหน้าโหลดเสร็จ
        window.onload = function() {
            fetchAttendanceData();
            fetchTotalCounts();
        };

        // เรียกฟังก์ชันใหม่เมื่อหน้าจอถูกปรับขนาด (เช่น การเปลี่ยนจากแนวนอนเป็นแนวตั้ง)
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                fetchAttendanceData();
                fetchTotalCounts();
            }
        });

        // เรียกฟังก์ชันเมื่อมีการเชื่อมต่ออินเทอร์เน็ตกลับมา
        window.addEventListener('online', function() {
            fetchAttendanceData();
            fetchTotalCounts();
        });


        $(document).ready(function() {
            // Fetch initial data for dropdowns
            fetchDropdownData();

            // Fetch total counts data on page load
            fetchTotalCounts();

            // Update Room and Student options based on Grade and Room selections
            $('#grade').change(function() {
                const gradeId = $(this).val();
                fetchRoomsByGrade(gradeId);
                fetchStudentsByGradeAndRoom(gradeId, ''); // ส่งค่า roomId ว่างเพื่อให้แสดงนักเรียนทั้งหมดในชั้นที่เลือก
                fetchAttendanceData(); // Refresh attendance data with new grade selection
            });

            $('#room').change(function() {
                const roomId = $(this).val();
                const gradeId = $('#grade').val();
                fetchStudentsByGradeAndRoom(gradeId, roomId);
                fetchAttendanceData(); // Refresh attendance data with new room selection
            });

            $('#student').change(function() {
                fetchAttendanceData(); // Refresh attendance data with new student selection
            });
        });

        function fetchDropdownData() {
            $.ajax({
                url: 'backend/fetch_dropdown_options.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#grade').empty().append('<option value="">ทั้งหมด</option>');
                    $.each(data.grades, function(index, grade) {
                        $('#grade').append('<option value="' + grade.id + '">' + grade.grade_level + '</option>');
                    });

                    // แสดงห้องและนักเรียนทั้งหมดในตอนแรก
                    $('#room').empty().append('<option value="">ทั้งหมด</option>');
                    $.each(data.rooms, function(index, room) {
                        $('#room').append('<option value="' + room.id + '">' + room.name + '</option>');
                    });

                    $('#student').empty().append('<option value="">ทั้งหมด</option>');
                    $.each(data.students, function(index, student) {
                        $('#student').append('<option value="' + student.id + '">' + student.prefix + ' ' + student.name + ' ' + student.surname + '</option>');
                    });
                },
                error: function() {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถดึงข้อมูลได้", "error");
                }
            });
        }

        function fetchRoomsByGrade(gradeId) {
    $.ajax({
        url: 'backend/fetch_rooms.php',
        type: 'POST',
        data: {
            grade_id: gradeId
        },
        dataType: 'json',
        success: function(data) {
            $('#room').empty().append('<option value="">ทั้งหมด</option>');
            $.each(data.rooms, function(index, room) {
                $('#room').append('<option value="' + room.id + '">' + room.name + '</option>');
            });
        },
        error: function() {
            Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถดึงข้อมูลห้องเรียนได้", "error");
        }
    });
}

        function fetchStudentsByGradeAndRoom(gradeId, roomId) {
            $.ajax({
                url: 'backend/fetch_students.php',
                type: 'POST',
                data: {
                    grade_id: gradeId,
                    room_id: roomId
                },
                dataType: 'json',
                success: function(data) {
                    $('#student').empty().append('<option value="">ทั้งหมด</option>');
                    $.each(data.students, function(index, student) {
                        $('#student').append('<option value="' + student.id + '">' + student.prefix + ' ' + student.name + ' ' + student.surname + '</option>');
                    });
                }
            });
        }


        function fetchAttendanceData() {
            const formData = $('#filterForm').serialize();

            $.ajax({
                url: 'backend/fetch_attendance_data.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $('#attendanceInCount').text(data.totalIn);
                    $('#attendanceOutCount').text(data.totalOut);
                },
                error: function() {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถดึงข้อมูลได้", "error");
                }
            });
        }

        // ฟังก์ชันดึงข้อมูลจำนวนนักเรียน ครู และแอดมิน
        function fetchTotalCounts() {
            $.ajax({
                url: 'backend/fetch_counts.php', // ตรวจสอบว่าไฟล์นี้ใช้ชื่อถูกต้องตามที่คุณเก็บไว้
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#totalAdminCount').text(data.totalAdmins);
                    $('#totalTeacherCount').text(data.totalTeachers);
                    $('#totalStudentCount').text(data.totalStudents);
                },
                error: function() {
                    Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถดึงข้อมูลจำนวนนักเรียน ครู และแอดมินได้", "error");
                }
            });
        }
    </script>