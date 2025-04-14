<?php
include_once 'db_connect.php';
include_once 'functions.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $sql = "UPDATE `regop` SET `is_verified` = TRUE, `verification_token` = NULL WHERE `verification_token` = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();

    if ($stmt->affected_rows == 1) {
        echo "Your email has been verified. You can now log in.";
    } else {
        echo "Invalid verification token.";
    }

    $stmt->close();
} else {
    echo "No verification token provided.";
}
?>