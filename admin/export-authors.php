<?php
session_start();
include('includes/config.php');

require '../vendor/autoload.php'; // Đảm bảo đường dẫn chính xác đến autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT * FROM tacgia"; // Truy vấn dữ liệu từ bảng `tacgia`
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề cho các cột
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Tác Giả');
$sheet->setCellValue('C1', 'Ngày Tạo');
$sheet->setCellValue('D1', 'Ngày Cập Nhật');

// Điền dữ liệu từ database vào các dòng Excel
$row = 2; // Dòng bắt đầu ghi dữ liệu
$cnt = 1;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $cnt);
    $sheet->setCellValue('B' . $row, $result->AuthorName);
    $sheet->setCellValue('C' . $row, $result->CreationDate);
    $sheet->setCellValue('D' . $row, $result->UpdationDate);
    $row++;
    $cnt++;
}

// Xuất file Excel
$filename = 'Danh_sach_tac_gia.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
