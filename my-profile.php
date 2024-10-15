<?php
session_start();
include('includes/config.php');
error_reporting(0);

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['update'])) {
        $sid = $_SESSION['stdid'];
        $fullname = $_POST['fullname']; // Đã sửa biến thành fullname
        $mobileno = $_POST['mobileno'];
        
        $sql = "UPDATE docgia SET FullName=:fullname, MobileNumber=:mobileno WHERE id=:sid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
        $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
        $query->execute();

        echo '<script>alert("Thông tin tài khoản đã được cập nhật")</script>';
    }

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Hệ thống quản lý thư viện | Thông tin tài khoản</title>
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
                    <h4 class="header-line">Thông tin tài khoản</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            Thay Đổi Thông Tin
                        </div>
                        <div class="panel-body">
                            <form name="update" method="post">
                                <?php
                                $sid = $_SESSION['stdid'];
                                $sql = "SELECT FullName,EmailId,MobileNumber,RegDate,UpdationDate,Status FROM docgia WHERE id=:sid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { ?>

                                        <div class="form-group">
                                            <label>Mã độc giả : </label>
                                            <?php echo htmlentities($sid); ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Ngày tạo tài khoản : </label>
                                            <?php echo htmlentities($result->RegDate); ?>
                                        </div>

                                        <?php if ($result->UpdationDate != "") { ?>
                                            <div class="form-group">
                                                <label>Lần chỉnh sửa cuối : </label>
                                                <?php echo htmlentities($result->UpdationDate); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="form-group">
                                            <label>Trạng thái tài khoản: </label>
                                            <?php if ($result->Status == 1) { ?>
                                                <span style="color: green">Active</span>
                                            <?php } else { ?>
                                                <span style="color: red">Blocked</span>
                                            <?php } ?>
                                        </div>

                                        <div class="form-group">
                                            <label>Họ và Tên</label>
                                            <input class="form-control" type="text" name="fullname" value="<?php echo htmlentities($result->FullName); ?>" autocomplete="off" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Số Điện Thoại:</label>
                                            <input class="form-control" type="text" name="mobileno" maxlength="10" value="<?php echo htmlentities($result->MobileNumber); ?>" autocomplete="off" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Địa Chỉ Email</label>
                                            <input class="form-control" type="email" name="email" id="emailid" value="<?php echo htmlentities($result->EmailId); ?>" autocomplete="off" required readonly />
                                        </div>
                                <?php }
                                } ?>
                                <button type="submit" name="update" class="btn btn-primary" id="submit">Thay đổi </button>
                            </form>
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
<?php } ?>
