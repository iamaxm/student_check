<?php
@session_start();
include '../../config/ConnectDB.php';

// ตั้ง timezone เป็น Asia/Bangkok
date_default_timezone_set("Asia/Bangkok");

if (isset($_POST['student_id']) && isset($_POST['hour'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $hour = (float) $_POST['hour'];

    // ดึงช่วงเวลาเช็คเข้าและออกจากฐานข้อมูล
    $time_settings_sql = "SELECT * FROM check_time_settings LIMIT 1";
    $time_settings_result = $conn->query($time_settings_sql);
    $time_settings = $time_settings_result->fetch_assoc();

    if (!$time_settings) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "ไม่พบข้อมูลการตั้งค่าเวลา"]);
        exit;
    }

    // กำหนดช่วงเวลาเช็คเข้าและออก
    $check_in_start = $time_settings['check_in_start'];
    $check_in_end = $time_settings['check_in_end'];
    $check_out_start = $time_settings['check_out_start'];
    $check_out_end = $time_settings['check_out_end'];

    $current_time = date("H:i:s");
    $is_check_in_time = $current_time >= $check_in_start && $current_time <= $check_out_start;
    $is_check_out_time = $current_time >= $check_out_start && $current_time <= $check_out_end;

    // ตรวจสอบว่านักเรียนมีอยู่ในระบบหรือไม่
    $student_check_sql = "SELECT id FROM student WHERE id = '$student_id'";
    $student_check_result = $conn->query($student_check_sql);

    if ($student_check_result->num_rows == 0) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "ไม่พบรหัสนักเรียนในระบบ"]);
        exit;
    }

    // ตรวจสอบสถานะล่าสุดของนักเรียนในวันนี้
    $today = date("Y-m-d");
    $status_check_sql = "
        SELECT * FROM check_in 
        WHERE id_student = '$student_id' 
          AND DATE(created_at) = '$today'
        LIMIT 1";
    $status_result = $conn->query($status_check_sql);
    $existing_entry = $status_result->fetch_assoc();

    // ตรวจสอบว่าเช็คออกได้หรือไม่
    if ($is_check_out_time && (!empty($existing_entry) && $existing_entry['in_at'] === null)) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "คุณไม่ได้เช็คเข้าเรียน ไม่สามารถเช็คออกได้"]);
        exit;
    }

    // ตรวจสอบว่าเช็คเข้าแล้วหรือยัง
    if ($is_check_in_time && !empty($existing_entry) && $existing_entry['in_at'] !== null) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "คุณได้เช็คชื่อเข้าเรียนแล้ว"]);
        exit;
    }

    // ตรวจสอบว่าเช็คออกแล้วหรือยัง
    if ($is_check_out_time && !empty($existing_entry) && $existing_entry['out_at'] !== null) {
        header('Content-Type: application/json');
        echo json_encode(['message' => "คุณได้เช็คชื่อออกเรียนแล้ว"]);
        exit;
    }

    if (!empty($existing_entry)) {
        // อัปเดตสถานะ (เข้าเรียนหรือออกเรียน)
        $update_field = '';
        if ($is_check_in_time) {
            $update_field = "in_at = NOW()";
        } elseif ($is_check_out_time) {
            $update_field = "out_at = NOW()";
        }

        if ($update_field) {
            $update_sql = "
                UPDATE check_in
                SET $update_field
                WHERE id = {$existing_entry['id']}
            ";

            if ($conn->query($update_sql) === TRUE) {
                $message = $is_check_in_time ? "เช็คชื่อเข้าเรียนสำเร็จ!" : "เช็คชื่อออกเรียนสำเร็จ!";
                header('Content-Type: application/json');
                echo json_encode(['message' => $message]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['message' => "เกิดข้อผิดพลาด: " . $conn->error]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['message' => "ไม่สามารถอัปเดตข้อมูลได้"]);
        }
    } else {
        // สร้างรายการใหม่สำหรับเช็คชื่อ
        if ($is_check_in_time) {
            $insert_sql = "
                INSERT INTO check_in (id_student, in_at, created_at) 
                VALUES ('$student_id', NOW(), NOW())
            ";
        } elseif ($is_check_out_time) {
            header('Content-Type: application/json');
            echo json_encode(['message' => "คุณไม่ได้เช็คเข้าเรียน ไม่สามารถเช็คออกได้"]);
            exit;
        }

        if ($conn->query($insert_sql) === TRUE) {
            $message = $is_check_in_time ? "เช็คชื่อเข้าเรียนสำเร็จ!" : "เช็คชื่อออกเรียนสำเร็จ!";
            header('Content-Type: application/json');
            echo json_encode(['message' => $message]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['message' => "เกิดข้อผิดพลาด: " . $conn->error]);
        }
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['message' => "ข้อมูลที่ส่งมาไม่ครบถ้วน"]);
}

$conn->close();
