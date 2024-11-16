<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Đảm bảo ngừng thực thi mã sau khi chuyển hướng
} else {
    // Code để chặn độc giả
    if (isset($_GET['inid'])) {
        $id = $_GET['inid'];
        $status = 0;
        $sql = "UPDATE docgia SET Status=:status WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();
        header('location:reg-students.php');
        exit();
    }

    // Khôi phục mật khẩu về mặc định
    if (isset($_GET['resetid'])) {
        $id = $_GET['resetid'];
        $defaultPassword = md5('123456'); // Mã hóa mật khẩu mặc định
        $sql = "UPDATE docgia SET Password=:password WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':password', $defaultPassword, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();
        $_SESSION['success'] = "Khôi phục mật khẩu thành công";
        header('location:reg-students.php');
        exit();
    }


    // Code để kích hoạt độc giả
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $status = 1;
        $sql = "UPDATE docgia SET Status=:status WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();
        header('location:reg-students.php');
        exit();
    }

    // Xóa độc giả
    if (isset($_GET['delid'])) {
        $id = $_GET['delid'];
        try {
            $sql = "DELETE FROM docgia WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_STR);
            $query->execute();
            $_SESSION['delmsg'] = "Xóa độc giả thành công";
        } catch (PDOException $e) {
            // Thông báo lỗi nếu có sự cố khi thực thi câu lệnh
            $_SESSION['error'] = "Không thể xóa độc giả này vì còn giao dịch mượn trả";
        }
        header('location:reg-students.php');
        exit();
    }
?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Quản Lý Thư Viện | Quản lý tài khoản</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- DATATABLE STYLE  -->
        <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
                        <h4 class="header-line">Quản lý tài khoản</h4>
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

                        <?php if ($_SESSION['delmsg']) { ?>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Success:</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php $_SESSION['delmsg'] = ""; ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($_SESSION['success']) { ?>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Success:</strong> <?php echo htmlentities($_SESSION['success']); ?>
                                    <?php $_SESSION['success'] = ""; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- Advanced Tables -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Tài khoản
                                <a style="margin-left: 10px; float: right;" href="export-users.php" class="btn btn-success">Xuất ra Excel</a>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên độc giả</th>
                                                <th>Email</th>
                                                <th>SĐT</th>
                                                <th>Ngày đăng kí</th>
                                                <th>Trạng thái</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM docgia";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr class="odd gradeX">
                                                        <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->FullName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->EmailId); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->MobileNumber); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->RegDate); ?></td>
                                                        <td class="center"><?php if ($result->Status == 1) {
                                                                                echo htmlentities("Đang Hoạt Động");
                                                                            } else {
                                                                                echo htmlentities("Đã Bị Chặn");
                                                                            } ?></td>
                                                        <td class="center" style="vertical-align: middle;">
                                                            <div class="btn-container" style="display: flex; justify-content: center; align-items: center; gap: 3px;">
                                                                <?php if ($result->Status == 1) { ?>
                                                                    <a href="reg-students.php?inid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Bạn có chắc muốn chặn người này?');">
                                                                        <button class="btn btn-danger">Chặn</button>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <a href="reg-students.php?id=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Bạn có muốn bỏ chặn người này?');">
                                                                        <button class="btn btn-primary">Bỏ Chặn</button>
                                                                    </a>
                                                                <?php } ?>
                                                                <a href="reg-students.php?resetid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Bạn có chắc muốn khôi phục mật khẩu của người này về mặc định?');">
        <button class="btn btn-info">Khôi phục mật khẩu</button>
    </a>
                                                                <a href="reg-students.php?delid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Bạn có chắc muốn xóa người này?');">
                                                                    <button class="btn btn-warning">Xóa</button>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php $cnt = $cnt + 1;
                                                }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--End Advanced Tables -->
                    </div>
                </div>
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
        <!-- DATATABLE SCRIPTS  -->
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
        <!-- CUSTOM SCRIPTS  -->
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>