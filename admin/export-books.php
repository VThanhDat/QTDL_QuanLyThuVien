<?php
session_start();
include('includes/config.php');

require '../vendor/autoload.php'; // Đảm bảo đường dẫn đúng đến autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT sach.BookName, sach.Image, theloai.CategoryName, tacgia.AuthorName, sach.BookPrice, sach.Quantity, sach.Stock, sach.Method 
        FROM sach 
        JOIN theloai ON theloai.id = sach.CatId 
        JOIN tacgia ON tacgia.id = sach.AuthorId";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề cho các cột
$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Tên Sách');
$sheet->setCellValue('C1', 'Thể Loại');
$sheet->setCellValue('D1', 'Tác Giả');
$sheet->setCellValue('E1', 'Giá');
$sheet->setCellValue('F1', 'Số lượng');
$sheet->setCellValue('G1', 'Kho');
$sheet->setCellValue('H1', 'Hình thức');

// Điền dữ liệu từ database vào các dòng Excel
$row = 2; // Dòng bắt đầu ghi dữ liệu
$cnt = 1;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $cnt);
    $sheet->setCellValue('B' . $row, $result->BookName);
    $sheet->setCellValue('C' . $row, $result->CategoryName);
    $sheet->setCellValue('D' . $row, $result->AuthorName);
    $sheet->setCellValue('E' . $row, $result->BookPrice);
    $sheet->setCellValue('F' . $row, $result->Quantity);
    $sheet->setCellValue('G' . $row, $result->Stock);
    $sheet->setCellValue('H' . $row, $result->Method == 1 ? 'Online' : 'Offline');
    $row++;
    $cnt++;
}

// Xuất file Excel
$filename = 'Danh_sach_sach.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
