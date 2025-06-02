<?php
session_start(); // Start session to access username
include('./db.php');

// Validate user session
if (!isset($_SESSION['logged_id']) || $_SESSION['logged_id'] <= 0) {
    header('Location: ./login.php');
    exit;
}

// Get order details
$statement = $pdo->prepare("SELECT * FROM pixel_media_order WHERE order_id = ?");
$statement->execute(array($order_id));
$order = $statement->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Calculate amounts
$discount_amount = $order['discount_amount'] ?? 0; // Default to 0 if not set
$final_amount = $order['order_total_amount'] - $discount_amount;
$due = $final_amount - $order['payment_amount'];

error_reporting(E_ALL);
ini_set('display_errors', 1);
require('fpdf.php');

class PDF extends FPDF
{
    // Function to draw a rounded rectangle
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F') {
            $op = 'f';
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    // Function to draw an arc
    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    function Footer()
    {
        $this->AddFont('Montserrat', '', 'Montserrat Regular 400.php');
        // Position at 1.5 cm from bottom
        $this->SetY(-45);
        // Montserrat regular 8
        $this->SetFont('Montserrat', '', 8);
        // Footer text
        $this->Cell(0, 10, 'This is a computer-generated invoice; no signature required.', 0, 0, 'L');
        $this->Image(BASE_URL . '/report/infive_footer.jpg', 0, 260, 210);
    }
}

$line_height = 6;

$pdf = new PDF();
$pdf->AddPage();
$pdf->AddFont('Montserrat', '', 'Montserrat Regular 400.php');
$pdf->AddFont('MontserratB', '', 'Montserrat Bold 700.php');

// Header: Logo and Company Info
$pdf->Image(BASE_URL . '/report/infive_logo.png', 10, 10, 30);
$pdf->SetFont('MontserratB', '', 10);
$pdf->SetXY(10, 40);
$pdf->Cell(40, $line_height, 'Infive Print', 0, 1, 'L');
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(40, $line_height, 'Issued by: ' . (isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'Unknown User'), 0, 1, 'L');
$pdf->Ln(5);

// Invoice To Section
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(40, $line_height, 'Invoice to', 0, 1, 'L');
$pdf->Cell(40, $line_height, $order['company_name'], 0, 1, 'L');

$pdf->Cell(15, $line_height, 'PHONE: ', 0, 0, 'L');
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(40, $line_height, $order['phone'], 0, 1, 'L');

// Order Info Section
$pdf->Ln(5);
$y = $pdf->GetY();

$pdf->SetFillColor(238, 238, 238);
$pdf->SetDrawColor(238, 238, 238);
$pdf->RoundedRect(10, $pdf->GetY(), 190, $line_height * 3, 2, 'DF');

$pdf->Cell(5, $line_height - 3, '', 0, 0, 'L', false);
$pdf->Cell(30, $line_height - 3, '', 0, 1, 'L', false);

$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(5, $line_height - 3, '', 0, 0, 'L', false);
$pdf->Cell(30, $line_height, 'ORDER ID', 0, 1, 'L', false);

$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(5, $line_height - 3, '', 0, 0, 'L', false);
$pdf->Cell(30, $line_height, $order['order_id'], 0, 1, 'L', false);

$pdf->SetXY(45, $y);
$pdf->Cell(30, $line_height - 3, '', 0, 1, 'L', false);
$pdf->SetX(45);

$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(30, $line_height, 'DATE', 0, 1, 'L', false);

$pdf->SetFont('Montserrat', '', 10);
$pdf->SetX(45);
$pdf->Cell(30, $line_height, date('d-F-Y', strtotime($order['order_date'])), 0, 1, 'L', false);

$pdf->SetXY(85, $y);
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(65, $line_height, 'DELIVERY ADDRESS', 0, 1, 'L', false);
$pdf->SetX(85);
$pdf->SetFont('Montserrat', '', 10);
$pdf->MultiCell(65, $line_height - 1, $order['delivery_address'], 0, 'L', 0);

$pdf->SetFont('MontserratB', '', 8);
$pdf->SetXY(150, $y + 3);
$pdf->Cell(20, $line_height - 2, 'TOTAL: RS.' . number_format($order['order_total_amount'], 2), 0, 1, 'L', false);
$pdf->SetX(150);
$pdf->Cell(20, $line_height - 2, 'ADVANCE: RS.' . number_format($order['payment_amount'], 2), 0, 1, 'L', false);
$pdf->SetX(150);
$pdf->Cell(20, $line_height - 2, 'TOTAL DUE: RS.' . number_format($due, 2), 0, 1, 'L', false);

$pdf->Ln(10);

// Table Header
$pdf->SetFillColor(14, 167, 85);
$pdf->SetDrawColor(14, 167, 85);
$pdf->RoundedRect(10, $pdf->GetY() + 10, 190, $line_height * 1.5, 2, 'DF');

$pdf->SetFont('MontserratB', '', 10);
$pdf->Ln(10);
$pdf->Cell(60, $line_height * 1.5, 'PRODUCT', 0, 0, 'L');
$pdf->Cell(70, $line_height * 1.5, 'SPEC', 0, 0, 'C');
$pdf->Cell(15, $line_height * 1.5, 'QTY', 0, 0, 'L');
$pdf->Cell(25, $line_height * 1.5, 'UNIT PRICE', 0, 0, 'L');
$pdf->Cell(25, $line_height * 1.5, 'TOTAL', 0, 1, 'L');

$pdf->Ln(2);

// Table Body
$statement = $pdo->prepare("SELECT * FROM pixel_media_order_details d WHERE order_id = ?");
$statement->execute(array($order_id));
$order_details = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($order_details as $item) {
    if (empty($item['product_specification']) || $item['product_specification'] == '-') {
        $row = 1;
    } else {
        $spec = json_decode($item['product_specification'], true);
        $row = count($spec);
    }
    $y = $pdf->GetY();
    $pdf->SetFont('MontserratB', '', 10);
    $pdf->Cell(60, $line_height * $row, $item['product_name'], 0, 0, 'L', false);
    if (empty($item['product_specification']) || $item['product_specification'] == '-') {
        $pdf->SetX(70);
        $pdf->Cell(70, $line_height, '-', 0, 0, 'L', false);
    } else {
        $spec = json_decode($item['product_specification'], true);
        foreach ($spec as $key => $val) {
            $key = str_replace('_', ' ', $key);
            $pdf->SetX(70);
            $pdf->Cell(70, $line_height, $key . ' : ' . $val, 0, 1, 'L', false);
        }
    }

    $pdf->SetFont('Montserrat', '', 10);
    $pdf->SetXY(140, $y);
    $pdf->Cell(15, $line_height * $row, $item['quantity'], 0, 0, 'L', false);
    $pdf->Cell(25, $line_height * $row, 'RS.' . number_format($item['price'], 2), 0, 0, 'L', false);
    $pdf->Cell(25, $line_height * $row, 'RS.' . number_format($item['total'], 2), 0, 1, 'L', false);

    $pdf->Cell(190, 3, '', 'B', 1, 'L', false);
}

// Totals Section
$pdf->Ln(2);
$pdf->SetFont('Montserrat', '', 10);

// Total Order Amount
$pdf->Cell(130, $line_height, '', 0, 0, 'R', false);
$pdf->Cell(30, $line_height, 'TOTAL ', 0, 0, 'L', false);
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(30, $line_height, 'RS.' . number_format($order['order_total_amount'], 2), 0, 1, 'L', false);

// Discount Amount
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(130, $line_height, '', 0, 0, 'R', false);
$pdf->Cell(30, $line_height, 'DISCOUNT', 0, 0, 'L', false);
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(30, $line_height, '- RS.' . number_format($discount_amount, 2), 0, 1, 'L', false);

// Final Amount After Discount
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(130, $line_height, '', 0, 0, 'R', false);
$pdf->Cell(30, $line_height, 'FINAL AMOUNT', 0, 0, 'L', false);
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(30, $line_height, 'RS.' . number_format($final_amount, 2), 0, 1, 'L', false);

// Advance Payment
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(130, $line_height, '', 0, 0, 'R', false);
$pdf->Cell(30, $line_height, 'ADVANCE', 0, 0, 'L', false);
$pdf->SetFont('MontserratB', '', 10);
$pdf->Cell(30, $line_height, 'RS.' . number_format($order['payment_amount'], 2), 0, 1, 'L', false);

// Total Due (with green background)
$pdf->SetFillColor(14, 167, 85);
$pdf->SetDrawColor(14, 167, 85);
$pdf->RoundedRect(140, $pdf->GetY() + 7, 60, $line_height * 2, 2, 'DF');

$pdf->Ln(10);
$pdf->SetFont('Montserrat', '', 11);
$pdf->Cell(130, $line_height, '', 0, 0, 'R', false);
$pdf->Cell(30, $line_height, 'TOTAL DUE', 0, 0, 'L', false);
$pdf->SetFont('MontserratB', '', 11);
$pdf->Cell(30, $line_height, 'RS.' . number_format($due, 2), 0, 1, 'L', false);

$pdf->Output('invoice.pdf', 'D');
?>