<?php
session_start();
include('includes/config.php');

require '../vendor/autoload.php'; // Đảm bảo đường dẫn đến autoload.php là chính xác

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy danh sách người dùng từ cơ sở dữ liệu
$sql = "SELECT FullName, EmailId, MobileNumber, RegDate, Status FROM docgia";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Khởi tạo bảng tính và cài đặt các tiêu đề cột
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Tên Độc Giả');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'SĐT');
$sheet->setCellValue('E1', 'Ngày Đăng Kí');
$sheet->setCellValue('F1', 'Trạng Thái');

// Điền dữ liệu từ database vào các dòng Excel
$row = 2; // Bắt đầu từ dòng 2 để trừ tiêu đề
$cnt = 1;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $cnt);
    $sheet->setCellValue('B' . $row, $result->FullName);
    $sheet->setCellValue('C' . $row, $result->EmailId);
    $sheet->setCellValue('D' . $row, $result->MobileNumber);
    $sheet->setCellValue('E' . $row, $result->RegDate);
    $sheet->setCellValue('F' . $row, $result->Status == 1 ? 'Đang Hoạt Động' : 'Đã Bị Chặn');
    $row++;
    $cnt++;
}

// Cài đặt header để tải file Excel
$filename = 'Danh_sach_nguoi_dung.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
