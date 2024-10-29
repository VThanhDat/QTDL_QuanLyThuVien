<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {
    if (isset($_GET['deleteid'])) {
        $id = intval($_GET['deleteid']); // Lấy ID của bản ghi cần xóa
        // Thực hiện câu lệnh SQL để update ReturnStatus và BorrowStatus về NULL
        $sql = "UPDATE ctmuontra SET BorrowStatus = NULL WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
    
        $_SESSION['delmsg'] = "Yêu cầu đã được xóa thành công"; // Thông báo xóa thành công
        header('location:issued-books.php'); // Chuyển hướng về trang danh sách sách
        exit();
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
    <title>Hệ thống quản lý thư viện | Sách đã mượn</title>
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
                    <h4 class="header-line">Sách đã mượn</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Sách đã mượn - <span style="color:red;">*Khi đã được duyệt thì bạn cần đến thư viện để nhận sách*</span>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên Sách</th>
                                            <th>Mã ISBN</th>
                                            <th>Ngày mượn</th>
                                            <th>Ngày trả</th>
                                            <th>Số lượng</th>
                                            <th>Hình thức</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sid = $_SESSION['stdid'];
                                        $sql = "SELECT ctmuontra.id, sach.BookName AS ten_sach, 
                                        sach.ISBNNumber AS ma_isbn, 
                                        ctmuontra.IssuesDate AS ngay_muon, 
                                        ctmuontra.ReturnDate AS ngay_tra,
                                        ctmuontra.QuantityBorrow,ctmuontra.Method,
                                        ctmuontra.BorrowStatus
                                        FROM ctmuontra JOIN sach ON ctmuontra.BookId = sach.id WHERE ctmuontra.ReaderId =:sid";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        foreach ($results as $result) { ?>
                                            <tr class="odd gradeX">
                                                <td class="center"><?php echo htmlentities($cnt); ?></td>
                                                <td class="center"><?php echo htmlentities($result->ten_sach); ?></td>
                                                <td class="center"><?php echo htmlentities($result->ma_isbn); ?></td>
                                                <td class="center"><?php echo htmlentities($result->ngay_muon); ?></td>
                                                <td class="center">
                                                    <?php
                                                    if ($result->ngay_tra == "") {
                                                        echo "<span style='color:red'>Chưa Trả</span>";
                                                    } else {
                                                        echo htmlentities($result->ngay_tra);
                                                    }
                                                    ?>
                                                </td>
                                                <td class="center"><?php echo htmlentities($result->QuantityBorrow); ?></td>
                                                <td class="center"><?php if ($result->Method == 1) { ?>
                                                        <a href="#" class="btn btn-success btn-xs">Online</a>
                                                    <?php } else { ?>
                                                        <a href="#" class="btn btn-danger btn-xs">Offline</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="center">
                                                    <?php
                                                    // Kiểm tra BorrowStatus và hiển thị trạng thái tương ứng
                                                    if ($result->BorrowStatus == '0') {
                                                        // Nút màu vàng cho "Chưa duyệt" và thêm nút "Xóa"
                                                        echo '<a href="#" class="btn btn-warning btn-xs">Chưa duyệt</a>';
                                                        echo ' <a href="issued-books.php?deleteid=' . htmlentities($result->id) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Bạn có chắc chắn muốn xóa yêu cầu này không?\');">Xóa</a>';
                                                    } elseif ($result->BorrowStatus == '1') {
                                                        if ($result->Method == 1) {
                                                            echo '<a href="#" class="btn btn-success btn-xs">Đã duyệt</a> ';
                                                        } else {
                                                            echo '<a href="#" class="btn btn-success btn-xs">Đã duyệt</a>'; // Nút màu xanh cho "Đã duyệt"
                                                        }
                                                    } elseif ($result->BorrowStatus == '2') {
                                                        echo '<a href="#" class="btn btn-info btn-xs">Đã trả</a>'; // Nút màu xanh da trời cho "Đã trả"
                                                    } elseif ($result->BorrowStatus == NULL) {
                                                        echo '<a href="#" class="btn btn-danger btn-xs">Từ chối</a>'; // Nút màu đỏ cho "Từ chối"
                                                    } else {
                                                        echo '<span class="text-muted">Unknown Status</span>'; // Hiển thị thông báo trạng thái không xác định
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php $cnt = $cnt + 1;
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