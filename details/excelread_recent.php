<?php
// Include PhpSpreadsheet autoloader first, if you're using Composer
require_once 'vendor/autoload.php';  // Make sure this path is correct

use PhpOffice\PhpSpreadsheet\IOFactory;
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

// File path to the Excel file
//$filePath = 'C:/wamp64/www/urdf/Best Dept MU Proforma - Modified 23rd September 2024.xls'; // Replace with your Excel file's path
$filePath = 'C:/wamp64/www/urdf/Updated_Best_Dept_MU_Proforma.xlsx';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Details</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bscallout.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css"> 
    <style>
        body { padding: 20px; background-color: #f8f9fa; }
        .table th, .table td { text-align: left; }
        .header-row { background-color: #f2f2f2; font-weight: bold; }

        /* Custom column widths for each sheet */
        .sheet-1 .col-1 { width: 50%;  }
        .sheet-1 .col-2 { width: 50%; }

        .sheet-2 .col-1 { width: 10%; }
        .sheet-2 .col-2 { width: 50%; }
        .sheet-2 .col-3 { width: 20%; }
        .sheet-2 .col-4 { width: 20%; }

        .sheet-3 .col-1 { width: 10%; }
        .sheet-3 .col-2 { width: 50%; }
        .sheet-3 .col-3 { width: 20%; }
        .sheet-3 .col-4 { width: 20%; }

        .sheet-4 .col-1 { width: 10%; }
        .sheet-4 .col-2 { width: 50%; }
        .sheet-4 .col-3 { width: 20%; }
        .sheet-4 .col-4 { width: 20%; }

        .sheet-5 .col-1 { width: 10%; }
        .sheet-5 .col-2 { width: 50%; }
        .sheet-5 .col-3 { width: 20%; }
        .sheet-5 .col-4 { width: 20%; }

        .sheet-6 .col-1 { width: 10%; }
        .sheet-6 .col-2 { width: 50%; }
        .sheet-6 .col-3 { width: 20%; }
        .sheet-6 .col-4 { width: 20%; }

        .sheet-8 .col-2 { width: 10%; }
        .sheet-8 .col-3 { width: 20%; }
        .sheet-8 .col-4 { width: 20%; }
        .sheet-8 .col-5 { width: 20%; }

        .sheet-9 .col-1 { width: 100%; }

        .table td.empty-cell {
            border: none; /* Hide border */
            background-color: #f8f9fa; /* Light grey background for empty cells */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Details</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="../instructions.php">UDRF</a></li>		
                    <li class="active"><a href="excelread.php"><strong>OPTION 1 (Excel read)</strong></a></li>
                    <li ><a href="dropdown.php">OPTION 2 (Dropdown)</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </div><!--.nav-collapse -->
        </div>
    </nav>

    <div class="container">
        <?php
            echo '<div class="bs-callout bs-callout-success hidden-print"><p class="lead"> <small>Currently Logged in as: </small><strong>'.htmlentities($_SESSION['username']) .'</strong></p>';
            echo '<p><small>Wrong User? Click to </small><a href="../logout.php">Log out</a>.</p></div>';
        ?>

        <h1 class="text-center">Excel Sheet Data</h1>

        <form id="committeeForm" method="post" action="save_committee.php">
    <?php foreach ($data as $sheetIndex => $sheet): ?>
        <h3>Sheet: <?php echo $sheet['sheetName']; ?></h3>

        <table class="table table-bordered sheet-<?php echo $sheetIndex + 1; ?>">
            <?php if (!empty($sheet['data'])): ?>
                <thead>
                    <?php if ($sheet['headerRows'] > 0): ?>
                        <tr class="header-row">
                            <th colspan="4"><?php echo $sheet['data'][0][0]; ?></th>
                        </tr>
                    <?php endif; ?>

                    <?php for ($i = 1; $i < $sheet['headerRows']; $i++): ?>
                        <tr class="header-row">
                            <?php for ($col = 0; $col < count($sheet['data'][$i]); $col++): ?>
                                <th class="col-<?php echo ($col + 1); ?>"><?php echo $sheet['data'][$i][$col]; ?></th>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </thead>

                <tbody>
                    <?php $totalMarks = 0; ?>
                    <?php for ($i = $sheet['headerRows']; $i < count($sheet['data']); $i++): ?>
                        <tr>
                            <?php
                            $isLastRow = ($i == count($sheet['data']) - 1);
                            ?>
                            <?php for ($col = 0; $col < count($sheet['data'][$i]); $col++): ?>
                                <td class="col-<?php echo ($col + 1); ?> <?php echo ($col == 0 && trim($sheet['data'][$i][$col]) === '') ? 'empty-cell' : ''; ?>">
                                    <?php if ($col == 3): // Assuming the "Committee" column is the fourth column ?>
                                        <?php if ($isLastRow): ?>
                                            <?php $totalMarks =+ (int)$sheet['data'][$i][$col]; ?>
                                            <input type="text" name="committee[<?php echo $sheetIndex; ?>][<?php echo $i; ?>]" value="<?php echo htmlspecialchars($totalMarks); ?>" disabled>
                                        <?php else: ?>
                                            <input type="text" name="committee[<?php echo $sheetIndex; ?>][<?php echo $i; ?>]" value="<?php echo htmlspecialchars($sheet['data'][$i][$col]); ?>">
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($sheet['data'][$i][$col]); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                            <?php if ($isLastRow): ?>
                                <td>Total Marks: <?php echo $totalMarks; ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            <?php endif; ?>
        </table>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Save Committee Data</button>
</form>    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/select2.js"></script>
    <script src="assets/js/app.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2({
                width: '200%'
            });
        });
    </script>
</body>
</html>