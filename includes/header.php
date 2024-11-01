<div class="navbar navbar-inverse set-radius-zero">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
        <div class="navbar-header">
            <a class="navbar-brand">
                <img src="assets/img/logo.png" />
            </a>
        </div>
        <?php if ($_SESSION['login']) { ?>
            <div class="right-div">
                <a href="logout.php" class="btn btn-primary">ĐĂNG XUẤT</a>
            </div>
        <?php } ?>
    </div>
</div>

<!-- LOGO HEADER END-->
<?php if ($_SESSION['login']) {
?>
    <section class="menu-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="dashboard.php" class="menu-top-active">TRANG CHỦ</a></li>

                            <li><a href="booklist.php">SÁCH</a></li>

                            <li><a href="issued-books.php">Sách Đã Mượn - Trả</a></li>

                            <li><a href="my-profile.php">Tài khoản của tôi</a></li>

                            <!-- <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> TÀI KHOẢN <i
                                        class="fa fa-angle-down"></i></a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="my-profile.php">Tài khoản
                                            của tôi</a></li>
                                </ul>
                            </li> -->
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php } else { ?>
    <section class="menu-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="adminlogin.php">ĐĂNG NHẬP ADMIN</a></li>
                            <li><a href="index.php">ĐĂNG NHẬP CLIENT</a></li>
                            <li><a href="signup.php">ĐĂNG KÝ</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php } ?>

<script>
    window.addEventListener("scroll", function() {
        var menuSection = document.querySelector(".menu-section");
        if (window.scrollY > menuSection.offsetTop) {
            menuSection.classList.add("sticky");
        } else {
            menuSection.classList.remove("sticky");
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Lấy URL hiện tại
        var currentUrl = window.location.href;

        // Lấy tất cả các liên kết trong menu
        var menuItems = document.querySelectorAll("#menu-top > li > a");

        // Duyệt qua các liên kết
        menuItems.forEach(function(item) {
            // Nếu href của liên kết trùng với URL hiện tại
            if (item.href === currentUrl) {
                // Thêm class "menu-top-active" vào liên kết cha
                item.classList.add("menu-top-active");
            } else {
                // Xóa class "menu-top-active" nếu không trùng
                item.classList.remove("menu-top-active");
            }
        });

        // Kiểm tra nếu đang ở trang "my-profile.php"
        if (currentUrl.includes("my-profile.php")) {
            // Xóa class "menu-top-active" khỏi "TÀI KHOẢN"
            var accountMenuItem = document.querySelector("#menu-top .dropdown-toggle");
            accountMenuItem.classList.add("menu-top-active");
        }
    });
</script>