<?php
session_start();
include('includes/config.php');
error_reporting(0);
if (isset($_POST['signup'])) {
    $fname = $_POST['fullname'];
    $mobileno = $_POST['mobileno'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $status = 1;
    $sql = "INSERT INTO docgia(FullName,MobileNumber,EmailId,Password,Status) VALUES(:fname,:mobileno,:email,:password,:status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        echo '<script>alert("Đăng kí tài khoản thành công")</script>';
    } else {
        echo "<script>alert('Có gì đó sai sai, vui lòng thử lại');</script>";
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
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
    <title>Hệ thống quản lý thư viện | Đăng ký</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script type="text/javascript">
        function valid() {
            if (document.signup.password.value != document.signup.confirmpassword.value) {
                alert("Mật khẩu nhập lại không trùng khớp!!");
                document.signup.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
    <script>
        function checkAvailability() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'emailid=' + $("#emailid").val(),
                type: "POST",
                success: function(data) {
                    $("#user-availability-status").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
    </script>

</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">ĐĂNG KÝ</h4>

                </div>
            </div>
            <div class="row">

                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Đăng ký
                        </div>
                        <div class="panel-body">
                            <form name="signup" method="post" onSubmit="return valid();">
                                <div class="form-group">
                                    <label>Họ và tên</label>
                                    <input class="form-control" type="text" name="fullname" autocomplete="off" required />
                                </div>


                                <div class="form-group">
                                    <label>Số điện thoại :</label>
                                    <input class="form-control" type="text" name="mobileno" maxlength="10" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <label>Nhập Email</label>
                                    <input class="form-control" type="email" name="email" id="emailid" onBlur="checkAvailability()" autocomplete="off" required />
                                    <span id="user-availability-status" style="font-size:12px;"></span>
                                </div>

                                <div class="form-group">
                                    <label>Nhập Mật khẩu</label>
                                    <input class="form-control" type="password" name="password" autocomplete="off" required />
                                </div>

                                <div class="form-group">
                                    <label>Xác nhận lại Mật khẩu </label>
                                    <input class="form-control" type="password" name="confirmpassword" autocomplete="off" required />
                                </div>

                                <button type="submit" name="signup" class="btn btn-success" id="submit">Đăng ký ngay </button>
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