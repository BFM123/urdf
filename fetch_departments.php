<?php
include_once 'db_connect.php';

$school_id = isset($_GET['school_id']) ? intval($_GET['school_id']) : 0;

if ($school_id > 0) {

    if (!$mysqli) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($mysqli, 'SELECT collno, collname FROM department WHERE school_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $school_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $departments = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    echo json_encode($departments);

    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}
?>