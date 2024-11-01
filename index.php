<?php
session_start();
error_reporting(0);
include('includes/config.php');
if ($_SESSION['login'] != '') {
    $_SESSION['login'] = '';
}
// Login cho độc giả
if (isset($_POST['login'])) {
    $email = $_POST['emailid'];
    $password = md5($_POST['password']);
    $sql = "SELECT id,EmailId,Password,Status FROM docgia WHERE EmailId=:email and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['stdid'] = $result->id;
            if ($result->Status == 1) {
                $_SESSION['login'] = $_POST['emailid'];
                echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
            } else {
                echo "<script>alert('Tài khoản của bạn đã bị khóa, hãy liên hệ admin để biết thêm');</script>";
            }
        }
    } else {
        echo "<script>alert('Sai tài khoản hoặc mật khẩu.');</script>";
    }
}


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Hệ thống quản lý thư viện | Đăng Nhập </title>
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
                    <h4 class="header-line">ĐĂNG NHẬP CLIENT</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Đăng Nhập
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">

                                <div class="form-group">
                                    <label>Nhập Email </label>
                                    <input class="form-control" type="text" name="emailid" required autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label>Nhập Mật Khẩu</label>
                                    <input class="form-control" type="password" name="password" required autocomplete="off" />
                                </div>
                                <button type="submit" name="login" class="btn btn-info">ĐĂNG NHẬP </button> | <a href="signup.php">Chưa có tài khoản? Đăng ký ngay</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>