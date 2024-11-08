<?php
session_start();
include('includes/config.php');

require '../vendor/autoload.php'; // Đảm bảo đường dẫn đến autoload.php là chính xác

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy dữ liệu mượn trả offline từ cơ sở dữ liệu
$sql = "SELECT docgia.FullName, sach.BookName, sach.ISBNNumber, ctmuontra.QuantityBorrow, ctmuontra.IssuesDate, ctmuontra.ReturnDate, ctmuontra.BorrowStatus
        FROM ctmuontra 
        JOIN docgia ON docgia.id = ctmuontra.ReaderId 
        JOIN sach ON sach.id = ctmuontra.BookId 
        WHERE ctmuontra.Method = 0
        ORDER BY ctmuontra.id DESC";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Khởi tạo bảng tính và cài đặt các tiêu đề cột
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', '#');
$sheet->setCellValue('B1', 'Tên Độc Giả');
$sheet->setCellValue('C1', 'Tên Sách');
$sheet->setCellValue('D1', 'ISBN');
$sheet->setCellValue('E1', 'Số Lượng');
$sheet->setCellValue('F1', 'Ngày Mượn');
$sheet->setCellValue('G1', 'Ngày Trả');
$sheet->setCellValue('H1', 'Trạng Thái');

// Điền dữ liệu từ database vào các dòng Excel
$row = 2; // Bắt đầu từ dòng 2 để trừ tiêu đề
$cnt = 1;
foreach ($results as $result) {
    $sheet->setCellValue('A' . $row, $cnt);
    $sheet->setCellValue('B' . $row, $result->FullName);
    $sheet->setCellValue('C' . $row, $result->BookName);
    $sheet->setCellValue('D' . $row, $result->ISBNNumber);
    $sheet->setCellValue('E' . $row, $result->QuantityBorrow);
    $sheet->setCellValue('F' . $row, $result->IssuesDate);
    $sheet->setCellValue('G' . $row, $result->ReturnDate ? $result->ReturnDate : "Chưa trả");

    // Thiết lập trạng thái mượn trả
    $status = "";
    if (is_null($result->BorrowStatus)) {
        $status = "Từ chối";  // Nếu BorrowStatus là NULL thì gán "Từ chối"
    } else {
        switch ($result->BorrowStatus) {
            case 0:
                $status = "Chưa duyệt";
                break;
            case 1:
                $status = "Đã duyệt";
                break;
            case 2:
                $status = "Đã trả";
                break;
            default:
                $status = "Không xác định";
                break;
        }
    }
    $sheet->setCellValue('H' . $row, $status);

    $row++;
    $cnt++;
}

// Cài đặt header để tải file Excel
$filename = 'Danh_sach_muon_tra_offline.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
