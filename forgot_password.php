<?php
include_once 'db_connect.php';
include_once 'functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = test_input($_POST['email']);
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $sql = "UPDATE regop SET reset_token = ?, reset_expiry = ? WHERE Email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $token, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $reset_link = "http://localhost/urdf/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message_body = "Please click the following link to reset your password: $reset_link";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brian4dev@gmail.com';
            $mail->Password = 'odeg igxn swzz kusr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('brian4dev@gmail.com', 'Mailer');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message_body;

            $mail->send();
            $message = 'Password reset email has been sent. Redirecting to login page...';
            $message_type = 'success';
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000);
                  </script>";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $message_type = 'danger';
        }
    } else {
        $message = 'Email address not found';
        $message_type = 'danger';
    }

    $stmt->close();
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Forgot Password</title>
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
                        <strong>Forgot Password</strong>
                    </div>
                    <div class="panel-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <form role="form" method="POST">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Email" name="email" type="email" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-lg btn-primary btn-block" value="Submit">
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