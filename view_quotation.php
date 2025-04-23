<?php
include('db.php');

// Get the quotation number from URL
$quotation_number = isset($_GET['quotation_number']) ? $_GET['quotation_number'] : '';
$print_mode = isset($_GET['print']) ? true : false;

// Fetch quotation details from database
$stmt = $pdo->prepare("SELECT * FROM pixel_media_quotations WHERE quotation_number = ?");
$stmt->execute([$quotation_number]);
$quotation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quotation) {
    die("Quotation not found");
}

// Decode the items
$items = json_decode($quotation['quotation_items'], true);

// If in print mode, update status to "sent" if it's currently "draft"
if ($print_mode && $quotation['status'] == 'draft') {
    $update_stmt = $pdo->prepare("UPDATE pixel_media_quotations SET status = 'sent' WHERE quotation_number = ?");
    $update_stmt->execute([$quotation_number]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation <?php echo $quotation_number; ?></title>
    <?php if (!$print_mode): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php endif; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F7F9F9;
            padding: 20px;
        }

        .quotation-container {
            background-color: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .quotation-header {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .quotation-title {
            color: #1BA664;
            font-size: 24px;
            font-weight: bold;
        }

        .quotation-number {
            font-size: 16px;
            margin-top: 5px;
        }

        .company-info {
            text-align: right;
        }

        .company-info h5 {
            margin: 0;
        }

        .company-info p {
            margin: 5px 0;
        }

        .quotation-details {
            margin-bottom: 30px;
        }

        .to-from {
            display: flex;
            justify-content: space-between;
        }

        .to,
        .from {
            width: 48%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .totals {
            margin-top: 20px;
            text-align: right;
        }

        .totals table {
            width: 300px;
            float: right;
            margin: 0;
        }

        .totals tr:last-child {
            font-weight: bold;
        }

        .terms {
            margin-top: 30px;
        }

        .terms h4 {
            font-size: 20px;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .quotation-footer {
            border-top: 1px solid #ccc;
            padding-top: 15px;
            margin-top: 50px;
            font-size: 13px;
            text-align: center;
            color: #555;
        }

        .quotation-footer p {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .status-draft {
            background-color: #6c757d;
            color: white;
        }

        .status-sent {
            background-color: #17a2b8;
            color: white;
        }

        .status-accepted {
            background-color: #28a745;
            color: white;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .quotation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .quotation-table th {
            background-color: #1BA664;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .quotation-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .quotation-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        @media print {
    @page {
        size: A4 portrait;
        margin: 10mm 15mm;
    }

    body {
        background: white !important;
        color: black !important;
        font-size: 11pt !important;
        line-height: 1.3 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quotation-container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .quotation-header {
        display: flex !important;
        flex-wrap: wrap !important;
        margin-bottom: 10mm !important;
        page-break-after: avoid;
    }

    .quotation-header img {
        width: 150px !important;
        height: auto !important;
    }

    .quotation-header>div {
        width: 50% !important;
        box-sizing: border-box;
    }

    .quotation-title {
        font-size: 24pt !important;
        margin-bottom: 5mm !important;
    }

    .quotation-details {
        margin-bottom: 10mm !important;
    }

    .to-from {
        display: flex !important;
        flex-wrap: wrap !important;
    }

    .to, .from {
        width: 48% !important;
        margin-bottom: 5mm !important;
    }

    table {
        width: 100% !important;
        max-width: 100% !important;
        margin: 5mm 0 !important;
        page-break-inside: avoid !important;
        font-size: 10pt !important;
    }

    th, td {
        padding: 2mm !important;
        border: 0.5pt solid #ddd !important;
    }

    .totals table {
        width: 60% !important;
        max-width: 60% !important;
    }

    .terms {
        margin-top: 10mm !important;
        margin-bottom: 10mm !important;
    }

    .signature {
        margin-top: 15mm !important;
        margin-bottom: 10mm !important;
    }

    /* CHANGED: Remove fixed positioning for footer */
    .quotation-footer {
        border-top: 0.5pt solid #ccc !important;
        padding-top: 3mm !important;
        margin-top: 10mm !important;
        font-size: 9pt !important;
        text-align: center !important;
        position: static !important;
        bottom: auto !important;
    }

    .no-print, .status-badge, .action-buttons {
        display: none !important;
    }

    /* Prevent page breaks inside important elements */
    .quotation-header, .quotation-details, .terms, .signature {
        page-break-inside: avoid;
    }
}
    </style>
</head>

<body>
    <div class="quotation-container">
        <!-- HEADER START -->
        <div class="quotation-header">
            <!-- Left side: Logo -->
            <div>
                <img src="assets/img/infive_logo.png" alt="logo" width="200">
            </div>

            <!-- Right side: Quotation title + number + company info -->
            <div class="company-info">
                <div class="quotation-title" style="font-size: 40px; font-weight: bold;">QUOTATION</div>
                <strong>Quotation #:</strong> <?php echo $quotation_number; ?>
                <h5>Infive (Pvt) Ltd</h5>
                <p>infivellc@gmail.com | InfivePrint.com | Info@infive.lk </p>
                <p>+94 71 499 4579 | +937 4 300 250 </p>
                <?php if (!$print_mode): ?>
                    <span class="status-badge status-<?php echo $quotation['status']; ?>">
                        <?php echo ucfirst($quotation['status']); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <!-- HEADER END -->

        <div class="quotation-details">
            <div class="to-from">
                <div class="to">
                    <strong>To:</strong><br>
                    <?php if (!empty($quotation['company_name'])): ?>
                        <?php echo htmlspecialchars($quotation['company_name']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($quotation['contact_person'])): ?>
                        Attn: <?php echo htmlspecialchars($quotation['contact_person']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($quotation['phone'])): ?>
                        Tel: <?php echo htmlspecialchars($quotation['phone']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($quotation['email'])): ?>
                        Email: <?php echo htmlspecialchars($quotation['email']); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($quotation['delivery_address'])): ?>
                        Address: <?php echo htmlspecialchars($quotation['delivery_address']); ?>
                    <?php endif; ?>
                </div>
                <div class="from">
                    <strong>Date:</strong> <?php echo date('Y-m-d', strtotime($quotation['quotation_date'])); ?><br>
                    <strong>Valid Until:</strong> <?php echo $quotation['valid_until']; ?>
                </div>
            </div>
        </div>

        <table class="quotation-table" style="width:100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #eee;">
                    <th>No</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($item['product']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>Rs. <?php echo number_format($quotation['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td>Rs. <?php echo number_format($quotation['discount'], 2); ?></td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total:</td>
                    <td>Rs. <?php echo number_format($quotation['total_amount'], 2); ?></td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>

        <div class="terms">
            <h4>Terms & Conditions:</h4>
            <ol>
                <li>This quotation is valid until <?php echo $quotation['valid_until']; ?>.</li>
                <li>60% advance payment required to confirm the order.</li>
                <li>Delivery time starts after the approval of final artwork.</li>
                <li>Prices are subject to change without prior notice.</li>
            </ol>
        </div>

        <div class="signature">
            <p><strong>This is an electronic invoice, no signature required</strong></p>
        </div>

        <!-- FOOTER START -->
        <div class="quotation-footer">
            <p>Payment Details :- Infive (Private) Limited | 000610017878 | Sampath Bank - Kurunegala</p>
            <p>Thank you for your business! | Infive (Private) Limited</p>
        </div>
        <!-- FOOTER END -->

        <?php if (!$print_mode): ?>
            <div class="no-print">
                <button onclick="printQuotation()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print Quotation
                </button>
                <a href="quotation_history.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to History
                </a>

                <?php if ($quotation['status'] == 'draft'): ?>
                    <a href="edit_quotation.php?quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>"
                        class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Quotation
                    </a>
                <?php endif; ?>

                <?php if ($quotation['status'] == 'sent'): ?>
                    <button class="btn btn-success" onclick="updateStatus('accepted')">
                        <i class="fas fa-check"></i> Mark as Accepted
                    </button>
                    <button class="btn btn-danger" onclick="updateStatus('rejected')">
                        <i class="fas fa-times"></i> Mark as Rejected
                    </button>
                <?php endif; ?>
            </div>

            <script>
                function printQuotation() {
                    // Open in new tab with print parameter
                    window.open('view_quotation.php?quotation_number=<?php echo $quotation_number; ?>&print=true', '_blank');
                }
                function updateStatus(newStatus) {
                    if (confirm('Are you sure you want to mark this quotation as ' + newStatus + '?')) {
                        fetch('update_quotation_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'quotation_number=<?php echo urlencode($quotation['quotation_number']); ?>&status=' + newStatus
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Status updated successfully');
                                    location.reload();
                                } else {
                                    alert('Error updating status: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while updating status');
                            });
                    }
                }
            </script>
        <?php else: ?>
            <script>
                // Automatically print if in print mode
                window.onload = function () {
                    window.print();
                    setTimeout(function () {
                        window.close();
                    }, 1000);
                };
            </script>
        <?php endif; ?>
    </div>
</body>

</html> 