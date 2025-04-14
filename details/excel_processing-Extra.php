<?php
include_once 'db_connect.php';
include_once 'functions.php';
sec_session_start();

if (login_check($mysqli) == true) {
    ;
} else {
    header('Location: ../login.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include PhpSpreadsheet library
require 'vendor/autoload.php'; // Update path as needed

use PhpOffice\PhpSpreadsheet\IOFactory;

// File path to the Excel file
$filePath = 'C:\xampp\htdocs\Best Dept MU Proforma - Modified 23rd September 2024.xls'; // Replace with your file path

$data = []; // Array to store extracted data

try {
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);
    $sheetCount = $spreadsheet->getSheetCount();

    // Loop through each sheet
    for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
        $sheet = $spreadsheet->getSheet($sheetIndex);
        $sheetName = $sheet->getTitle();
        $sheetData = [];
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $headerRows = 0; // Default number of header rows
        $maxColumns = $highestColumnIndex; // Default max columns

        // Adjust header rows and column count for each sheet based on the structure
        if ($sheetIndex == 0) { // First sheet (Profile sheet)
            $headerRows = 3;
            $maxColumns = 2; // 2 columns wide
        } elseif ($sheetIndex == 8) { // Ninth sheet (Category sheet)
            $headerRows = 0; // No headers
            $maxColumns = 1; // 1 column wide
        } elseif ($sheetIndex >= 1 && $sheetIndex <= 4) { // Sheets 2–5
            $headerRows = 3;
            $maxColumns = 4; // 4 columns wide
        } elseif ($sheetIndex == 5) { // Sheet 6
            $headerRows = 4;
            $maxColumns = 4; // 4 columns wide
        } elseif ($sheetIndex == 6) { // Sheet 7
            $headerRows = 5;
            $maxColumns = 4; // 4 columns wide
        } elseif ($sheetIndex == 7) { // Sheet 8 - Skip first column
            $headerRows = 3;
            $maxColumns = 5; // 4 columns wide, but skip the first column
        }

        // Extract data from the sheet
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmptyRow = true;

            for ($col = 1; $col <= $maxColumns; $col++) {
                if ($sheetIndex == 7 && $col == 1) {
                    continue; // Skip the first column for sheet 8
                }
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                $cellValue = $sheet->getCell($cellCoordinate)->getValue();
                $cellValue = htmlspecialchars($cellValue ?? ''); // Escape HTML

                if (trim($cellValue) !== '') {
                    $isEmptyRow = false; // If any cell has data, mark row as not empty
                }

                $rowData[] = $cellValue;
            }

            if (!$isEmptyRow) {
                $sheetData[] = $rowData; // Add non-empty row
            }
        }

        // Store extracted data for each sheet
        $data[] = [
            'sheetName' => $sheetName,
            'headerRows' => $headerRows,
            'data' => $sheetData
        ];
    }
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo "Error reading Excel file: " . $e->getMessage();
}

// Store data to be used in the next file
session_start();
$_SESSION['excel_data'] = $data;
header('Location: excelreadOG.php');
exit;
