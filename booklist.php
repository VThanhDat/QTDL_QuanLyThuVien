<?php
session_start();
error_reporting(0);
if (isset($_SESSION['borrow_success'])) {
    echo "<script>alert('" . $_SESSION['borrow_success'] . "');</script>";
    unset($_SESSION['borrow_success']); // Xóa thông báo sau khi đã hiển thị
}
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

// Lấy từ khóa tìm kiếm và hình thức (Online hoặc Offline) nếu có
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$methodFilter = isset($_GET['method']) ? (int)$_GET['method'] : -1; // -1: không lọc
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : -1; // -1: không lọc

// Số lượng sách mỗi trang
$booksPerPage = 8;

// Xác định trang hiện tại
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);

// Tính toán offset
$offset = ($current_page - 1) * $booksPerPage;

// Truy vấn để lấy tổng số sách phù hợp với từ khóa, hình thức và thể loại (nếu có)
$sqlTotal = "SELECT COUNT(*) FROM sach WHERE 1=1";
if ($searchKeyword) {
    $sqlTotal .= " AND BookName LIKE :keyword";
}
if ($methodFilter != -1) {
    $sqlTotal .= " AND Method = :method";
}
if ($categoryFilter != -1) {
    $sqlTotal .= " AND CatId = :category";
}
$queryTotal = $dbh->prepare($sqlTotal);
if ($searchKeyword) {
    $queryTotal->bindValue(':keyword', '%' . $searchKeyword . '%', PDO::PARAM_STR);
}
if ($methodFilter != -1) {
    $queryTotal->bindValue(':method', $methodFilter, PDO::PARAM_INT);
}
if ($categoryFilter != -1) {
    $queryTotal->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
}
$queryTotal->execute();
$totalBooks = $queryTotal->fetchColumn();
$totalPages = ceil($totalBooks / $booksPerPage);

// Truy vấn để lấy dữ liệu sách từ cơ sở dữ liệu với phân trang, từ khóa tìm kiếm, hình thức và thể loại
$sql = "SELECT * FROM sach WHERE 1=1";
if ($searchKeyword) {
    $sql .= " AND BookName LIKE :keyword";
}
if ($methodFilter != -1) {
    $sql .= " AND Method = :method";
}
if ($categoryFilter != -1) {
    $sql .= " AND CatId = :category";
}
$sql .= " LIMIT :offset, :booksPerPage";
$query = $dbh->prepare($sql);
if ($searchKeyword) {
    $query->bindValue(':keyword', '%' . $searchKeyword . '%', PDO::PARAM_STR);
}
if ($methodFilter != -1) {
    $query->bindValue(':method', $methodFilter, PDO::PARAM_INT);
}
if ($categoryFilter != -1) {
    $query->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
}
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->bindParam(':booksPerPage', $booksPerPage, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Truy vấn để lấy danh sách thể loại sách
$sqlCategories = "SELECT * FROM theloai";
$queryCategories = $dbh->prepare($sqlCategories);
$queryCategories->execute();
$categories = $queryCategories->fetchAll(PDO::FETCH_OBJ);
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
                <div class="col-md-6">
                    <h4 class="header-line">
                        <i class="fas fa-book" style="margin-right: 10px;"></i> SÁCH
                    </h4>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Nhập tên sách..." value="<?php echo htmlentities($searchKeyword); ?>">
                            <select name="method" class="form-control">
                                <option value="-1" <?php echo ($methodFilter == -1) ? 'selected' : ''; ?>>Tất cả</option>
                                <option value="1" <?php echo ($methodFilter == 1) ? 'selected' : ''; ?>>Online</option>
                                <option value="0" <?php echo ($methodFilter == 0) ? 'selected' : ''; ?>>Offline</option>
                            </select>
                            <select name="category" class="form-control">
                                <option value="-1">Tất cả thể loại</option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo htmlentities($category->id); ?>" <?php echo ($categoryFilter == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlentities($category->CategoryName); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <?php if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <div class="card" style="width: 23rem;">
                                <div class="card-img">
                                    <img src="../admin/assets/img/<?php echo htmlentities($result->Image); ?>" class="card-img-top" alt="<?php echo htmlentities($result->BookName); ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlentities($result->BookName); ?></h5>
                                    <p class="card-text">Hình thức: <?php if ($result->Method == 1) { ?>
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
                                <a href="?page=<?php echo $page; ?><?php echo $searchKeyword ? '&search=' . urlencode($searchKeyword) : ''; ?><?php echo '&method=' . $methodFilter; ?><?php echo '&category=' . $categoryFilter; ?>">
                                    <?php echo $page; ?>
                                </a>
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
