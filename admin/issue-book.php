<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit();
} else {
    if (isset($_POST['issue'])) {
        $studentid = strtoupper($_POST['studentid']);
        $bookid = $_POST['bookdetails'];
        $quantityborrow = $_POST['quantityborrow'];

        // Check the available stock of the book
        $sqlStockCheck = "SELECT Stock FROM sach WHERE id = :bookid";
        $queryStockCheck = $dbh->prepare($sqlStockCheck);
        $queryStockCheck->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $queryStockCheck->execute();
        $book = $queryStockCheck->fetch(PDO::FETCH_OBJ);

        // If no book is found or stock is insufficient
        if (!$book || $quantityborrow > $book->Stock) {
            $_SESSION['error'] = "Số lượng sách bạn mượn nhiều hơn số lượng sách trong kho.";
            header('location:issue-book.php');
            exit(); // Ensure that the code after this is not executed
        }

        // Check if the reader has already borrowed this book and has not returned it
        $sqlCheck = "SELECT * FROM ctmuontra WHERE ReaderId = :studentid AND BookId = :bookid AND (BorrowStatus = 1 OR BorrowStatus = 0)";
        $queryCheck = $dbh->prepare($sqlCheck);
        $queryCheck->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $queryCheck->bindParam(':bookid', $bookid, PDO::PARAM_STR);
        $queryCheck->execute();

        if ($queryCheck->rowCount() > 0) {
            // Reader has already borrowed this book and has not returned it
            $_SESSION['error'] = "Bạn đã mượn cuốn sách này và chưa trả.";
            header('location:issue-book.php');
            exit(); // Ensure that the code after this is not executed
        } else {
            // Proceed with issuing the book
            $sql = "INSERT INTO ctmuontra (ReaderId, BookId, QuantityBorrow, Method, BorrowStatus) 
                    VALUES (:studentid, :bookid, :quantityborrow, 0, 1)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':studentid', $studentid, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
            $query->bindParam(':quantityborrow', $quantityborrow, PDO::PARAM_STR);

            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if ($lastInsertId) {
                $_SESSION['msg'] = "Mượn thành công";
                header('location:manage-issued-books.php');
                exit();
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại !!!";
                header('location:issue-book.php');
                exit();
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
        <title>Quản Lý Thư Viện | Mượn Sách</title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <script>
            // function for getting student name
            function getstudent() {
                $("#loaderIcon").show();
                jQuery.ajax({
                    url: "get_student.php",
                    data: 'studentid=' + $("#studentid").val(),
                    type: "POST",
                    success: function(data) {
                        $("#get_student_name").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {
                        $("#loaderIcon").hide();
                    }
                });
            }

            // function for book details
            function getbook() {
                $("#loaderIcon").show();
                jQuery.ajax({
                    url: "get_book.php",
                    data: 'bookid=' + $("#bookid").val(),
                    type: "POST",
                    success: function(data) {
                        $("#get_book_name").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {
                        $("#loaderIcon").hide();
                    }
                });
            }
        </script>
        <style type="text/css">
            .others {
                color: red;
            }
        </style>
    </head>

    <body>
        <!-- MENU SECTION START-->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END-->
        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Mượn sách</h4>
                    </div>
                    <?php if ($_SESSION['error']) { ?>
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>Error:</strong> <?php echo htmlentities($_SESSION['error']); ?>
                                <?php $_SESSION['error'] = ""; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                Thông tin</div>
                            <div class="panel-body">
                                <form role="form" method="post">
                                    <div class="form-group">
                                        <label>Mã độc giả<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="studentid" id="studentid" onBlur="getstudent()" autocomplete="off" required />
                                    </div>
                                    <div class="form-group">
                                        <span id="get_student_name" style="font-size:16px;"></span>
                                    </div>
                                    <div class="form-group">
                                        <label> Số ISBN hoặc Tên Sách<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="bookid" id="bookid" onBlur="getbook()" required="required" />
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="bookdetails" id="get_book_name" required readonly>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label> Số lượng mượn<span style="color:red;">*</span></label>
                                        <input class="form-control" type="number" name="quantityborrow" id="quantityborrow" value="1" min="1" readonly required />
                                    </div>
                                    <button type="submit" name="issue" id="submit" class="btn btn-info">Mượn Sách</button>
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
        <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>