<?php
include_once 'psl-config.php'; // Assuming this has DB config if needed

function sec_session_start() {
    $session_name = 'epdsintth';
    $secure = false; // Set to true if using HTTPS
    $httponly = true;

    // Force cookies only
    if (ini_set('session.use_only_cookies', 1) === false) {
        file_put_contents('debug.log', "Failed to set session.use_only_cookies\n", FILE_APPEND);
        header("Location: ../error.php?err=Could not initiate a safe session");
        exit;
    }

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly
    );

    session_name($session_name);
    if (!session_start()) {
        file_put_contents('debug.log', "Session start failed\n", FILE_APPEND);
        exit("Session failed to start");
    }
    session_regenerate_id(true); // Regenerate ID to prevent fixation
    file_put_contents('debug.log', "Session started: " . session_id() . "\n", FILE_APPEND);
}

function login($loginname, $password, $mysqli) {
    $sql = "SELECT Email, username, Password, is_admin FROM regop WHERE Email = ? OR username = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        file_put_contents('debug.log', "Prepare failed: " . $mysqli->error . "\n", FILE_APPEND);
        return false;
    }

    $stmt->bind_param('ss', $loginname, $loginname);
    if (!$stmt->execute()) {
        file_put_contents('debug.log', "Execute failed: " . $stmt->error . "\n", FILE_APPEND);
        $stmt->close();
        return false;
    }

    $stmt->store_result();
    file_put_contents('debug.log', "Rows found: " . $stmt->num_rows . "\n", FILE_APPEND);

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($email, $username, $db_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;
            $_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);
            $_SESSION['is_admin'] = (bool)$is_admin;

            // Log successful login
            $timestamp = date('Y-m-d H:i:s', time() + 19800); // UTC+5:30
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_sql = "INSERT INTO loginattempts (email, time, IP, login) VALUES (?, ?, ?, 1)";
            $log_stmt = $mysqli->prepare($log_sql);
            if ($log_stmt) {
                $log_stmt->bind_param('sss', $email, $timestamp, $ip);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                file_put_contents('debug.log', "Log prepare failed: " . $mysqli->error . "\n", FILE_APPEND);
            }

            file_put_contents('debug.log', "Login success for $username\n", FILE_APPEND);
            $stmt->close();
            return true;
        } else {
            // Log failed attempt
            $timestamp = date('Y-m-d H:i:s', time() + 19800);
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_sql = "INSERT INTO loginattempts (email, time, IP, login) VALUES (?, ?, ?, 0)";
            $log_stmt = $mysqli->prepare($log_sql);
            if ($log_stmt) {
                $log_stmt->bind_param('sss', $email, $timestamp, $ip);
                $log_stmt->execute();
                $log_stmt->close();
            }

            file_put_contents('debug.log', "Password mismatch for $loginname\n", FILE_APPEND);
            $stmt->close();
            return false;
        }
    } else {
        file_put_contents('debug.log', "No user found for $loginname\n", FILE_APPEND);
        $stmt->close();
        return false;
    }
}

function login_check($mysqli) {
    if (isset($_SESSION['username'], $_SESSION['login_string'])) {
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT Password FROM regop WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);

                // Debug: Log the login check
                file_put_contents('debug.log', "Login check - Username: $username, Login String: $login_string, Generated String: $login_check\n", FILE_APPEND);

                if (hash_equals($login_check, $login_string)) {
                    return true;
                } else {
                    file_put_contents('debug.log', "Login string mismatch for user: $username\n", FILE_APPEND);
                    return false;
                }
            } else {
                file_put_contents('debug.log', "No user found for username: $username\n", FILE_APPEND);
                return false;
            }
            $stmt->close();
        } else {
            file_put_contents('debug.log', "Failed to prepare statement for login check\n", FILE_APPEND);
            return false;
        }
    } else {
        file_put_contents('debug.log', "Session variables not set for login check\n", FILE_APPEND);
        return false;
    }
}

function esc_url($url) {
    if (empty($url)) {
        return $url;
    }
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = str_replace($strip, '', $url);
    $url = str_replace(';//', '://', $url);
    if ($url[0] !== '/') {
        return '';
    }
    return $url;
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
?>