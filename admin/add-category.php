<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php"); 
    exit(); // Thêm exit để đảm bảo ngừng thực thi mã sau khi chuyển hướng
} else {

    if (isset($_POST['create'])) {
        $category = $_POST['category'];
        $status = $_POST['status'];
    
        try {
            $sql = "INSERT INTO theloai(CategoryName, Status) VALUES(:category, :status)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->execute();
            
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId) {
                $_SESSION['msg'] = "Thêm thể loại thành công";
            }
            header('location:manage-categories.php');
            exit();
        } catch (PDOException $e) {
            // Kiểm tra lỗi nếu trigger bắt lỗi tên thể loại đã tồn tại
            if (strpos($e->getMessage(), 'Tên thể loại đã tồn tại') !== false) {
                $_SESSION['error'] = "Tên thể loại đã tồn tại. Vui lòng chọn tên khác.";
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
        <title>Quản Lý Thư Viện | Thêm Thể Loại</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
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
                        <h4 class="header-line">Thêm thể loại</h4>
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
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3"">
                        <div class=" panel panel-info">
                        <div class="panel-heading">
                            Thông tin thể loại
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Tên thể loại</label>
                                    <input class="form-control" type="text" name="category" autocomplete="off" required />
                                </div>
                                <div class="form-group">
                                    <label>Trạng Thái</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" id="status" value="1" checked="checked">Kích hoạt
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="status" id="status" value="0">Ẩn
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" name="create" class="btn btn-info">Thêm </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br><br><br><br><br><br><br><br>
        </div>
        </div>
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