<?php
session_start();
include('db.php');

if ($_SESSION['logged_id'] <= 0) {
    header('Location: ./');
}

$order_id = $_GET['id'] ?? 0;

// Get order details
$statement = $pdo->prepare("SELECT o.* FROM pixel_media_order o WHERE o.order_id = ?");
$statement->execute(array($order_id));
$order = $statement->fetch(PDO::FETCH_ASSOC);

// Get order items
$statement = $pdo->prepare("SELECT * FROM pixel_media_order_details d WHERE order_id = ?");
$statement->execute(array($order_id));
$order_details = $statement->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$order_total_amount = $order['order_total_amount'];
$advance_paid = $order['payment_amount'];
$due = $order_total_amount - $advance_paid;

function formatNumber($number)
{
    return number_format($number, 2, '.', '&nbsp;');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?> - Infive Print</title>
    <style>
        @page {
            size: A5;
            margin: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
            font-size: 12px;
        }

        .invoice-container {
            width: 100%;
            max-width: 140mm;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #eee;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 2px solid #1BA664;
            padding-bottom: 10px;
        }

        .company-info h1 {
            color: #1BA664;
            margin: 0;
            font-size: 18px;
        }

        .company-info p {
            margin: 3px 0;
            font-size: 10px;
        }

        .invoice-info h2 {
            margin: 0;
            color: #1BA664;
            font-size: 16px;
            text-align: right;
        }

        .invoice-info p {
            margin: 3px 0;
            font-size: 10px;
            text-align: right;
        }

        .client-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .client-box,
        .payment-box {
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 5px;
            font-size: 10px;
        }

        .client-box {
            border: 1px solid black;
            flex: 1;

        }

        .payment-box {
            background-color: #1BA664;
            border: 1px solid black;
            color: white;
            width: 35%;
            min-width: 120px;
        }

        .client-box h3,
        .payment-box h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 12px;
            color: black;
        }

        .client-box p,
        .payment-box p {
            margin: 3px 0;
            font-size: 10px;
            color: black;
        }

        .payment-box p,
        .totals-value {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table th {
            background-color: #1BA664;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
        }

        table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .spec-details div {
            margin-bottom: 2px;
            font-size: 9px;
        }

        .totals {
            float: right;
            width: 150px;
            margin-top: 10px;
            font-size: 10px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .totals-label {
            font-weight: 600;
        }

        .totals-value {
            text-align: right;
            width: 60px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 9px;
            color: #777;
        }

        .thank-you {
            text-align: center;
            margin-top: 15px;
            font-style: italic;
            color: #1BA664;
            font-size: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        
        <div class="client-info" style="padding: 1px;">
            <div class="client-box">
                <h3>BILL TO</h3>
                <p><strong><?php echo $order['company_name']; ?></strong></p>
                <p>Phone: <?php echo $order['phone']; ?></p>
                <p>Delivery Address: <?php echo $order['delivery_address']; ?></p>
                <p>Date: <?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                <p>Invoice #: <?php echo $order_id; ?></p>
            </div>
            
            <div class="payment-box">
                <h3>PAYMENT SUMMARY</h3>
                <p>Advance: Rs. <?php echo htmlspecialchars_decode(formatNumber(abs($advance_paid))); ?></p>
                <p>Due: Rs. <?php echo htmlspecialchars_decode(formatNumber(abs($due))); ?></p>
                <br>
                <div style="border: 3px solid black;">
                <p style="font-size: 15px; text-align: center; font-weight: bold;">Total: Rs.
                    <?php echo htmlspecialchars_decode(formatNumber(abs($order_total_amount))); ?>
                </p>
                </div>
                
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 20%; color: black;">PRODUCT</th>
                    <th style="width: 35%; color: black;">SPECIFICATIONS</th>
                    <th style="width: 10%; color: black;">QTY</th>
                    <th style="width: 15%; color: black;">UNIT PRICE</th>
                    <th style="width: 20%; color: black;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $item) { ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td class="spec-details">
                            <?php
                            if (!empty($item['product_specification'])) {
                                $spec = json_decode($item['product_specification'], true);
                                if ($spec) {
                                    // Display print type first if it exists
                                    if (isset($spec['print_type'])) {
                                        $printTypes = is_array($spec['print_type']) ? $spec['print_type'] : [$spec['print_type']];
                                    }

                                    foreach ($spec as $key => $val) {
                                        $key = str_replace('_', ' ', $key);

                                        // Skip already displayed fields and internal fields
                                        if ($key == 'print_type' || strpos($key, 'spec') === 0)
                                            continue;

                                        // Handle custom type
                                        if ($key == 'type' && $val == 'Other' && isset($spec['custom_type'])) {
                                            echo "<div><strong>Type</strong>: " . htmlspecialchars($spec['custom_type']) . "</div>";
                                        }
                                        // Handle custom thickness
                                        elseif ($key == 'thikness' && $val == 'Other' && isset($spec['custom_thikness'])) {
                                            echo "<div><strong>Thickness</strong>: " . htmlspecialchars($spec['custom_thikness']) . "</div>";
                                        }
                                        // Handle regular fields
                                        elseif (!empty($val) && !is_array($val)) {
                                            echo "<div><strong>" . htmlspecialchars(ucfirst($key)) . "</strong>: " . htmlspecialchars($val) . "</div>";
                                        }
                                    }
                                } else {
                                    echo htmlspecialchars($item['product_specification']);
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs. <?php echo formatNumber($item['price']); ?></td>
                        <td>Rs. <?php echo formatNumber($item['total']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="totals" style="margin-right: 40px;">
            <div class="totals-row">
                <span class="totals-label">Subtotal:</span>
                <span class="totals-value">Rs.
                    <?php echo htmlspecialchars_decode(formatNumber(abs($order_total_amount))); ?></span>
            </div>
            <div class="totals-row">
                <span class="totals-label">Advance Paid:</span>
                <span class="totals-value">Rs.
                    <?php echo htmlspecialchars_decode(formatNumber(abs($advance_paid))); ?></span>
            </div>
            <div class="totals-row">
                <span class="totals-label">Balance Due:</span>
                <span class="totals-value">Rs. <?php echo htmlspecialchars_decode(formatNumber(abs($due))); ?></span>
            </div>
        </div>
        <div style="clear: both;"></div>

        <!-- <div class="thank-you">
            <p style="color: black; font-weight: bold;">Thank you for your business!</p>
        </div> -->

        <!-- <div class="footer">
            <p>INFIVE PRINT | 206A Wilgoda Est.Rd, Kurunegale</p>
            <p>Terms: Payment due upon receipt. Late payments subject to interest charges.</p>
        </div> -->
    </div>

    <div class="no-print" style="text-align: center; margin-top: 10px;">
        <button onclick="window.print()"
            style="padding: 5px 10px; background-color: #1BA664; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 10px;">
            Print Invoice
        </button>
        <button onclick="window.close()"
            style="padding: 5px 10px; background-color: #666; color: white; border: none; border-radius: 3px; cursor: pointer; margin-left: 5px; font-size: 10px;">
            Close Window
        </button>
    </div>

    <script>
        window.onload = function () {
            // Auto-print if requested
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('autoprint')) {
                window.print();
            }
        };
    </script>
</body>

</html>