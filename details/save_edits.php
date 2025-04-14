<?php
include_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $committees = $_POST['committee'] ?? [];
    $data1 = $_POST['data1'] ?? [];
    $data2 = $_POST['data2'] ?? [];
    $data3 = $_POST['data3'] ?? [];

    if (empty($committees) || count($committees) !== count($data1) || count($data2) !== count($data3)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit();
    }

    $stmt = $mysqli->prepare("
        INSERT INTO your_table (committee, data1, data2, data3) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        data1 = VALUES(data1), 
        data2 = VALUES(data2), 
        data3 = VALUES(data3)
    ");

    foreach ($committees as $index => $committee) {
        $stmt->bind_param(
            'ssss',
            $committee,
            $data1[$index],
            $data2[$index],
            $data3[$index]
        );

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed at index ' . $index]);
            exit();
        }
    }

    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
