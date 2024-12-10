<?php
require('../dbcred/db.php');
require('../fpdf/fpdf.php');
require('../fpdi/src/autoload.php');

use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();

// Set page size to 8.5 x 13 inches
$pdf->AddPage('P', array(215.9, 355.6)); // Portrait orientation

// Load the existing PDF
$pageCount = $pdf->setSourceFile('../admin/static/MinorRepairForm.pdf'); 
$templateId = $pdf->importPage(1); 
$pdf->useTemplate($templateId, 0, 0, 215.9); // Adjust template to match new page size

// Add a header
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetXY(10, 10); // Position for the header

// Retrieve data from GET parameters
$name = isset($_GET['name']) ? $_GET['name'] : 'N/A';
$position = isset($_GET['position']) ? $_GET['position'] : 'N/A';
$department = isset($_GET['department']) ? $_GET['department'] : 'N/A';
$email = isset($_GET['email']) ? $_GET['email'] : 'N/A';
$type = isset($_GET['type']) ? $_GET['type'] : 'N/A';
$serial = isset($_GET['serial']) ? $_GET['serial'] : 'N/A';
$brandmodel = isset($_GET['brandmodel']) ? $_GET['brandmodel'] : 'N/A';
$propertyno = isset($_GET['propertyno']) ? $_GET['propertyno'] : 'N/A';
$acqcost = isset($_GET['acqcost']) ? $_GET['acqcost'] : 'N/A';
$acqdate = isset($_GET['acqdate']) ? $_GET['acqdate'] : 'N/A';
$scope = isset($_GET['scope']) ? $_GET['scope'] : 'N/A';
$datetime = isset($_GET['datetime']) ? $_GET['datetime'] : 'N/A';
list($date, $time) = explode(' ', $datetime, 2); // Split into date and time

$pdf->SetFont('Arial', 'B', 12);

// Absolute positions for each piece of data
$pdf->SetXY(45, 50); // X position for Type
$pdf->Cell(90, 10, $type, 0, 0);

$pdf->SetXY(145, 50); // X position for BrandModel
$pdf->Cell(90, 10, $brandmodel, 0, 1);

$pdf->SetXY(45, 58); // X position for Serial
$pdf->Cell(90, 10, $serial, 0, 0);

$pdf->SetXY(145, 58); // X position for PropertyNo
$pdf->Cell(90, 10, $propertyno, 0, 1);

$pdf->SetXY(45, 66); // X position for AcqDate
$pdf->Cell(90, 8, $acqdate, 0, 0);

$pdf->SetXY(145, 66); // X position for AcqCost
$pdf->Cell(90, 8, $acqcost, 0, 1);

// Handle Scope with line breaking and ellipsis
$maxLines = 5;
$lineHeight = 5; // Height of each line
$maxCharsPerLine = 76; // Maximum characters per line
$maxLength = $maxLines * $maxCharsPerLine; // Total characters allowed

if (strlen($scope) > $maxLength) {
    // Truncate to fit max lines and add ellipsis
    $scope = substr($scope, 0, $maxLength - 3) . '...';
}

// Scope with line breaking
$pdf->SetXY(45, 86); // X position for Scope
$pdf->MultiCell(0, $lineHeight, $scope, 0, 'L', false);

$pdf->SetXY(45, 123); // X position for Name
$pdf->Cell(90, 8, $name, 0, 0);

$pdf->SetXY(163, 123); // X position for Position
$pdf->Cell(90, 8, $position, 0, 1);

$pdf->SetXY(45, 129); // X position for Department
$pdf->Cell(0, 8, $department, 0, 1);

$pdf->SetXY(45, 135); // X position for Date
$pdf->Cell(90, 8, $date, 0, 0);

$pdf->SetXY(115, 135); // X position for Time
$pdf->Cell(90, 8, $time, 0, 1);

// Output the new PDF to download
$pdf->Output('D', $name . '-repair.pdf');

?>
