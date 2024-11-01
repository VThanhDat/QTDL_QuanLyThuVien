<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

// Số lượng sách mỗi trang
$booksPerPage = 8; // Thay đổi giá trị này nếu cần
// Xác định trang hiện tại
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Đảm bảo trang bắt đầu từ 1

// Tính toán offset
$offset = ($current_page - 1) * $booksPerPage;

// Truy vấn để lấy tổng số sách
$sqlTotal = "SELECT COUNT(*) FROM sach";
$queryTotal = $dbh->prepare($sqlTotal);
$queryTotal->execute();
$totalBooks = $queryTotal->fetchColumn();
$totalPages = ceil($totalBooks / $booksPerPage);

// Truy vấn để lấy dữ liệu sách từ cơ sở dữ liệu với phân trang
$sql = "SELECT * FROM sach LIMIT :offset, :booksPerPage";
$query = $dbh->prepare($sql);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->bindParam(':booksPerPage', $booksPerPage, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Hệ thống quản lý thư viện | Trang Admin</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">
                        <i class="fas fa-book" style="margin-right: 10px;"></i> SÁCH
                    </h4>
                </div>
            </div>
            <div class="row">
                <?php if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <div class="card" style="width: 23rem;">
                                <!-- Hiển thị hình ảnh sách nếu có -->
                                <div class="card-img">
                                    <img src="../admin/assets/img/<?php echo htmlentities($result->Image); ?>" class="card-img-top" alt="<?php echo htmlentities($result->BookName); ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlentities($result->BookName); ?></h5>
                                    <p class="card-text">Hình thức:<?php if ($result->Method == 1) { ?>
                                        <a href="#" class="btn btn-success btn-xs">Online</a>
                                    <?php } else { ?>
                                        <a href="#" class="btn btn-danger btn-xs">Offline</a>
                                    <?php } ?>
                                    </p>
                                    <a href="<?php echo ($result->Method == 0) ? 'javascript:void(0);' : 'detailsbook.php?bookid=' . htmlentities($result->id); ?>"
                                        class="btn btn-primary"
                                        <?php if ($result->Method == 0) { ?>
                                        onclick="alert('Sách này phải đến thư viện để được mượn trực tiếp.');"
                                        <?php } ?>>
                                        Mượn sách
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="col-md-12">
                        <p>Không có sách nào để hiển thị.</p>
                    </div>
                <?php } ?>
            </div>

            <!-- Phân trang -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <ul class="pagination">
                        <?php for ($page = 1; $page <= $totalPages; $page++) { ?>
                            <li class="<?php echo ($page == $current_page) ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>