<?php
require_once 'vendor/autoload.php';  // Ensure PhpSpreadsheet is included

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:/xampp/htdocs/Best Dept MU Proforma - Modified 23rd September 2024.xls'; // Update with your file path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = $_POST['data'] ?? [];

    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($filePath);

        // Select the correct sheet
        $sheetIndex = 0; // Update this to the correct sheet index if required
        $sheet = $spreadsheet->getSheet($sheetIndex);

        // Update only the editable rows in the 4th column
        foreach ($updatedData as $rowIndex => $columns) {
            if (isset($columns[3])) { // Check if column 4 is set
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4) . ($rowIndex + 1);
                $sheet->setCellValue($cellCoordinate, $columns[3]);
            }
        }

        // Save the updated Excel file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        echo "Changes saved successfully!";
    } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        echo "Error saving Excel file: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
