<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

/*
if (login_check($mysqli) == false) {

    header('Location: login.php');
    exit();
}
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Hash the new password
    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the password in the database and reset the force_password_change flag
    $sql = "UPDATE `regop` SET `Password` = ?, `force_password_change` = FALSE WHERE `Email` = ?";
    //echo $sql; die();

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $new_password_hashed, $email);
    $stmt->execute();
    $stmt->close();

    // Redirect to the main page
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: #ff0000;
            margin-bottom: 10px;
        }
        .success {
            color: #008000;
            margin-bottom: 10px;
        }
        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .strength-meter {
            height: 10px;
            background-color: #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }
        .strength-meter div {
            height: 100%;
            border-radius: 5px;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <h1>Change Password</h1>
    <form action="change_password.php" method="post" onsubmit="return validateForm()">
        <label for="new_password">New Password:</label>

        <div class="form-group">
            <label for="new_password"> New Password:</label>
            <input type="password" id="password" name="new_password" required onkeyup="checkPasswordStrength()">
            <div class="strength-meter">
                <div id="strength-bar"></div>
            </div>
            <div class="password-requirements">
                Password must contain:
                <ul>
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character</li>
                </ul>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit">Change Password</button>
    </form>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strength-bar');
            let strength = 0;

            if (password.length >= 8) strength += 20;
            if (password.match(/[A-Z]/)) strength += 20;
            if (password.match(/[a-z]/)) strength += 20;
            if (password.match(/[0-9]/)) strength += 20;
            if (password.match(/[^A-Za-z0-9]/)) strength += 20;

            strengthBar.style.width = strength + '%';
            
            if (strength <= 40) {
                strengthBar.style.backgroundColor = '#ff4d4d';
            } else if (strength <= 80) {
                strengthBar.style.backgroundColor = '#ffd700';
            } else {
                strengthBar.style.backgroundColor = '#4CAF50';
            }
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>

</body>
</html>