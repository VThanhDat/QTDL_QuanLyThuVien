<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {
    $sid = $_SESSION['stdid'];

    // Hàm lấy số sách đã mượn
    function getIssuedBooks($dbh, $sid)
    {
        // Tổng số sách đã mượn
        $sqlIssued = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 2";
        $queryIssued = $dbh->prepare($sqlIssued);
        $queryIssued->bindParam(':sid', $sid, PDO::PARAM_INT);
        $queryIssued->execute();
        $issuedCount = $queryIssued->fetchColumn();

        // Tổng số sách chưa mượn
        $sqlNotIssued = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 1";
        $queryNotIssued = $dbh->prepare($sqlNotIssued);
        $queryNotIssued->bindParam(':sid', $sid, PDO::PARAM_INT);
        $queryNotIssued->execute();
        $notIssuedCount = $queryNotIssued->fetchColumn();

        return $issuedCount + $notIssuedCount;
    }

    // Hàm lấy số sách đã trả
    function getReturnedBooks($dbh, $sid)
    {
        $sql = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 2";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sid', $sid, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn();
    }

    // Hàm lấy tổng số sách (đã mượn, chưa mượn, bị từ chối)
    function getTotalBooks($dbh, $sid)
    {
        // Tổng số sách đã mượn
        $sqlIssued = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 2";
        $queryIssued = $dbh->prepare($sqlIssued);
        $queryIssued->bindParam(':sid', $sid, PDO::PARAM_INT);
        $queryIssued->execute();
        $issuedCount = $queryIssued->fetchColumn();

        // Tổng số sách chưa mượn
        $sqlNotIssued = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 1";
        $queryNotIssued = $dbh->prepare($sqlNotIssued);
        $queryNotIssued->bindParam(':sid', $sid, PDO::PARAM_INT);
        $queryNotIssued->execute();
        $notIssuedCount = $queryNotIssued->fetchColumn();

        // Tổng số sách bị từ chối (giả sử có trạng thái "rejected")
        $sqlRejected = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND BorrowStatus = 0";
        $queryRejected = $dbh->prepare($sqlRejected);
        $queryRejected->bindParam(':sid', $sid, PDO::PARAM_INT);
        $queryRejected->execute();
        $rejectedCount = $queryRejected->fetchColumn();

        // Tổng số sách = số sách đã mượn + số sách chưa mượn + số sách bị từ chối
        return $issuedCount + $notIssuedCount + $rejectedCount;
    }

    // Hàm lấy số sách quá hạn
    function getOverdueBooks($dbh, $sid)
    {
        $sql = "SELECT COUNT(*) FROM ctmuontra WHERE ReaderId = :sid AND ReturnDate < NOW() AND BorrowStatus = 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sid', $sid, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn();
    }

    // Hàm lấy số sách đang chờ
    function getReservedBooks($dbh, $sid) {
        // Gọi function MySQL getReservedBooks và lấy kết quả
        $sql = "SELECT getReservedBooks(:sid) AS reservedBooks";
        $query = $dbh->prepare($sql);
    
        // Gán giá trị cho tham số `sid`
        $query->bindParam(':sid', $sid, PDO::PARAM_INT);
    
        // Thực thi truy vấn và lấy kết quả
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
    
        // Trả về số sách đang chờ
        return $result['reservedBooks'];
    }
    
    // Gọi các hàm và lưu kết quả
    $issuedBooks = getIssuedBooks($dbh, $sid);
    $returnedBooks = getReturnedBooks($dbh, $sid);
    $totalBooks = getTotalBooks($dbh, $sid);
    $overdueBooks = getOverdueBooks($dbh, $sid);
    $reservedBooks = getReservedBooks($dbh, $sid);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Hệ thống quản lý thư viện | Trang Admin</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">
                        <i class="fas fa-home" style="margin-right: 10px;"></i> TRANG CHỦ
                    </h4>
                </div>
            </div>
            <div class="content-wrapper">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-info back-widget-set">
                    <i class="fa fa-bars fa-5x icon-green"></i>
                    <span class="book-title">Số Sách Đã Mượn</span>
                    <h5 class="number-display"><?php echo htmlentities($issuedBooks); ?></h5>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-warning back-widget-set">
                    <i class="fa fa-recycle fa-5x icon-green"></i>
                    <span class="book-title">Số Sách Đã Trả</span>
                    <h5 class="number-display"><?php echo htmlentities($returnedBooks); ?></h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-success back-widget-set">
                    <i class="fa fa-check fa-5x icon-green"></i>
                    <span class="book-title">Tổng Số Sách</span>
                    <h5 class="number-display"><?php echo htmlentities($totalBooks); ?></h5>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-danger back-widget-set">
                    <i class="fa fa-exclamation-triangle fa-5x icon-green"></i>
                    <span class="book-title">Số Sách Quá Hạn</span>
                    <h5 class="number-display"><?php echo htmlentities($overdueBooks); ?></h5>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="alert alert-custom back-widget-set">
                    <i class="fa fa-bookmark fa-5x icon-green"></i>
                    <span class="book-title">Số Sách Đang Chờ Duyệt</span>
                    <h5 class="number-display"><?php echo htmlentities($reservedBooks); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>
    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>