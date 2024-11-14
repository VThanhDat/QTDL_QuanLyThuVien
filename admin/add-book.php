<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (strlen($_SESSION['alogin']) == 0) {
    header("location:../adminlogin.php");
    exit(); // Thêm exit để đảm bảo ngừng thực thi mã sau khi chuyển hướng
} else {
    // Xử lý khi người dùng gửi form thêm sách
    if (isset($_POST['add'])) {
        $bookname = $_POST['bookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $stock = $_POST['quantity'];
        $method = $_POST['method'];

        // Xử lý ảnh
        $image = $_FILES["book_image"]["name"];
        $image_tmp = $_FILES["book_image"]["tmp_name"];
        $image_extension = pathinfo($image, PATHINFO_EXTENSION);
        $image_new_name = time() . '.' . $image_extension;  // Tạo tên mới để tránh trùng

        // Đường dẫn lưu trữ ảnh trong thư mục admin/assets/img
        $image_folder = __DIR__ . "/assets/img/" . $image_new_name;

        // Kiểm tra sự tồn tại của sách trước khi thêm
        $sql_check = "SELECT * FROM sach WHERE BookName=:bookname AND CatId=:category AND AuthorId=:author";
        $query_check = $dbh->prepare($sql_check);
        $query_check->bindParam(':bookname', $bookname, PDO::PARAM_STR);
        $query_check->bindParam(':category', $category, PDO::PARAM_INT);
        $query_check->bindParam(':author', $author, PDO::PARAM_INT);
        $query_check->execute();

        if ($query_check->rowCount() > 0) {
            // Nếu sách đã tồn tại
            $_SESSION['error'] = "Cuốn sách này đã tồn tại";
            header('location:manage-books.php');
        } else {
            // Di chuyển ảnh đã upload vào thư mục img
            move_uploaded_file($image_tmp, $image_folder);

            // Gọi stored procedure để thêm sách vào cơ sở dữ liệu
            $sql = "CALL InsertBook(:bookname, :category, :author, :isbn, :price, :image, :quantity, :stock, :method)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bookname', $bookname, PDO::PARAM_STR);
            $query->bindParam(':category', $category, PDO::PARAM_INT);
            $query->bindParam(':author', $author, PDO::PARAM_INT);
            $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':image', $image_new_name, PDO::PARAM_STR);  // Lưu tên ảnh vào
            $query->bindParam(':quantity', $quantity, PDO::PARAM_STR);
            $query->bindParam(':stock', $stock, PDO::PARAM_STR);
            $query->bindParam(':method', $method, PDO::PARAM_STR);

            // Thực thi truy vấn
            $query->execute();
            $rowCount = $query->rowCount();

            // Kiểm tra xem việc thêm sách có thành công không
            if ($rowCount > 0) {
                $_SESSION['msg'] = "Thêm sách thành công";
                header('location:manage-books.php');
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại !!!";
                header('location:manage-books.php');
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
        <title>Quản Lý Thư Viện | Thêm Sách</title>
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <link href="assets/css/style.css" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    </head>

    <body>
        <!-- Gồm header của trang -->
        <?php include('includes/header.php'); ?>

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line">Thêm Sách</h4>
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
                                        <input class="form-control" type="text" name="bookname" autocomplete="off" required />
                                    </div>

                                    <!-- Thể loại sách -->
                                    <div class="form-group">
                                        <label>Thể loại<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required>
                                            <option value="">Chọn thể loại</option>
                                            <?php
                                            $status = 1;
                                            $sql = "SELECT * FROM theloai WHERE Status=:status";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':status', $status, PDO::PARAM_STR);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <option value="<?php echo htmlentities($result->id); ?>">
                                                        <?php echo htmlentities($result->CategoryName); ?>
                                                    </option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>

                                    <!-- Tác giả -->
                                    <div class="form-group">
                                        <label>Tác Giả<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required>
                                            <option value="">Chọn Tác Giả</option>
                                            <?php
                                            $sql = "SELECT * FROM tacgia";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) { ?>
                                                    <option value="<?php echo htmlentities($result->id); ?>">
                                                        <?php echo htmlentities($result->AuthorName); ?>
                                                    </option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>

                                    <!-- ISBN -->
                                    <div class="form-group">
                                        <label>Mã số tiêu chuẩn quốc tế cho sách (ISBN)<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" required autocomplete="off" />
                                        <p class="help-block">ISBN là mã số tiêu chuẩn quốc tế cho sách. ISBN phải là duy nhất</p>
                                    </div>

                                    <!-- Phí mượn sách -->
                                    <div class="form-group">
                                        <label>Phí mượn sách<span style="color:red;">*</span></label>
                                        <input class="form-control" type="number" name="price" autocomplete="off" min="0" required />
                                    </div>

                                    <!-- Số lượng -->
                                    <div class="form-group">
                                        <label>Số lượng<span style="color:red;">*</span></label>
                                        <input class="form-control" type="number" name="quantity" min="0" required />
                                    </div>

                                    <div class="form-group">
                                        <label>Hình thức</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="method" id="method" value="1" checked="checked">Online
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="method" id="method" value="0">Offline
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Hình ảnh -->
                                    <div class="form-group">
                                        <label>Hình ảnh sách<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="book_image" required />
                                    </div>

                                    <!-- Nút thêm -->
                                    <button type="submit" name="add" class="btn btn-info">Thêm</button>
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
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>