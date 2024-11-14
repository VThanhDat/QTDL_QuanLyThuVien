<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Ensure to stop executing code after redirection
} else {
    if (isset($_POST['update'])) {
        try {
            $athrid = intval($_GET['athrid']);
            $author = $_POST['author'];
            $sql = "UPDATE tacgia SET AuthorName=:author WHERE id=:athrid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':author', $author, PDO::PARAM_STR);
            $query->bindParam(':athrid', $athrid, PDO::PARAM_INT);
            $query->execute();
            $_SESSION['updatemsg'] = "Cập nhật thông tin tác giả thành công";
            header('location:manage-authors.php');
            exit();
        } catch (PDOException $e) {
            // Kiểm tra thông báo lỗi từ trigger
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = "Tên tác giả đã tồn tại. Vui lòng chọn tên khác.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
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
        <title>Online Library Management System | Add Author</title>
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
        <!------MENU SECTION START-->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END-->

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Thêm tác giả</h4>
                    </div>
                    <div class="row">
                        <?php if ($_SESSION['error']) { ?>
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
                                Thông tin tác giả
                            </div>
                            <div class="panel-body">
                                <form role="form" method="post">
                                    <div class="form-group">
                                        <label>Tên tác giả</label>
                                        <?php
                                        $athrid = intval($_GET['athrid']);
                                        $sql = "SELECT * FROM tacgia WHERE id=:athrid";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':athrid', $athrid, PDO::PARAM_INT);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                        ?>
                                                <input class="form-control" type="text" name="author" value="<?php echo htmlentities($result->AuthorName); ?>" required />
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button type="submit" name="update" class="btn btn-info">Cập nhật</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTENT-WRAPPER SECTION END-->
        <?php include('includes/footer.php'); ?>
        <!-- FOOTER SECTION END-->
        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>