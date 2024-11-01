<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit();
} else {
    // Update stock on page load
    $sqlUpdate = "UPDATE sach SET Stock = Quantity - 
                  (SELECT COALESCE(SUM(QuantityBorrow), 0) AS TotalBorrowedBooks 
                   FROM ctmuontra 
                   WHERE BorrowStatus = 1 AND BookId = sach.id)";
    $queryUpdate = $dbh->prepare($sqlUpdate);
    $queryUpdate->execute();

    // Delete book
    if (isset($_GET['del'])) {
        $id = $_GET['del'];
        $sql = "DELETE FROM sach WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        if ($query->execute()) {
            $_SESSION['delmsg'] = "Xóa sách thành công";
        } else {
            $_SESSION['error'] = "Không thể xóa sách";
        }
        header('location:manage-books.php');
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
        <title>Quản Lý Thư Viện | Quản Lý Sách</title>
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <link href="assets/css/style.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    </head>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Quản Lý Sách</h4>
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
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Danh sách Sách
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên Sách</th>
                                                <th>Hình ảnh</th>
                                                <th>Thể Loại</th>
                                                <th>Tác Giả</th>
                                                <th>Giá</th>
                                                <th>Số lượng</th>
                                                <th>Hình thức</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT sach.BookName, sach.Image, theloai.CategoryName, tacgia.AuthorName, sach.BookPrice, sach.Quantity, sach.Stock, sach.Method, sach.id as bookid 
                                                FROM sach 
                                                JOIN theloai ON theloai.id = sach.CatId 
                                                JOIN tacgia ON tacgia.id = sach.AuthorId";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <tr class="odd gradeX">
                                                        <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                                        <td class="center">
                                                            <img src="../admin/assets/img/<?php echo htmlentities($result->Image); ?>" alt="<?php echo htmlentities($result->BookName); ?>" style="width: 80px; height: 80px;" />
                                                        </td>
                                                        <td class="center"><?php echo htmlentities($result->CategoryName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->AuthorName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->BookPrice); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->Stock); ?>/<?php echo htmlentities($result->Quantity); ?></td>
                                                        <td class="center">
                                                            <?php if ($result->Method == 1) { ?>
                                                                <a href="#" class="btn btn-success btn-xs">Online</a>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-danger btn-xs">Offline</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="center">
                                                            <a href="edit-book.php?bookid=<?php echo htmlentities($result->bookid); ?>">
                                                                <button class="btn btn-primary"><i class="fa fa-edit"></i> Sửa</button>
                                                            </a>
                                                            <a href="manage-books.php?del=<?php echo htmlentities($result->bookid); ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?');">
                                                                <button class="btn btn-danger"><i class="fa fa-pencil"></i> Xóa</button>
                                                            </a>
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
                    </div>
                </div>
            </div>
        </div>

        <?php include('includes/footer.php'); ?>
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/dataTables/jquery.dataTables.js"></script>
        <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>