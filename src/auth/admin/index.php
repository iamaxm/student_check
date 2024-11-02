<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่และตรวจสอบว่าชื่อผู้ใช้ไม่ใช่ค่าว่าง
if (empty($_SESSION['username'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบ หรือชื่อผู้ใช้เป็นค่าว่าง ให้เปลี่ยนเส้นทางไปยังหน้าเข้าสู่ระบบ
    header("Location: ../index.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modernize Free</title>
    <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../../assets/css/styles.min.css" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=diversity_3" />
    
</head>
<style>
    * {
        font-family: "Bai Jamjuree", sans-serif;
    }
</style>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <?php include 'slidebar.php'; ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?php

            $swcase = @$_GET['id'];
            switch ($swcase) {
                case "dashboard":
                    include'index.php';
                    break;
                case "admin":
                    include 'header.php';
                    include 'admin.php';
                    break;
                case "grade_level":
                    include 'header.php';
                    include 'grade_level.php';
                    break;
                case "rooms":
                    include 'header.php';
                    include 'rooms.php';
                    break;
                case "teacher":
                    include 'header.php';
                    include 'teacher.php';
                    break;
                case "student":
                    include 'header.php';
                    include 'student.php';
                    break;
                case "check_name_student":
                    include 'header.php';
                    include 'check_name_student.php';
                    break;
                case "history_check_inout":
                    include 'header.php';
                    include 'history_check_inout.php';
                    break;
                case "manual_check_inout":
                    include 'header.php';
                    include 'manual_check_inout.php';
                    break;
                default:
                    include 'header.php';
                    include 'dashboard.php';
            }


            ?>
            <!--  Header End -->
            
        </div>
    </div>
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
    <script src="../../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
</body>

</html>