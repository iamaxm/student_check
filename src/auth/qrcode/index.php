<?php
require_once __DIR__ . '/vendor/autoload.php'; // โหลด Composer autoloader

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

// สร้าง QR Code
$result = Builder::create()
    ->writer(new PngWriter()) // ตั้งค่ารูปแบบไฟล์
    ->data('Hello, QR Code!') // ข้อมูลที่ต้องการใน QR Code
    ->size(300) // ขนาดของ QR Code
    ->margin(10) // ระยะขอบ
    ->build();

// บันทึก QR Code ลงไฟล์
$filePath = __DIR__ . '/qrcode.png';
$result->saveToFile($filePath);

// แสดงผล QR Code
header('Content-Type: image/png');
echo $result->getString();
?>
