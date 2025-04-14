<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

ob_start();
include_once 'psl-config.php';

function sec_session_start() {
$session_name = 'epdsintth';   // Set a custom session name
$secure = FALSE;
// This stops JavaScript being able to access the session id.
$httponly = true;
// Forces sessions to only use cookies.
if (ini_set('session.use_only_cookies', 1) === FALSE) {
header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
exit();
}
// Gets current cookies params.
$cookieParams = session_get_cookie_params();
session_set_cookie_params($cookieParams["lifetime"],
 $cookieParams["path"],
 $cookieParams["domain"],
 $secure,
 $httponly);
// Sets the session name to the one set above.
session_name($session_name);
session_start();            // Start the PHP session 
session_regenerate_id();    // regenerated the session, delete the old one. 
}

/* Clean/remove code this after verification
function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    $sql = "SELECT `Email`, `Password`, `Collno`, `Collname`, `force_password_change` FROM `regop` WHERE `Email`= ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $stmt->store_result();

    // get variables from result.
    $stmt->bind_result($email, $db_password, $collno, $collname, $force_password_change);
    $stmt->fetch();

    // hash the password with the unique salt.
    if ($stmt->num_rows == 1) {
        // If the user exists we check if the account is locked
        // from too many login attempts 

        // Check if the password in the database matches
        // the password the user submitted.
        if (hash_equals($db_password, crypt($password, $db_password))) {
            // Password is correct!
            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            // XSS protection as we might print this value
            $user_id = preg_replace("/[^0-9]+/", "", $email);

            // Set session variables
            $_SESSION['email'] = $email;
            $_SESSION['collno'] = $collno;
            $_SESSION['collname'] = $collname;
            $_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);

            // Check if the user needs to change their password
            
            if ($force_password_change) {

                // Redirect to password change page
                header('Location: change_password.php');
                exit();
            }

            // Login successful
            return true;
        } else {
            // Password is not correct
            return false;
        }
    } else {
        // No user exists
        return false;
    }
}
*/

//new, verify this works accordingly then delete the duplicate
function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    $sql = "SELECT `Email`, `Password`, `Collno`, `Collname`, `force_password_change`, `is_verified`, `role` FROM `regop` WHERE `Email`= ?";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $stmt->store_result();

    // get variables from result.
    $stmt->bind_result($email, $db_password, $collno, $collname, $force_password_change, $is_verified, $role);
    $stmt->fetch();

    // hash the password with the unique salt.
    if ($stmt->num_rows == 1) {
        // Check if the account is verified
        if (!$is_verified) {
            echo "Your account is not verified. Please check your email for the verification link.";
            return false;
        }

        // Check if the password in the database matches
        // the password the user submitted.
        if (hash_equals($db_password, crypt($password, $db_password))) {
            // Password is correct!
            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            // XSS protection as we might print this value
            $user_id = preg_replace("/[^0-9]+/", "", $email);

            // Set session variables
            $_SESSION['email'] = $email;
            $_SESSION['collno'] = $collno;
            $_SESSION['collname'] = $collname;
            $_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);
            $_SESSION['username'] = $email;
            $_SESSION['role'] = $role;
            
            // Check if the user needs to change their password
            if ($force_password_change) {
                // Redirect to password change page
                header('Location: change_password.php');
                exit();
            }

            // Login successful
            return true;
        } else {
            // Password is not correct
            return false;
        }
    } else {
        // No user exists
        return false;
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['username'],$_SESSION['login_string'])) {
 
        //$user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT Password 
                                      FROM regop 
                                      WHERE Email = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            
            $stmt->bind_param('s', $username);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
  
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    
                    return false;
                }
            } else {
                // Not logged in
                
                return false;
            }
        } else {
            // Not logged in
            
            return false;
        }
    } else {
        // Not logged in
        
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function registration($email, $password, $school, $departments, $conn) {

    // Check if username or email already exists
    $check_query = "SELECT * FROM regop WHERE Email = '$email'";
        
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        $message = '<div class="error">Username or email already exists!</div>';
    } else {
     
        $hash_cost_factor = '10';
        $salt = sprintf('$2a$%02d$', $hash_cost_factor) . strtr(base64_encode(random_bytes(16)), '+', '.'); 
        $hash = crypt($password, $salt);
    
        // Generate a verification token
        $verification_token = bin2hex(random_bytes(16));

        // Hash the password
        $password_hashed = password_hash($password, PASSWORD_BCRYPT); // the old hashing method has been implemented instead of this
       
        // Set the force_password_change variable
        $force_password_change = TRUE;
        $status = 'active';

         // Flatten the departments array
         $flat_departments = array_merge(...$departments);

         // Convert departments array to string
         $departments_str = implode(',', $flat_departments);

        // Insert the new user into the database
        $sql = "INSERT INTO `regop` (`Email`, `Password`, `PassInput`, `school`, `departments`, `force_password_change`, `verification_token`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $email, $hash, $password, $school, $departments_str, $force_password_change, $verification_token, $status);
        $stmt->execute();
        $stmt->close();

        // Send verification email using PHPMailer
        $verification_link = "http://localhost/urdf/verify.php?token=$verification_token";
        $subject = "Verify Your Email Address";
        $message = "Please click the following link to verify your email address: $verification_link";

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'brian4dev@gmail.com'; // SMTP username
            $mail->Password = 'odeg igxn swzz kusr'; // Use the App Password generated from Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('brian4dev@gmail.com', 'Mailer');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            echo 'Verification email has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

function fetch_users($conn) {
    $sql = "SELECT id, Email, collno FROM regop";
    $result = $conn->query($sql);
    return $result;
}

function fetch_data($conn, $table_name, $field_values = "*", $condition = "", $condition_value = "") {
    $sql = "SELECT $field_values FROM $table_name";
    if ($condition != "") {
        $sql .= " WHERE $condition = '$condition_value' AND status = 'active'";
    } else {
        $sql .= " WHERE status = 'active'";
    }

    $result = $conn->query($sql);
    return $result;
}

function fetch_values($conn, $table_name, $field_values = "*", $condition = "", $condition_value = "") {
    $sql = "SELECT $field_values FROM $table_name";
    if ($condition != "") {
        $sql .= " WHERE $condition = '$condition_value' AND status = 'active'";
    } else {
        $sql .= " WHERE status = 'active'";
    }

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row[$field_values];
    } else {
        return null;
    }
}

function department($conn)
{
    $sql = "SELECT * FROM department WHERE status = 'active'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch all rows as an associative array
        $departments = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $departments = [];
    }

    return $departments;
}
ob_end_flush();
?>