<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

// Xử lý khi người dùng bấm nút "Mượn sách"
if (isset($_POST['borrow'])) {
    // Lấy thông tin từ form
    $book_id = intval($_GET['bookid']); // ID sách từ URL
    $quantity = intval($_POST['quantity']);

    // Lấy ReaderId từ session
    $sid = $_SESSION['stdid'];

    // Kiểm tra xem người dùng đã mượn sách này chưa
    $checkSql = "SELECT * FROM ctmuontra WHERE ReaderId = :sid AND BookId = :bookid AND (BorrowStatus = 0 OR BorrowStatus = 1)";
    $checkQuery = $dbh->prepare($checkSql);
    $checkQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
    $checkQuery->bindParam(':bookid', $book_id, PDO::PARAM_INT);
    $checkQuery->execute();

    if ($checkQuery->rowCount() > 0) {
        // Nếu có kết quả trả về, nghĩa là đã mượn sách nhưng chưa trả
        echo "<script>alert('Cuốn sách này bạn đã mượn hoặc đang chờ duyệt.');</script>";
    } else {
        // Kiểm tra số lượng sách có trong kho
        $stockSql = "SELECT Stock FROM sach WHERE id = :bookid"; // Assuming 'Stock' is the column name
        $stockQuery = $dbh->prepare($stockSql);
        $stockQuery->bindParam(':bookid', $book_id, PDO::PARAM_INT);
        $stockQuery->execute();
        $stock = $stockQuery->fetch(PDO::FETCH_OBJ);

        // Kiểm tra số lượng sách mượn so với số lượng sách có
        if ($quantity > $stock->Stock) {
            echo "<script>alert('Số lượng sách bạn mượn nhiều hơn số lượng sách trong kho.');</script>";
        } else {
            // Sử dụng DateTime để lấy ngày mượn và ngày trả
            $issuesdate = new DateTime($_POST['issuesdate']);
            $returndate = new DateTime($_POST['returndate']);

            // Lấy thời gian hiện tại
            $issuesdate->setTime(date('H'), date('i'), date('s')); // Thêm thời gian hiện tại (giờ, phút, giây)

            // Thêm thông tin mượn sách vào bảng ctmuontra
            $sql = "INSERT INTO ctmuontra (ReaderId, BookId, QuantityBorrow, IssuesDate, ReturnDate, Method, BorrowStatus) 
                    VALUES (:sid, :bookid, :quantityborrow, :issuesdate, :returndate, 1, 0)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':sid', $sid, PDO::PARAM_STR);
            $query->bindParam(':bookid', $book_id, PDO::PARAM_INT);
            $query->bindParam(':quantityborrow', $quantity, PDO::PARAM_INT);
            $query->bindParam(':issuesdate', $issuesdate->format('Y-m-d H:i:s'), PDO::PARAM_STR); // Định dạng ngày giờ
            $query->bindParam(':returndate', $returndate->format('Y-m-d H:i:s'), PDO::PARAM_STR); // Định dạng ngày trả

            if ($query->execute()) {
                // Lưu thông báo vào session trước khi chuyển hướng
                $_SESSION['borrow_success'] = "Mượn sách thành công";
                
                // Chuyển hướng đến booklist.php
                header('location:booklist.php');
                exit();
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
                header('location:booklist.php');
                exit();
            }
        }
    }
}


// Lấy thông tin sách dựa trên $book_id
$book_id = intval($_GET['bookid']);
$sql = "SELECT sach.*,theloai.CategoryName,tacgia.AuthorName FROM sach 
            JOIN theloai ON sach.CatId = theloai.id 
            JOIN tacgia ON tacgia.id = sach.AuthorId 
            WHERE sach.id=:book_id ";
$query = $dbh->prepare($sql);
$query->bindParam(':book_id', $book_id, PDO::PARAM_INT);
$query->execute();
$book = $query->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Quản Lý Thư Viện | Mượn Sách</title>
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
                    <h4 class="header-line">Mượn Sách</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Thông tin sách
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <!-- Tên sách -->
                                <div class="form-group">
                                    <label>Tên sách<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($book->BookName); ?>" autocomplete="off" required readonly />
                                </div>

                                <!-- Thể loại sách -->
                                <div class="form-group">
                                    <label>Thể loại<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="category" value="<?php echo htmlentities($book->CategoryName); ?>" readonly></input>
                                </div>

                                <!-- Tác giả -->
                                <div class="form-group">
                                    <label>Tác Giả<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="author" value="<?php echo htmlentities($book->AuthorName); ?>" readonly></input>
                                </div>

                                <!-- ISBN -->
                                <div class="form-group">
                                    <label>Mã số tiêu chuẩn quốc tế cho sách (ISBN)<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($book->ISBNNumber); ?>" required autocomplete="off" readonly />
                                    <p class="help-block">ISBN là mã số tiêu chuẩn quốc tế cho sách. ISBN phải là duy nhất</p>
                                </div>

                                <!-- Số lượng -->
                                <div class="form-group">
                                    <label>Số lượng<span style="color:red;">*</span></label>
                                    <input class="form-control" type="number" name="quantity" value="1" min="1" readonly required />
                                </div>


                                <!-- Ngày mượn -->
                                <div class="form-group">
                                    <label>Ngày mượn<span style="color:red;">*</span></label>
                                    <input class="form-control" type="date" name="issuesdate" id="issuesdate" required />
                                </div>

                                <!-- Ngày trả -->
                                <div class="form-group">
                                    <label>Ngày trả<span style="color:red;">*</span></label>
                                    <input class="form-control" type="date" name="returndate" id="returndate" required />
                                </div>

                                <!-- Nút mượn sách -->
                                <button type="submit" name="borrow" class="btn btn-info">Mượn sách</button>

                                <!-- Nút hủy bỏ -->
                                <button type="button" class="btn btn-danger" onclick="window.location.href='booklist.php';">Hủy bỏ</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <!-- Scripts -->
    <script>
        // Lấy ngày hiện tại
        var today = new Date().toISOString().split('T')[0];
        // Đặt ngày hiện tại vào input của ngày mượn
        document.getElementById("issuesdate").value = today;
        // Kiểm tra hợp lệ trước khi gửi form
        document.querySelector('form').addEventListener('submit', function(e) {
            var issuesDate = document.getElementById("issuesdate").value;
            var returnDate = document.getElementById("returndate").value;

            // Nếu ngày trả nhỏ hơn hoặc bằng ngày mượn
            if (returnDate <= issuesDate) {
                e.preventDefault(); // Ngăn gửi form
                alert('Ngày trả không được nhỏ hơn hoặc bằng ngày mượn.');
            }
        });
    </script>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>