<?php
ob_start(); // Start output buffering to prevent header issues
include_once 'db_connect.php';
include_once 'functions.php';
sec_session_start();

// Debug: Log session start
file_put_contents('debug.log', "Session started. Session ID: " . session_id() . "\n", FILE_APPEND);

// Check if already logged in
if (login_check($mysqli) === true) {
    file_put_contents('debug.log', "User already logged in. Redirecting to dashboard.\n", FILE_APPEND);
    header('Location: /details/dashboard.php');
    exit;
} else {
    file_put_contents('debug.log', "User not logged in. Proceeding to login form.\n", FILE_APPEND);
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sanitize input function
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process login
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['loginname'], $_POST['password'])) {
        $loginname = test_input($_POST['loginname']);
        $password = test_input($_POST['password']);
        
        // Debug: Log the login attempt
        file_put_contents('debug.log', "Login attempt - Username/Email: $loginname\n", FILE_APPEND);
        
        if (login($loginname, $password, $mysqli) === true) {
            file_put_contents('debug.log', "Login successful. Redirecting to dashboard.\n", FILE_APPEND);
            header('Location: urdf/details/dashboard.php');
            exit;
        } else {
            $error = "User ID or Password Incorrect.";
            file_put_contents('debug.log', "Login failed. Incorrect credentials.\n", FILE_APPEND);
        }
    } else {
        $error = "Invalid Request";
        file_put_contents('debug.log', "Invalid request. Missing loginname or password.\n", FILE_APPEND);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border-left: 4px solid #3498db;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
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

        .profile-img {
            display: block;
            margin: 0 auto 20px;
            max-width: 100px;
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
        .btn-secondary {
            background-color: #7f8c8d;
            border: none;
            padding: 10px;
            font-size: 0.95em;
            border-radius: 3px;
            transition: background-color 0.2s ease;
            width: 100%;
            color: white;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #6c7778;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>College Authorized Sign In</h2>
        <img class="profile-img" src="images/UoMLogo.jpg" alt="University Logo">
        <div class="error-alert" id="errorAlert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <form role="form" method="POST">
            <fieldset>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-user"></i>
                        </span>
                        <input class="form-control" placeholder="Email" name="loginname" type="text" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-lock"></i>
                        </span>
                        <input class="form-control" placeholder="Password" name="password" type="password" value="" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Sign In">
                </div>
                <div class="form-group">
                    <a href="forgot_password.php" class="btn btn-secondary">Forgot Password?</a>
                </div>
            </fieldset>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php ob_end_flush(); // Flush output buffer ?>