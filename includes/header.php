<div class="navbar navbar-inverse set-radius-zero">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">

                <img src="assets/img/logo.png" />
            </a>

        </div>
        <?php if($_SESSION['login'])
            {
        ?>
        <div class="right-div">
            <a href="logout.php" class="btn btn-danger pull-right">ĐĂNG XUẤT</a>
        </div>
        <?php }?>
    </div>
</div>
<!-- LOGO HEADER END-->
<?php if($_SESSION['login'])
{
?>
<section class="menu-section">
    <div class="container">
        <div class="row ">
            <div class="col-md-12">
                <div class="navbar-collapse collapse ">
                    <ul id="menu-top" class="nav navbar-nav navbar-right">
                        <li><a href="dashboard.php" class="menu-top-active">TRANG CHỦ</a></li>

                        <li><a href="booklist.php">SÁCH</a></li>

                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> TÀI KHOẢN <i
                                    class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="my-profile.php">Tài khoản
                                        của tôi</a></li>
                            </ul>
                        </li>
                        <li><a href="issued-books.php">Sách Đã Mượn - Trả</a></li>
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
                        <li><a href="signup.php">ĐĂNG KÝ</a></li>
                        <li><a href="index.php">ĐĂNG NHẬP</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<?php } ?>