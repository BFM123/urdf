<?php
include_once 'db_connect.php';
include_once 'functions.php';
require_once 'vendor/autoload.php'; // Make sure this path is correct

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

sec_session_start();

if (login_check($mysqli) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $committeeData = $_POST['committee'];

        // Load the existing Excel file
        $filePath = 'C:/wamp64/www/urdf/Best Dept MU Proforma - Modified 23rd September 2024.xls'; // Replace with your Excel file's path
        $spreadsheet = IOFactory::load($filePath);

        // Update the Excel file with the committee data
        foreach ($committeeData as $sheetIndex => $rows) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            foreach ($rows as $rowIndex => $committee) {
                // Assuming the "Committee" column is the second column (D)
                $cellCoordinate = 'D' . ($rowIndex + 3);
                $sheet->setCellValue($cellCoordinate, $committee);
            }
        }

        // Save the updated Excel file
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $updatedFilePath = 'C:/wamp64/www/urdf/Updated_Best_Dept_MU_Proforma.xls'; // Path to save the updated file
        $writer->save($updatedFilePath);

        // Send the updated file to the browser for download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Updated_Best_Dept_MU_Proforma.xls"');
        header('Cache-Control: max-age=0');
        readfile($updatedFilePath);

        exit;
    }
} else {
    header('Location: ../login.php');
    exit;
}
?>