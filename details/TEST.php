<?php
// Include PhpSpreadsheet autoloader first, if you're using Composer
require_once 'vendor/autoload.php';  // Make sure this path is correct

use PhpOffice\PhpSpreadsheet\IOFactory;
include_once 'db_connect.php';
include_once 'functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// File path to the Excel file
$filePath = 'C:\wamp64\www\urdf\Best Dept MU Proforma - Modified 23rd September 2024.xls';
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
            $headerRows = 4;
            $maxColumns = 4; // 4 columns wide
        } elseif ($sheetIndex == 7) { // Sheet 8 - Skip first column
            $headerRows = 2;
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
                $cell = $sheet->getCell($cellCoordinate);
            
                // Get the raw cell value
                $rawValue = $cell->getValue();
            
                // Check if the cell contains a formula and evaluate it if needed
                if (is_string($rawValue) && substr($rawValue, 0, 1) === '=') {
                    $cellValue = $cell->getCalculatedValue(); // Evaluate the formula
                } else {
                    $cellValue = $rawValue; // Use the raw value
                }
            
                // Escape HTML for output safety
                $cellValue = $cellValue ?? '';
            
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
function getFilePath($mysqli, $collno, $particulars, $srno) {
    $path = null;
    $query = "SELECT path FROM MergedDetails WHERE collno = ? AND particulars = ? AND srno = ? LIMIT 1";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("isi", $collno, $particulars, $srno);
        $stmt->execute();
        $stmt->bind_result($path);
        $stmt->fetch();
        $stmt->close();
    }
    return $path;
}

// Map Excel sheet names to database particulars
$sheetMappings = [
    'stud supp, achiev, & prog' => 'studsuppachieveprog',
    'research details' => 'researchdetails',
    'nep initiatives' => 'nepinitiatives', 
    'dept gov and prac' => 'deptgovandprac',
    'conf worksh sem' => 'confworkshsem',
    'collaborations' => 'collaborations'
];

$collno = 89; // Hardcoded college number
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Details</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <style>
                    .navbar-inverse {
                background-color: #2c3e50;
                border: none;
                border-radius: 0;
            }

            .navbar-brand {
                font-weight: 500;
                letter-spacing: 0.5px;
            }

            /* Specific column width adjustments for each sheet */
            .sheet-1 .col-1 { width: 60%; }
            .sheet-1 .col-2 { width: 40%; }
            .sheet-2 .col-1 { width: 15%; }
            .sheet-2 .col-2 { width: 55%; }
            .sheet-2 .col-3 { width: 15%; }
            .sheet-2 .col-4 { width: 15%; }
            .sheet-3 .col-1 { width: 15%; }
            .sheet-3 .col-2 { width: 55%; }
            .sheet-3 .col-3 { width: 15%; }
            .sheet-3 .col-4 { width: 15%; }
            .sheet-4 .col-1 { width: 15%; }
            .sheet-4 .col-2 { width: 55%; }
            .sheet-4 .col-3 { width: 15%; }
            .sheet-4 .col-4 { width: 15%; }
            .sheet-5 .col-1 { width: 15%; }
            .sheet-5 .col-2 { width: 55%; }
            .sheet-5 .col-3 { width: 15%; }
            .sheet-5 .col-4 { width: 15%; }
            .sheet-6 .col-1 { width: 15%; }
            .sheet-6 .col-2 { width: 55%; }
            .sheet-6 .col-3 { width: 15%; }
            .sheet-6 .col-4 { width: 15%; }
            .sheet-8 .col-2 { width: 15%; }
            .sheet-8 .col-3 { width: 25%; }
            .sheet-8 .col-4 { width: 25%; }
            .sheet-8 .col-5 { width: 25%; }
            .sheet-9 .col-1 { width: 100%; }

            input[type="text"].form-control {
            background-color: #3498db; /* Blue background */
            color: white; /* White text */
            border: 1px solid #2980b9; /* Darker blue border */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        /* Style for input boxes when filled with a number (green) */
        input[type="text"].form-control.filled {
            background-color: #2ecc71; /* Green background */
            color: white; /* White text */
            border: 1px solid #27ae60; /* Darker green border */
        }      
    </style>
</head>

<body>
    <?php
        include_once '../new_header.php';
    ?>

    <div class="container">

        <div class="bs-callout">
            <p class="lead"><strong>Proforma Details</strong></p>
        </div>
                <div class="table-responsive">
            <form name="form1" id="form1" method="POST" action="save_committee.php">
            <?php
            foreach ($data as $sheetIndex => $sheetData) {
                $originalSheetName = $sheetData['sheetName'];
                $headerRows = $sheetData['headerRows'];
                $sheetRows = $sheetData['data'];
                
                // Normalize sheet name for matching
                $cleanSheetName = preg_replace('/\(.*?\)/', '', $originalSheetName); // Remove text in parentheses
                $cleanSheetName = trim(strtolower($cleanSheetName));
                $matchedParticulars = null;

                // Find matching database particulars
                foreach ($sheetMappings as $excelPattern => $dbParticulars) {
                    if (strpos($cleanSheetName, strtolower($excelPattern)) !== false) {
                        $matchedParticulars = $dbParticulars;
                        break;
                    }
                }

                if (!$matchedParticulars) continue; // Skip unmapped sheets

                echo "<div class='table-responsive sheet-" . ($sheetIndex + 1) . "'>";
                echo "<h3>" . htmlspecialchars($originalSheetName) . "</h3>";
                echo "<table class='table table-bordered'>";
                
                // Header rows
                echo "<thead>";
                echo "<tr class='header-row'><th colspan='" . count($sheetRows[0]) . "'>" . htmlspecialchars($originalSheetName) . "</th></tr>";
                
                if ($headerRows > 0) {
                    for ($i = 0; $i < $headerRows; $i++) {
                        echo "<tr class='header-row'>";
                        $currentRow = $sheetRows[$i];
                        
                        $nonEmptyCells = array_filter($currentRow, function($value) { 
                            return trim($value) !== ''; 
                        });
                        
                        if (count($nonEmptyCells) === 1) {
                            $headerValue = reset($currentRow);
                            echo "<th colspan='" . count($currentRow) . "'>" . htmlspecialchars($headerValue) . "</th>";
                        } else {
                            foreach ($currentRow as $header) {
                                echo "<th>" . htmlspecialchars($header) . "</th>";
                            }
                        }
                        echo "</tr>";
                    }
                }
                echo "</thead>";

                // Table body
  // ... [Previous code remains unchanged until table body section] ...

// Table body
echo "<tbody>";
for ($i = $headerRows; $i < count($sheetRows); $i++) {
    echo "<tr>";
    $rowData = $sheetRows[$i];
    
    if ($sheetIndex == 5) {
        // Calculate SR No. for the first 6 content rows
        $srno = ($i - $headerRows) + 1;
        $maxSrno = 6; // Only print SR numbers for the first 6 rows
    } else {
        // Regular SR no. handling for other sheets
        $srno = trim($rowData[0]);
    }

    foreach ($rowData as $colIndex => $cell) {
        echo "<td class='col-" . ($colIndex + 1) . "'>";

        // Handle document links
        if ($colIndex == 1) {
            // For Sheet 6, use calculated SR no. instead of cell value
            $effectiveSrno = ($sheetIndex == 5) ? $srno : $srno;
            
            if (is_numeric($effectiveSrno)) {
                $filePath = getFilePath($mysqli, $collno, $matchedParticulars, $effectiveSrno);
                
                if ($filePath) {
                    echo "<a href='$filePath' target='_blank'>$cell</a>";
                } else {
                    echo $cell;
                }
            } else {
                echo $cell;
            }
        } else {
            if ($sheetIndex == 5 && $colIndex == 0 && $srno <= $maxSrno) {
                echo $srno; // Display generated SR number
            } else {
                echo $cell; // Print cell content as-is
            }
        }
        
        echo "</td>";
    }

    // Input column logic
    if ($sheetIndex != 0 && $sheetIndex != count($data) - 1) {
        if (is_numeric($effectiveSrno)) {
            echo "<td><input type='text' name='input[$sheetIndex][$i]' class='form-control'></td>";
        }
    }
    echo "</tr>";
}
echo "</tbody></table></div>";

                // Add Save button for mapped sheets
                echo "<button type='submit' name='save_sheet' value='$sheetIndex' 
                      class='btn btn-primary save-btn'>Save " . htmlspecialchars($originalSheetName) . "</button>";
            }
            ?>
        </form>
    </div>

    <style>
    .doc-link {
        color: #2ecc71;
        font-weight: 500;
        text-decoration: underline;
    }
    .doc-link:hover {
        color: #27ae60;
        text-decoration: none;
    }
    .save-btn {
        margin: 20px 0 40px;
        padding: 10px 30px;
    }
    </style>
            </form>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Select all input boxes
    const inputs = document.querySelectorAll('input[type="text"].form-control');

    // Add event listener to each input box
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            // Check if the input contains a number
            if (/\d/.test(this.value)) {
                this.classList.add('filled'); // Add 'filled' class if number is present
            } else {
                this.classList.remove('filled'); // Remove 'filled' class if no number
            }
        });
    });
});
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src="assets/js/select2.min.js"></script>

</body>
</html>