<?php
session_start();

include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Thêm exit để đảm bảo ngừng thực thi mã sau khi chuyển hướng
}

// Khởi tạo biến session nếu chưa có
if (!isset($_SESSION['error'])) {
    $_SESSION['error'] = "";
}

if (isset($_POST['create'])) {
    $author = $_POST['author'];
    // Thêm tác giả mới vào cơ sở dữ liệu
    try {
        $sql = "INSERT INTO tacgia (AuthorName) VALUES (:author)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':author', $author, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        if ($lastInsertId) {
            $_SESSION['msg'] = "Thêm Tác Giả thành công";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại !!!";
        }
        header('location:manage-authors.php');
        exit(); // Dừng thực thi mã sau khi chuyển hướng
    } catch (PDOException $e) {
        // Kiểm tra lỗi nếu trigger bắt lỗi tên thể loại đã tồn tại
        if (strpos($e->getMessage(), 'Tên tác giả đã tồn tại') !== false) {
            $_SESSION['error'] = "Tên tác giả đã tồn tại. Vui lòng chọn tên khác.";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại !!!";
        }
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
    <title>Quản Lý Thư Viện | Thêm Tác Giả</title>
    <!-- BOOTSTRAP CORE STYLE -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<body>
    <!-- MENU SECTION START -->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END -->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Thêm Tác Giả</h4>
                </div>
                <div class="row">
                    <?php if (isset($_SESSION['error']) && $_SESSION['error']) { ?>
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>Error:</strong> <?php echo htmlentities($_SESSION['error']); ?>
                                <?php $_SESSION['error'] = ""; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Thông Tin Tác Giả
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Tên Tác Giả</label>
                                    <input class="form-control" type="text" name="author" autocomplete="off" required />
                                </div>
                                <button type="submit" name="create" class="btn btn-info">Thêm</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER SECTION START -->
    <?php include('includes/footer.php'); ?>
    <!-- FOOTER SECTION END -->

    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE LOADING TIME -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>
