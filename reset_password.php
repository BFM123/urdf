<?php
include_once 'db_connect.php';
include_once 'functions.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = test_input($_POST['token']);
    $new_password = test_input($_POST['new_password']);
    $confirm_password = test_input($_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        $sql = "SELECT reset_expiry FROM regop WHERE reset_token = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->bind_result($reset_expiry);
        $stmt->fetch();
        $stmt->close();

        if (strtotime($reset_expiry) > time()) {
            $hash_cost_factor = '10';
            $salt = sprintf('$2a$%02d$', $hash_cost_factor) . strtr(base64_encode(random_bytes(16)), '+', '.'); 
            $hash = crypt($new_password, $salt);

            $sql = "UPDATE regop SET Password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ss', $hash, $token);
            $stmt->execute();
            $stmt->close();

            $message = 'Password has been reset successfully. Redirecting to login page...';
            $message_type = 'success';
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000);
                  </script>";
        } else {
            $message = 'Reset token has expired';
            $message_type = 'danger';
        }
    } else {
        $message = 'Passwords do not match';
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Reset Password</title>
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
                        <strong>Reset Password</strong>
                    </div>
                    <div class="panel-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <form role="form" method="POST">
                            <fieldset>
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                                <div class="form-group">
                                    <input class="form-control" placeholder="New Password" name="new_password" type="password" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Confirm Password" name="confirm_password" type="password" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-lg btn-primary btn-block" value="Reset Password">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>