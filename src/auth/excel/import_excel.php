<?php
require 'vendor/autoload.php'; // โหลด PHPSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    @session_start();
    include '../config/ConnectDB.php';

    date_default_timezone_set('Asia/Bangkok');

    $id_grade = isset($_POST['id_grade']) ? mysqli_real_escape_string($conn, $_POST['id_grade']) : '';
    $id_room = isset($_POST['id_room']) ? mysqli_real_escape_string($conn, $_POST['id_room']) : '';
    $id_teacher = isset($_POST['id_teacher']) ? mysqli_real_escape_string($conn, $_POST['id_teacher']) : '';
    $id_admin = isset($_POST['id_admin']) ? mysqli_real_escape_string($conn, $_POST['id_admin']) : '';

    if (empty($id_grade) || empty($id_room) || empty($id_teacher) || empty($id_admin)) {
        echo json_encode(['type' => 'warning', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน!']);
        exit;
    }

    if (isset($_FILES['excelFile']['tmp_name']) && !empty($_FILES['excelFile']['tmp_name'])) {
        $file = $_FILES['excelFile']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) <= 1) {
                echo json_encode(['type' => 'error', 'message' => 'ไฟล์ไม่มีข้อมูล']);
                exit;
            }

            $duplicates = []; // เก็บข้อมูลซ้ำ
            foreach ($rows as $key => $row) {
                if ($key === 0) continue;

                $prefix = isset($row[0]) ? trim(mysqli_real_escape_string($conn, $row[0])) : null;
                $name = isset($row[1]) ? trim(mysqli_real_escape_string($conn, $row[1])) : null;
                $surname = isset($row[2]) ? trim(mysqli_real_escape_string($conn, $row[2])) : null;

                if (!$prefix || !$name || !$surname) {
                    echo json_encode(['type' => 'error', 'message' => "ข้อมูลในแถวที่ " . ($key + 1) . " ไม่สมบูรณ์"]);
                    exit;
                }

                // ตรวจสอบข้อมูลซ้ำ
                $stmt_check = $conn->prepare("SELECT id FROM student WHERE name = ? AND surname = ?");
                $stmt_check->bind_param('ss', $name, $surname);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $duplicates[] = "แถวที่ " . ($key + 1) . " ชื่อนักเรียน '$name $surname'";
                }
                $stmt_check->close();
            }

            if (!empty($duplicates)) {
                echo json_encode([
                    'type' => 'error',
                    'message' => 'พบข้อมูลซ้ำ: ' . implode(', ', $duplicates)
                ]);
                exit;
            }

            // ถ้าไม่มีข้อมูลซ้ำ เพิ่มข้อมูลใหม่
            foreach ($rows as $key => $row) {
                if ($key === 0) continue;

                $prefix = trim(mysqli_real_escape_string($conn, $row[0]));
                $name = trim(mysqli_real_escape_string($conn, $row[1]));
                $surname = trim(mysqli_real_escape_string($conn, $row[2]));

                $created_at = date('Y-m-d H:i:s');
                $stmt_insert = $conn->prepare("INSERT INTO student (prefix, name, surname, id_grade, id_room, id_teacher, id_admin, created_at) 
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_insert->bind_param('sssiiiis', $prefix, $name, $surname, $id_grade, $id_room, $id_teacher, $id_admin, $created_at);

                if (!$stmt_insert->execute()) {
                    echo json_encode(['type' => 'error', 'message' => 'ไม่สามารถเพิ่มข้อมูลได้: ' . $stmt_insert->error]);
                    $stmt_insert->close();
                    exit;
                }
                $stmt_insert->close();
            }

            echo json_encode(['type' => 'success', 'message' => 'นำเข้าข้อมูลเรียบร้อย']);
        } catch (Exception $e) {
            echo json_encode(['type' => 'error', 'message' => 'เกิดข้อผิดพลาดในการประมวลผลไฟล์: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['type' => 'error', 'message' => 'ไม่พบไฟล์']);
    }
}
