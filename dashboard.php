<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} 
else {
        $sid = $_SESSION['stdid'];

//     $stmt1 = $dbh->prepare("CALL LaySachDaMuon(:student_id)");
//     $stmt1->bindParam(':student_id', $sid, PDO::PARAM_STR);
//     $stmt1->execute();
//     $issuedBooks = $stmt1->fetchColumn();
//     $stmt1->closeCursor();

//     $stmt2 = $dbh->prepare("CALL LaySachChuaTra(:student_id)");
//     $stmt2->bindParam(':student_id', $sid, PDO::PARAM_STR);
//     $stmt2->execute();
//     $returnedBooks = $stmt2->fetchColumn();
//     $stmt2->closeCursor();
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
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">TRANG CHỦ</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="alert alert-info back-widget-set text-center">
                        <i class="fa fa-bars fa-5x"></i>
                        <!-- <h3><?php echo htmlentities($issuedBooks); ?> </h3> -->
                        Số Sách Đã Mượn
                    </div>
                </div>

                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="alert alert-warning back-widget-set text-center">
                        <i class="fa fa-recycle fa-5x"></i>
                        <!-- <h3><?php echo htmlentities($returnedBooks); ?></h3> -->
                        Số Sách Chưa Trả
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