<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else { ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Quản Lý Thư Viện | ADMIN</title>
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
                        <h4 class="header-line">TRANG QUẢN LÝ</h4>
                    </div>
                </div>




                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <!-- CONTENT-WRAPPER SECTION END-->
                <?php include('includes/footer.php'); ?>
                <!-- FOOTER SECTION END-->
                <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
                <!-- CORE JQUERY  -->
                <script src="assets/js/jquery-1.10.2.js"></script>
                <!-- BOOTSTRAP SCRIPTS  -->
                <script src="assets/js/bootstrap.js"></script>
                <!-- CUSTOM SCRIPTS  -->
                <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>