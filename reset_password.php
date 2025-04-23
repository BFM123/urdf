<?php
require 'vendor/autoload.php';
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

$error = '';
$message = '';
$token_valid = false;

// Verify token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and is not expired
    $stmt = $mysqli->prepare("SELECT Email FROM regop WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token_valid = true;
    } else {
        $error = "Invalid or expired reset token. Please request a new password reset.";
        //$error = $result;
    }
    $stmt->close();
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['token'])) {
    $new_password = $_POST['password'];
    $token = $_POST['token'];
    
    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Hash the new password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $stmt = $mysqli->prepare("UPDATE regop SET Password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->bind_param('ss', $password_hash, $token);
        $stmt->execute();
        
        if ($stmt->affected_rows === 1) {
            $message = "Password successfully reset. You can now login with your new password.";
            // Redirect to login page after 3 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000);
            </script>";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Department Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <style>
        body { padding-top: 70px; background-color: #f8f9fb; }
        .reset-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <h2 class="text-center">Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($token_valid): ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                               title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
                
                <script>
                    // Password confirmation validation
                    var password = document.getElementById("password");
                    var confirm_password = document.getElementById("confirm_password");

                    function validatePassword(){
                        if(password.value != confirm_password.value) {
                            confirm_password.setCustomValidity("Passwords Don't Match");
                        } else {
                            confirm_password.setCustomValidity('');
                        }
                    }

                    password.onchange = validatePassword;
                    confirm_password.onkeyup = validatePassword;
                </script>
            <?php else: ?>
                <p class="text-center">
                    <a href="forgot_password.php" class="btn btn-link">Request New Password Reset</a>
                </p>
            <?php endif; ?>
            
            <p class="text-center">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>
</body>
</html>