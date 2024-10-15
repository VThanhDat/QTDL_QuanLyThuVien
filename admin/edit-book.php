<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php"); 
    exit(); // Ensure to stop executing code after redirection
}

if (isset($_POST['update'])) {
    $bookname = $_POST['bookname'];
    $category = $_POST['category'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $method = $_POST['method'];
    $bookid = intval($_GET['bookid']);

    // Step 1: Retrieve the current stock of the book
    $sqlCurrentStock = "SELECT Stock FROM sach WHERE id=:bookid";
    $queryCurrentStock = $dbh->prepare($sqlCurrentStock);
    $queryCurrentStock->bindParam(':bookid', $bookid, PDO::PARAM_STR);
    $queryCurrentStock->execute();
    $currentStockResult = $queryCurrentStock->fetch(PDO::FETCH_OBJ);

    // Check if the current stock exists
    if ($currentStockResult) {
        // Calculate the new stock after update
        $newStock = $quantity - $currentStockResult->Stock;

        // Check if the new stock would be less than 0
        if ($newStock < 0) {
            $_SESSION['msg'] = "Số lượng sách sửa đổi sẽ làm cho số lượng tồn kho trở về dưới 0.";
        } else {
            // Proceed with the image upload and update logic if the stock is valid
            $target_dir = "F:/Github/QTDL_QuanLyThuVien/admin/assets/img/"; // Ensure this directory is writable
            $imagePath = null; // Initialize image path variable

            // Handle image upload
            if (!empty($_FILES["book_image"]["name"])) {
                $target_file = $target_dir . basename($_FILES["book_image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if file is an actual image
                $check = getimagesize($_FILES["book_image"]["tmp_name"]);
                if ($check === false) {
                    $_SESSION['msg'] = "Tệp đã chọn không phải là hình ảnh.";
                } else {
                    // Allow certain file formats
                    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $_SESSION['msg'] = "Chỉ cho phép định dạng JPG, JPEG, PNG & GIF.";
                    } else {
                        if (move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file)) {
                            $imagePath = basename($_FILES["book_image"]["name"]); // Store only the filename for the database
                        } else {
                            $_SESSION['msg'] = "Lỗi trong việc tải lên hình ảnh.";
                        }
                    }
                }
            }

            // Update book information including image path if a new image was uploaded
            $sql = "UPDATE sach SET BookName=:bookname, CatId=:category, AuthorId=:author, ISBNNumber=:isbn, BookPrice=:price, Quantity=:quantity, Method=:method" . 
                   ($imagePath ? ", Image=:image" : "") . 
                   " WHERE id=:bookid";

            $query = $dbh->prepare($sql);
            $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':author', $author, PDO::PARAM_STR);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':quantity', $quantity, PDO::PARAM_STR);
            $query->bindParam(':method', $method, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);

            // Bind image parameter only if a new image was uploaded
            if ($imagePath) {
                $query->bindParam(':image', $imagePath, PDO::PARAM_STR);
            }

            $query->execute();

            $_SESSION['msg'] = "Cập nhật thông tin sách thành công";
            header('location:manage-books.php');
            exit();
        }
    } else {
        $_SESSION['msg'] = "Sách không tồn tại trong hệ thống.";
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
    <title>Quản Lý Thư Viện | Chỉnh Sửa Sách</title>
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
                    <h4 class="header-line">Chỉnh Sửa Thông Tin Sách</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Thông Tin Sách
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <?php
                                $bookid = intval($_GET['bookid']);
                                $sql = "SELECT sach.BookName,sach.Quantity,sach.Method ,theloai.CategoryName, theloai.id as cid, tacgia.AuthorName, tacgia.id as athrid, sach.ISBNNumber, sach.BookPrice, sach.Image, sach.id as bookid FROM sach JOIN theloai ON theloai.id=sach.CatId JOIN tacgia ON tacgia.id=sach.AuthorId WHERE sach.id=:bookid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) {
                                ?>
                                        <div class="form-group">
                                            <label>Tên Sách<span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->BookName); ?>" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Thể Loại<span style="color:red;">*</span></label>
                                            <select class="form-control" name="category" required>
                                                <option value="<?php echo htmlentities($result->cid); ?>">
                                                    <?php echo htmlentities($result->CategoryName); ?>
                                                </option>
                                                <?php
                                                $status = 1;
                                                $sql1 = "SELECT * FROM theloai WHERE Status=:status";
                                                $query1 = $dbh->prepare($sql1);
                                                $query1->bindParam(':status', $status, PDO::PARAM_STR);
                                                $query1->execute();
                                                $resultss = $query1->fetchAll(PDO::FETCH_OBJ);

                                                if ($query1->rowCount() > 0) {
                                                    foreach ($resultss as $row) {
                                                        if ($result->CategoryName != $row->CategoryName) {
                                                ?>
                                                            <option value="<?php echo htmlentities($row->id); ?>"><?php echo htmlentities($row->CategoryName); ?></option>
                                                <?php
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Tác Giả<span style="color:red;">*</span></label>
                                            <select class="form-control" name="author" required>
                                                <option value="<?php echo htmlentities($result->athrid); ?>">
                                                    <?php echo htmlentities($result->AuthorName); ?>
                                                </option>
                                                <?php
                                                $sql2 = "SELECT * FROM tacgia";
                                                $query2 = $dbh->prepare($sql2);
                                                $query2->execute();
                                                $result2 = $query2->fetchAll(PDO::FETCH_OBJ);

                                                if ($query2->rowCount() > 0) {
                                                    foreach ($result2 as $ret) {
                                                        if ($result->AuthorName != $ret->AuthorName) {
                                                ?>
                                                            <option value="<?php echo htmlentities($ret->id); ?>"><?php echo htmlentities($ret->AuthorName); ?></option>
                                                <?php
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Số ISBN<span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($result->ISBNNumber); ?>" required />
                                            <p class="help-block">ISBN là số tiêu chuẩn quốc tế cho sách. ISBN phải là duy nhất.</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Giá (USD)<span style="color:red;">*</span></label>
                                            <input class="form-control" type="number" name="price" value="<?php echo htmlentities($result->BookPrice); ?>" min="0" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Số  lượng<span style="color:red;">*</span></label>
                                            <input class="form-control" type="number" name="quantity" value="<?php echo htmlentities($result->Quantity); ?>" min="0" required />
                                        </div>

                                        <div class="form-group">
                                            <label>Hình thức</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="method" value="1" <?php if ($result->Method == 1) echo 'checked'; ?>> Online
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="method" value="0" <?php if ($result->Method == 0) echo 'checked'; ?>> Offline
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Hình Ảnh Bìa Sách</label><br>
                                            <?php if ($result->Image) { ?>
                                                <img src="assets/img/<?php echo htmlentities($result->Image); ?>" style="max-width: 100px; max-height: 150px;" alt="Hình Ảnh Bìa Sách"><br>
                                                <p>Hình ảnh hiện tại: <?php echo htmlentities($result->Image); ?></p>
                                            <?php } ?>
                                            <input type="file" name="book_image" />
                                            <p class="help-block">Chọn hình ảnh bìa sách (nếu có)</p>
                                        </div>
                                        

                                <?php }
                                } ?>
                                <button type="submit" name="update" class="btn btn-info">Cập nhật</button>
                                <button type="button" class="btn btn-danger" onclick="window.location.href='manage-books.php'">Quay lại</button>
                            </form>
                            <div style="color:red;"><?php echo htmlentities($_SESSION['msg']); $_SESSION['msg'] = ""; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
