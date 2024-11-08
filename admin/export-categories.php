<?php
session_start();
include('includes/config.php');

require '../vendor/autoload.php'; // Trở về thư mục gốc trước khi yêu cầu autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT * FROM theloai";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề cho các cột
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Thể Loại');
$sheet->setCellValue('C1', 'Trạng Thái');
$sheet->setCellValue('D1', 'Ngày Tạo');
$sheet->setCellValue('E1', 'Ngày Cập Nhật');

// Điền dữ liệu từ database vào các dòng Excel
$row = 2; // Dòng bắt đầu ghi dữ liệu
$cnt = 1;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $cnt);
    $sheet->setCellValue('B' . $row, $result->CategoryName);
    $sheet->setCellValue('C' . $row, $result->Status == 1 ? 'Kích hoạt' : 'Ẩn');
    $sheet->setCellValue('D' . $row, $result->CreationDate);
    $sheet->setCellValue('E' . $row, $result->UpdationDate);
    $row++;
    $cnt++;
}

// Xuất file Excel
$filename = 'Danh_sach_the_loai.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
