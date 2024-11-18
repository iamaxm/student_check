<?php
include '../config/ConnectDB.php';

$today = date("Y-m-d");
$status_filter = $is_check_in_time ? "'pending', 'in'" : "'out'";
$time_inout_sql = "
    SELECT time_inout.status, time_inout.created_at, 
           student.prefix, student.name AS student_name, student.surname
    FROM time_inout
    LEFT JOIN student ON time_inout.id_student = student.id
    WHERE DATE(time_inout.created_at) = '$today' 
      AND time_inout.status IN ($status_filter)
    ORDER BY time_inout.created_at DESC";
$time_inout_result = $conn->query($time_inout_sql);

echo '<table class="table text-nowrap mb-0 align-middle">';
echo '<thead class="text-dark fs-4">
        <tr>
            <th><center>ชื่อ นามสกุล</center></th>
            <th><center>สถานะ</center></th>
            <th><center>วันที่และเวลา</center></th>
        </tr>
      </thead>';
echo '<tbody>';
while ($time_inout_row = $time_inout_result->fetch_assoc()) {
    echo '<tr>
            <td><center>' . $time_inout_row['prefix'] . ' ' . $time_inout_row['student_name'] . ' ' . $time_inout_row['surname'] . '</center></td>
            <td><center>' . $time_inout_row['status'] . '</center></td>
            <td><center>' . date("d/m/Y H:i:s", strtotime($time_inout_row['created_at'])) . '</center></td>
          </tr>';
}
echo '</tbody>';
echo '</table>';
?>
