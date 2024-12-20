<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Thêm exit để đảm bảo ngừng thực thi mã sau khi chuyển hướng
} else {
    if (isset($_GET['rid'])) {
        $rid = intval($_GET['rid']);
        $borrowstatus = 2;
        // Correct SQL update statement
        $sql = "UPDATE ctmuontra SET BorrowStatus = :borrowstatus WHERE id = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->bindParam(':borrowstatus', $borrowstatus, PDO::PARAM_STR);
        $query->execute();

        // Set the success message in the session
        $_SESSION['msg'] = "Đã trả sách thành công";

        // Redirect to the same page to display the message
        header('location:manage-issued-books.php');
        exit(); // Make sure to stop script execution after redirection
    }
?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Ouản Lý Thư Viện | Quản lý mượn/trả</title>
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
                        <h4 class="header-line">Quản lý mượn trả OFFLINE</h4>
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
                                    <?php $_SESSION['msg'] = ""; // Clear the message after displaying 
                                    ?>
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
                                Quản lý mượn trả
                                <a style="margin-left: 10px; float: right;" href="export-offline-borrow-return.php" class="btn btn-success">Xuất ra Excel</a>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên độc giả</th>
                                                <th>Tên Sách</th>
                                                <th>ISBN </th>
                                                <th>Ngày mượn</th>
                                                <th>Ngày trả</th>
                                                <th>Số lượng</th>
                                                <th>Trạng thái mượn</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sql = "SELECT docgia.FullName,ctmuontra.BorrowStatus, sach.BookName, sach.ISBNNumber,ctmuontra.QuantityBorrow,ctmuontra.Method, ctmuontra.IssuesDate, ctmuontra.ReturnDate, ctmuontra.id as rid 
                                                FROM ctmuontra 
                                                JOIN docgia ON docgia.id = ctmuontra.ReaderId 
                                                JOIN sach ON sach.id = ctmuontra.BookId
                                                WHERE ctmuontra.Method = 0
                                                ORDER BY ctmuontra.id DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {              ?>
                                                    <tr class="odd gradeX">
                                                        <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->FullName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->BookName); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->ISBNNumber); ?></td>
                                                        <td class="center"><?php echo htmlentities($result->IssuesDate); ?></td>
                                                        <td class="center"><?php if ($result->ReturnDate == "") {
                                                                                echo htmlentities("Chưa trả");
                                                                            } else {
                                                                                echo htmlentities($result->ReturnDate);
                                                                            }
                                                                            ?></td>
                                                        <td class="center"><?php echo htmlentities($result->QuantityBorrow); ?></td>
                                                        <td class="center" style="vertical-align: middle;">
                                                            <div class=" btn-container" style="display: flex; justify-content: center; align-items: center; gap: 3px;">
                                                                <?php if ($result->BorrowStatus == '0') { ?>
                                                                    <a href="manage-issued-books-online.php?approve_id=<?php echo htmlentities($result->rid); ?>">
                                                                        <button class="btn btn-success"><i class="fa fa-check "></i> Duyệt</button>
                                                                    </a>
                                                                    <a href="manage-issued-books-online.php?reject_id=<?php echo htmlentities($result->rid); ?>">
                                                                        <button class="btn btn-warning"><i class="fa fa-times "></i> Từ chối</button>
                                                                    </a>
                                                                <?php } elseif ($result->BorrowStatus == '1') { ?>
                                                                    <button class="btn btn-info" disabled><i class="fa fa-check "></i> Đã duyệt</button>
                                                                <?php } elseif ($result->BorrowStatus == '2') { ?>
                                                                    <button class="btn btn-danger" disabled><i class="fa fa-times "></i> Đã trả</button>
                                                                <?php } else { ?>
                                                                    <button class="btn btn-danger" disabled><i class="fa fa-times "></i> Từ chối</button>
                                                                <?php } ?>
                                                            </div>
                                                        </td>

                                                        <td class="center" style="vertical-align: middle;">
                                                            <div class=" btn-container" style="display: flex; justify-content: center; align-items: center; gap: 3px;">
                                                                <?php if ($result->BorrowStatus == '1') { ?>
                                                                    <a href="manage-issued-books.php?rid=<?php echo htmlentities($result->rid); ?>"><button class="btn btn-primary"><i class="fa fa-edit "></i> Trả Sách</button>
                                                                    <?php } else { ?>
                                                                        <button class="btn btn-danger" disabled><i class="fa fa-edit "></i> Đã trả</button>
                                                                    <?php } ?>
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