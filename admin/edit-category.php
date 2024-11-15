<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit();
} else {
    if (isset($_POST['update'])) {
        $category = $_POST['category'];
        $status = $_POST['status'];
        $catid = intval($_GET['catid']);

        // Kiểm tra tên thể loại đã tồn tại chưa (ngoại trừ thể loại hiện tại)
        $checkSql = "SELECT * FROM theloai WHERE CategoryName=:category AND id != :catid";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':category', $category, PDO::PARAM_STR);
        $checkQuery->bindParam(':catid', $catid, PDO::PARAM_INT);
        $checkQuery->execute();

        if ($checkQuery->rowCount() > 0) {
            $_SESSION['error'] = "Tên thể loại đã tồn tại, vui lòng chọn tên khác!";
            header("location:edit-category.php?catid=" . $catid);
            exit(); // Dừng thực thi mã sau khi chuyển hướng
        } else {
            // Thực hiện cập nhật nếu tên thể loại chưa tồn tại
            $sql = "UPDATE theloai SET CategoryName=:category, Status=:status WHERE id=:catid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':catid', $catid, PDO::PARAM_INT);
            $query->execute();

            $_SESSION['success'] = "Thể loại đã được cập nhật thành công!";
            header('location:manage-categories.php');
            exit(); // Dừng thực thi mã sau khi chuyển hướng
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
        <title>Quản Lý Thư Viện | Sửa Thể Loại</title>

        <!-- BOOTSTRAP CORE STYLE -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLE -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <!-- MENU SECTION START -->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END -->

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Sửa Thể Loại</h4>
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
                                Thông tin thể loại
                            </div>

                            <div class="panel-body">
                                <form role="form" method="post">
                                    <?php
                                    $catid = intval($_GET['catid']);
                                    $sql = "SELECT * FROM theloai WHERE id=:catid";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':catid', $catid, PDO::PARAM_INT);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {
                                    ?>
                                            <div class="form-group">
                                                <label>Tên Thể Loại</label>
                                                <input class="form-control" type="text" name="category" value="<?php echo htmlentities($result->CategoryName); ?>" required />
                                            </div>

                                            <div class="form-group">
                                                <label>Status</label>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="status" value="1" <?php if ($result->Status == 1) echo 'checked'; ?>> Kích hoạt
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="status" value="0" <?php if ($result->Status == 0) echo 'checked'; ?>> Ẩn
                                                    </label>
                                                </div>
                                            </div>
                                    <?php }
                                    } ?>
                                    <button type="submit" name="update" class="btn btn-info">Cập Nhật</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTENT-WRAPPER SECTION END -->

        <?php include('includes/footer.php'); ?>

        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE LOADING TIME -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>