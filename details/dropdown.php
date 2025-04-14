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
      <title>Department Details</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bscallout.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css"> 
	
	
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
                <a class="navbar-brand" href="#">Details</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                      <li ><a href="../instructions.php">UDRF</a></li>		
                    <li><a href="excelread.php"><strong>OPTION 1 (Excel read)</strong></a></li>                    
                    
                    <li class="active"><a href="dropdown.php">OPTION 2 (Dropdown)</a></li>
                   		
                </ul>
				 
                <ul class="nav navbar-nav navbar-right">                   
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </div><!--.nav-collapse -->
        </div>
    </nav>

    <div class="container">
            <?php
      
                        echo '<div class="bs-callout bs-callout-success hidden-print"><p class="lead"> <small>Currently Logged in as: </small><strong>'.htmlentities($_SESSION['username']) .'</strong>				
						
						</p>';
 
            echo '<p><small>Wrong User? Click to </small><a href="../logout.php">Log out</a>.</p></div>';

        ?>
		<form class="form-inline" method="POST" role="form" action="#">
		  <div class="form-group">
			<label for="department">Department :</label>
			<select class="select2 form-control" name="department" id="department" required>
   <option disabled selected value>Select Department</option>
   <?php 
     $sql = $mysqli->query("SELECT DISTINCT collname, allDetails.collno as dept_no FROM allDetails INNER JOIN regop ON allDetails.collno = regop.collno ORDER BY allDetails.collno");
     while($row = mysqli_fetch_array($sql)) {
       echo '<option value="'.$row['dept_no'].'?'.$row['collname'].'">' . $row['collname'] . '</option>';
     }
   ?>
</select>

		  </div>
		 <div style="color: blue; text-align: left; margin-top: 15px;">
            <button type="submit" class="btn btn-primary">Get Details</button>
          </div>
		  
		
		</form>
		<br/>
		<hr/>
		<?php
	
		if(isset($_POST['department']))
		{
			$department = explode('?', $_POST['department']);
			$deptno = $department[0]; // department number
			$deptname = $department[1]; // department name

			echo '<p>You have selected Department: <strong>'.$deptname.'</strong> (ID: '.$deptno.')</p>';

			echo'<form role="form" method="POST" action="updatemarks.php">

			<table class="table table-bordered">
			<thead>
				<tr>
				<th>Particulars</th>
				<th>Srno</th>
				<th>Suported Documents</th>

				<th>Commitee</th>
				
				</tr>
			<thead>
			<tbody>';

			$i=0;
			$sql = $mysqli->query("SELECT * FROM allDetails WHERE collno='$deptno' ORDER BY particulars, srno");
			$sqlparticular = $mysqli->query("SELECT DISTINCT particulars, COUNT(*) as total FROM allDetails WHERE collno='$deptno' AND particulars != '' GROUP BY particulars");

			$particulars_rowspan = [];
			while ($row = mysqli_fetch_assoc($sqlparticular)) {
				$particulars_rowspan[$row['particulars']] = $row['total'];
			}

			$current_particular = null;
			$current_commitee = null;
			
			
			while ($row = mysqli_fetch_assoc($sql)) {
				echo '<tr>';
				// Check if we need to display the particulars cell
				if ($row['particulars'] !== $current_particular) {
					$current_particular = $row['particulars'];
					$rowspan = $particulars_rowspan[$current_particular];
					echo '<td rowspan="' . $rowspan . '" style="text-align: center; vertical-align: middle;"><b>' . $row['particulars'] . '</b></td>';
				}
				echo '<td class="text-center">' . $row['srno'] . '</td>';
				echo '<td>';
				
				$partisql = mysqli_query($mysqli,'select particulars from allParticulars where particularname = "'.$row['particulars'].'" and srno="'.$row['srno'].'";');		

					
				$resf = mysqli_fetch_assoc($partisql);
				$nameparticular = $resf['particulars'];	
				
				$filename = basename($row['path']);
				
				if ($row['srno'] == '0') {
    $addpath = "../";
} else {
    $addpath = '';
}

echo $nameparticular . '<a href="' . $addpath . $row['path'] . '" target="_blank">
        <i class="fas fa-eye" style="margin-left: 10px; margin-right: 5px;"></i> VIEW
      </a>';
echo '</td>';
				
					// Check if we need to display the particulars cell
				if ($row['particulars'] !== $current_commitee) {
					$current_commitee = $row['particulars'];
					$rowspan = $particulars_rowspan[$current_commitee];
					echo '<td rowspan="' . $rowspan . '" style="text-align: center; vertical-align: middle;"><input type="text" id="committee" name="committee"/> </td>';
					
					
				}
				
				
				
				
				echo '</tr>';
			}
		echo'<tbody>
		</table>';
		
		}		
	?>
<br/>
<br/>
<br/>
    </div>
   

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script type="text/javascript" src="assets/js/select2.js"></script>
		
<!-- App js -->
<script src="assets/js/app.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {

	$('.select2').select2({
	width:'200%'
	});

	});
</script>
</body>
</html>
