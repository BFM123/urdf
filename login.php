<?php
include_once 'db_connect.php';
include_once 'functions.php';
sec_session_start();
if (login_check($mysqli) == true) {
    //header('Location: details/excelread.php'); work with the TEST for now
    header('Location: details/TEST.php');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>Login for Examination Form Receipts</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-table.css">
        <script src="js/jquery-2.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrap-table.js"></script>
    </head>
    <body>
        <div class="container-fluid" style="margin-top:40px">
            <div class="row">
                <div class="col-sm-6 col-md-4 col-md-offset-4">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center text-capitalize">
                            <strong>College Authorized Person Sign in to continue</strong>
                        </div>
                        <div class="panel-body">
                            <form role="form" method="POST">
                                <fieldset>
                                    <div class="row">
                                        <div class="center-block">
                                            <img class="profile-img center-block"
                                                 src="images/UoMLogo.jpg" height="35%" width = "35%" alt="">
                                        </div>
                                    </div><br/>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="glyphicon glyphicon-user"></i>
                                                    </span> 
                                                    <input class="form-control" placeholder="Email" name="loginname" type="text" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="glyphicon glyphicon-lock"></i>
                                                    </span>
                                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                                </div>
                                            </div>

                                          
                                            <div class="form-group text-center">
                                                <a href="forgot_password.php">Forgot Password?</a>
                                            </div>

                                            <?php
                                            
                                            header("Content-Type: text/html;charset=UTF-8");

                                            /*
                                            function test_input($data) {
                                                $data = trim($data);
                                                $data = stripslashes($data);
                                                $data = htmlspecialchars($data);
                                                return $data;
                                            }
                                            */
 // Our custom secure way of starting a PHP session.
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
if (isset($_POST['loginname'], $_POST['password'])) {
    $username = test_input($_POST['loginname']);
    $password = test_input($_POST['password']);
    if (login($username, $password, $mysqli) == true) {
        // Login success 
        //header('Location: details/excelread.php');
        header('Location: details/TEST.php');
    } else {
        // Login failed 
        echo "<div id=\"showError\" class=\"form-group\"><div  class=\"alert alert-danger\" role=\"alert\">
                                                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                                                <span class=\"sr-only\">Error:</span>
                                                User ID or Password Incorrect.
                                                </div></div>";
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}
}
                                            /*$DBServer = '208.91.198.197:3306';
                                            $DBUser = 'dbconvocation';
                                            $DBPass = 'C0nv0CAT10N';
                                            $DBName = 'convocation';
                                            $conn = new mysqli('localhost', 'root', '', 'examform');*/
/*
                                            if ($conn->connect_error) {
                                                trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
                                            }
                                            mysqli_set_charset($conn, "utf8");
                                            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                                                $username = test_input($_POST['loginname']);
                                                $password = test_input($_POST['password']);

                                                $sql = 'SELECT `Password` FROM `regop` WHERE `Email`= ? ';
                                                $stmt = $conn->prepare($sql);
                                                if ($stmt === false) {
                                                    trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
                                                }
                                                $stmt->bind_param('s', $username);
                                                $stmt->execute();
                                                $meta = $stmt->result_metadata();

                                                while ($field = $meta->fetch_field()) {
                                                    $parameters[] = &$row[$field->name];
                                                }

                                                call_user_func_array(array($stmt, 'bind_result'), $parameters);

                                                while ($stmt->fetch()) {
                                                    $i = 0;
                                                    foreach ($row as $key => $val) {
                                                        $x[$key] = $val;
                                                        $x[$i++] = $val;
                                                    }
                                                    $sd[] = $x;
                                                    
                                                }
                                                
                                                $stmt->close();
                                                $conn->close();
                                                    
                                                if (isset($sd) && hash_equals($sd[0][0], crypt($password, $sd[0][0]))) {
                                                echo $password."    ".$sd[0][0]."    ".hash_equals($sd[0][0], crypt($password, $sd[0][0]))."    ".($sd[0][0] === crypt($password, $sd[0][0]));
                                                header("Location: ColgReport.php");
                                                die();
                                                } else {
                                                echo "<div id=\"showError\" class=\"form-group\"><div  class=\"alert alert-danger\" role=\"alert\">
                                                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                                                <span class=\"sr-only\">Error:</span>
                                                User ID or Password Incorrect.
                                                </div></div>";
                                            }
                                            
                                                }
 */

                                            ?>
                                            
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- STAT UCC -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//muexam.mu.ac.in/statucc/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//muexam.mu.ac.in/statucc/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
<!-- End STAT UCC Code -->
    </body>
</html>