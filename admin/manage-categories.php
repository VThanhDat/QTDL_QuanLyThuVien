<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Thêm exit để đảm bảo ngừng thực thi mã sau khi chuyển hướng
} else {
    if (isset($_GET['del'])) {
        $id = $_GET['del'];
        try {
            $sql = "DELETE FROM theloai WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $_SESSION['delmsg'] = "Xóa thể loại thành công!!";
        } catch (PDOException $e) {
            // Thông báo lỗi chi tiết để ghi log hoặc hiển thị
            $_SESSION['error'] = "Không thể xóa tên thể loại này. ";
        }
        header('location:manage-categories.php');
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
        <title>Quản Lý Thư Viện | Quản Lý Thể Loại</title>

        <!-- BOOTSTRAP CORE STYLE -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- DATATABLE STYLE -->
        <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
                        <h4 class="header-line">Quản Lý Thể Loại</h4>
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

                        <?php if ($_SESSION['msg']) { ?>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Success:</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                                    <?php $_SESSION['msg'] = ""; ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($_SESSION['updatemsg']) { ?>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Success:</strong> <?php echo htmlentities($_SESSION['updatemsg']); ?>
                                    <?php $_SESSION['updatemsg'] = ""; ?>
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
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Advanced Tables -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Danh sách thể loại
                                <a style="margin-left: 10px; float: right;" href="export-categories.php" class="btn btn-success">Xuất ra Excel</a>
                            </div>

                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Thể loại</th>
                                                <th>Trạng Thái</th>
                                                <th>Ngày Tạo</th>
                                                <th>Ngày Cập Nhật</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM theloai";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr class="odd gradeX">
                                                        <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->CategoryName); ?></td>
                                                        <td class="center">
                                                            <?php if ($result->Status == 1) { ?>
                                                                <a href="#" class="btn btn-success btn-xs">Kích hoạt</a>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-danger btn-xs">Ẩn</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="center"><?php echo htmlentities($result->CreationDate); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->UpdationDate); ?></td>
                                                        <td class="center" style="vertical-align: middle;">
                                                            <div class=" btn-container" style="display: flex; justify-content: center; align-items: center; gap: 3px;">
                                                                <a href="edit-category.php?catid=<?php echo htmlentities($result->id); ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Sửa</a>
                                                                <a href="manage-categories.php?del=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Bạn đồng ý xóa thể loại?');" class="btn btn-danger"><i class="fa fa-trash"></i> Xóa</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            <?php $cnt++;
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

        <!-- CONTENT-WRAPPER SECTION END -->
        <?php include('includes/footer.php'); ?>
        <!-- FOOTER SECTION END -->

        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE LOADING TIME -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>