<?php
include_once 'db_connect.php';

sec_session_start();

if (login_check($mysqli) == true) {
    ;
} else {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
?>

<header>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><i class="fa fa-building"></i> Department Portal</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="../instructions.php"><i class="fa fa-book"></i> UDRF Guide</a></li>        
                    <li class="active"><a href="excelread.php"><i class="fa fa-table"></i> Excel Import</a></li>
                    <li><a href="dropdown.php"><i class="fa fa-list"></i> Department List</a></li>
                    <?php if ($role === 'admin'): ?>
                        <li><a href="../registration.php"><i class="fa fa-users"></i> User Management</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i> <?php echo htmlentities($_SESSION['username']); ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../change_password.php"><i class="fa fa-key"></i> Change Password</a></li>
                            <li class="divider"></li>
                            <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Add required CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>
    .navbar {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .navbar-brand {
        font-weight: 600;
    }
    .navbar-inverse {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }
    .navbar-inverse .navbar-nav > li > a {
        color: #ecf0f1;
    }
    .navbar-inverse .navbar-nav > .active > a,
    .navbar-inverse .navbar-nav > .active > a:hover {
        background-color: #34495e;
    }
    .dropdown-menu {
        border-radius: 3px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .dropdown-menu > li > a {
        padding: 8px 20px;
    }
    .dropdown-menu > li > a:hover {
        background-color: #f8f9fa;
    }
    .fa {
        margin-right: 5px;
    }
</style>