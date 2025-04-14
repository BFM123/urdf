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
        $sheetCount = $spreadsheet->getSheetCount();

        // Prepare SQL statement for inserting committee marks
        $stmt = $mysqli->prepare("INSERT INTO committee_marks (sheet_index, row_index, committee_mark) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE committee_mark = VALUES(committee_mark)");

        // Update the Excel file with the committee data and insert into the database
        foreach ($committeeData as $sheetIndex => $rows) {
            // Check if the sheet index is valid
            if ($sheetIndex >= $sheetCount) {
                continue; // Skip invalid sheet indices
            }

            $sheet = $spreadsheet->getSheet($sheetIndex);
            $headerRows = 0; // Default number of header rows

            // Adjust header rows based on the structure
            if ($sheetIndex == 0) { // First sheet (Profile sheet)
                $headerRows = 3;
            } elseif ($sheetIndex == 8) { // Ninth sheet (Category sheet)
                $headerRows = 0; // No headers
            } elseif ($sheetIndex >= 1 && $sheetIndex <= 4) { // Sheets 2–5
                $headerRows = 3;
            } elseif ($sheetIndex == 5) { // Sheet 6
                $headerRows = 4;
            } elseif ($sheetIndex == 6) { // Sheet 7
                $headerRows = 4;
            } elseif ($sheetIndex == 7) { // Sheet 8 - Skip first column
                $headerRows = 2;
            }

            foreach ($rows as $rowIndex => $committee) {
                // Adjust the row index to account for header rows
                $excelRowIndex = $rowIndex + $headerRows + 1; // +1 because Excel rows are 1-based

                // Assuming the "Committee" column is the second column (D)
                $cellCoordinate = 'D' . $excelRowIndex;
                $sheet->setCellValue($cellCoordinate, $committee);

                // Insert committee mark into the database
                $stmt->bind_param("iis", $sheetIndex, $rowIndex, $committee);
                $stmt->execute();
            }
        }

        // Close the prepared statement
        $stmt->close();

        // Save the updated Excel file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $updatedFilePath = 'C:/wamp64/www/urdf/Updated_Best_Dept_MU_Proforma.xlsx'; // Path to save the updated file
        $writer->save($updatedFilePath);

        // Send the updated file to the browser for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Updated_Best_Dept_MU_Proforma.xlsx"');
        header('Cache-Control: max-age=0');
        readfile($updatedFilePath);

        exit;
    }
} else {
    header('Location: ../login.php');
    exit;
}
?>