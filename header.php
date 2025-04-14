<?php
include_once 'db_connect.php';
include_once 'functions.php';
 
sec_session_start();
 
if (login_check($mysqli) == true) {
    ;
} else {
    header('Location: login.php');
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
    <title>Department Details</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bscallout.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>body{padding-top:100px;}.starter-template{padding:80px 15px;text-align:center;}.table-nonfluid {width: auto !important;}
	
	.edudocupload {
    width: 300px; /* Set your desired width here */
    margin-right: 10px; /* Space between file input and button */
    border: 1px solid #ccc; /* Border color */
    color: #333; /* Text color */
    border-radius: 5px; /* Rounded corners */
}

.btnsave {
    margin-left: 50px; /* Space between file input and button */
    background-color: #4CAF50; /* Green background */
    color: white; /* White text */
    border: none; /* Remove default border */
    padding: 8px 16px; /* Padding for button */
    cursor: pointer; /* Pointer cursor on hover */
    border-radius: 5px; /* Rounded corners */
}

.btnsave:hover {
    background-color: #45a049; /* Darker green on hover */
}

#examtable {
    border-collapse: collapse; /* Optional: for a cleaner look */
    width: 100%; /* Optional: make the table full width */
}

#examtable th,
#examtable td {
   padding: 20px 20px 20px 20px;
    border: 1px solid #ccc; /* Optional: adds a border to the cells */
}

#examtable th {
    background-color: #f2f2f2; /* Optional: header background color */
}
	
	
	</style>

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
                <a class="navbar-brand" href="#">Details</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                   <li class="active"><a href="./instructions.php"><strong>UDRF</strong></a></li>					
                    <li><a href="details/researchdetails.php"><strong>Research Details(300 Marks)</strong></a></li>                    
                     <li><a href="details/nepinitiatives.php">NEP Initiatives,TLAP</a></li>
                    <li><a href="details/deptgovprac.php">Dept Gov and Prac</a></li>
                    <li><a href="details/studsuppachieveprog.php">Stud Supp, Achiev, & Prog</a></li>
                    <li><a href="details/confworkshsem.php">Conf worksh</a></li>	
                    <li><a href="details/collaborations.php">Collaborations</a></li>						
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div><!--.nav-collapse -->
        </div>
    </nav>
