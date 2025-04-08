<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require FPDF library
require('fpdf.php');

// Check if PEAR is required and if so, include it
// require('PEAR.php'); // Uncomment if PEAR is needed

// Create a new instance of FPDF
$pdf = new FPDF('P', 'mm', 'A4');

// Set document properties
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);

// Add a page
$pdf->AddPage();

// ALL VARIABLES
$name = "Richard Tsalwa";
$unit1 = "Music Theory";
$unit2 = "Repertoire";
$unit3 = "Practical Assessment";
$rank1 = "Distinction";
$rank2 = "Credit";
$rank3 = "Pass";

// Set font and add text to the document
$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(170, 5, "NO: 0678", 0, 1, 'R');
$pdf->SetX(20);
$pdf->Cell(180, 5, "NAME: " . $name, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(180, 5, "CONTEMPORARY GUITAR PROGRAM", 0, 1, 'L');

// Table headers
$line_height = 6;
$pdf->Ln(25);
$pdf->SetFont('Arial', 'UB', 12);
$pdf->Cell(40, $line_height, "", 0, 0, 'C');
$pdf->Cell(50, $line_height, "UNIT", 1, 0, 'L');
$pdf->Cell(50, $line_height, "GRADE", 1, 1, 'L');

// Table data
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, $line_height, "", 0, 0, 'C');
$pdf->Cell(50, $line_height, $unit1, 1, 0, 'L');
$pdf->Cell(50, $line_height, $rank1, 1, 1, 'L');

$pdf->Cell(40, $line_height, "", 0, 0, 'C');
$pdf->Cell(50, $line_height, $unit2, 1, 0, 'L');
$pdf->Cell(50, $line_height, $rank2, 1, 1, 'L');

$pdf->Cell(40, $line_height, "", 0, 0, 'C');
$pdf->Cell(50, $line_height, $unit3, 1, 0, 'L');
$pdf->Cell(50, $line_height, $rank3, 1, 1, 'L');

// Additional content
$pdf->Ln(15);
$pdf->SetX(20);
$pdf->Cell(180, $line_height, "You have completed the requirements for Contemporary Guitar certification with CREDIT.", 0, 1, 'L');

$pdf->Ln(15);
$pdf->SetX(20);
$pdf->Cell(30, $line_height, "____________________", 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(180, $line_height, "Date of Publication of Result: January 7, 2017", 0, 1, 'L');

$pdf->Ln(15);
$pdf->SetX(20);
$pdf->Cell(180, $line_height, "Note: Report to our office any concerns that you may have with the results hereby indicated.", 0, 1, 'L');

$pdf->SetX(140);
$pdf->Cell(30, $line_height + 2, "Kamata School of Music", 0, 1, 'L');
$pdf->SetX(140);
$pdf->Cell(30, $line_height + 2, "Admin:", 0, 1, 'L');
$pdf->SetX(140);
$pdf->Cell(30, $line_height + 2, "...................................", 0, 0, 'L');

// Output the PDF
$pdf->Output("1.pdf", "D");
$pdf->Close();
?>
