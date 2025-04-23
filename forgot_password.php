<?php
require 'vendor/autoload.php'; //new
include_once 'db_connect.php';
include_once 'functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

sec_session_start();

// Sanitize input function
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = test_input($_POST['email']);
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        //$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
        
        // Update database with reset token
        $stmt = $mysqli->prepare("UPDATE regop SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE Email = ?");
        $stmt->bind_param('ss', $token, $email);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $mail = new PHPMailer(true);
            
            try {
                // Server settings with debugging enabled
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output
                $mail->Debugoutput = 'error_log';       // Log to error_log
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'brian4dev@gmail.com';
                $mail->Password = 'odeg igxn swzz kusr';
                $mail->SMTPSecure = 'ssl';              // Use SSL instead of ENCRYPTION_SMTPS
                $mail->Port = 465;
                
                // Optional timeout settings
                $mail->Timeout = 30;                    // Set timeout to 30 seconds
                $mail->SMTPKeepAlive = true;           // Keep SMTP connection alive
                    
                // Recipients
                $mail->setFrom('brian4dev@gmail.com', 'Password Reset');
                $mail->addAddress($email);
        
                
                // Content
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . 
                             dirname($_SERVER['PHP_SELF']) . 
                             "/reset_password.php?token=" . $token;
                
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "
                    <h2>Password Reset Request</h2>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='{$reset_link}'>{$reset_link}</a></p>
                    <p>This link will expire in 1 hour.</p>
                ";
                
                $mail->send();
                $message = "Reset instructions sent to your email.";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000);
                  </script>";
            } catch (Exception $e) {
                $error = "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Department Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 25px;
            background-color: #f8f9fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .forgot-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border-left: 4px solid #3498db;
            width: 100%;
            max-width: 400px;
        }

        .forgot-container h2 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
            text-transform: capitalize;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .input-group {
            width: 100%;
        }

        .input-group-addon {
            background-color: #3498db;
            color: white;
            border: 1px solid #2980b9;
            border-right: none;
            border-radius: 3px 0 0 3px;
        }

        .form-control {
            padding: 10px;
            border: 1px solid #d6dce0;
            border-radius: 0 3px 3px 0;
            font-size: 0.95em;
            box-shadow: none;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 10px;
            font-size: 0.95em;
            border-radius: 3px;
            transition: background-color 0.2s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .error-alert {
            background-color: #fff;
            border-left: 4px solid #e74c3c;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            color: #e74c3c;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }

        .success-alert {
            background-color: #fff;
            border-left: 4px solid #2ecc71;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            color: #2ecc71;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: <?php echo $message ? 'block' : 'none'; ?>;
        }

        .navbar-inverse {
            background-color: #2c3e50;
            border: none;
            border-radius: 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 500;
            letter-spacing: 0.5px;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Department Details</a>
            </div>
        </div>
    </nav>

    <div class="forgot-container">
        <h2>Forgot Password</h2>
        <div class="error-alert" id="errorAlert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <div class="success-alert" id="successAlert">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <form role="form" method="POST">
            <fieldset>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-envelope"></i>
                        </span>
                        <input class="form-control" placeholder="Registered Email" name="email" type="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Reset Password">
                </div>
                <div class="form-group">
                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                </div>
            </fieldset>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>