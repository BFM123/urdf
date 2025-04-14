<?php
include_once 'db_connect.php';
include_once 'functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    ;
} else {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>College Internal Marks</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bscallout.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <style>body{padding-top:60px;}.starter-template{padding:40px 15px;text-align:center;}.table-nonfluid {width: auto !important;}</style>

    <!--[if IE]>
        <script src="https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">College Internal Marks</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li ><a href="../instructions.php">Instructions</a></li>
                    <li><a href="entermarks.php">Enter Marks</a></li>
                    <li><a href="enteredmarks.php">Entered Marks</a></li>
                    <li><a href="genreport.php" target="_blank">Generate Report</a></li>
		    
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php 
    echo '<li><a>Coll No: '.htmlentities($_SESSION['collno']).'</a></li>';
                    ?>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </div><!--.nav-collapse -->
        </div>
    </nav>
