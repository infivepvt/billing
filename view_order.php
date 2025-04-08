<?php
session_start();
include('db.php');

if ($_SESSION['logged_id'] <= 0) {
    header('Location: ./');
}
$product = array('Business card Design', 'Business card Print full color', 'Business card Print foil', 'Business card Print matte', 'Sticker print', 'Flyer Print', 'Tag Print', 'Any Other Design', 'Any Other Print', 'Delivery');
function formatNumber($number)
{
    return number_format($number, 2, '.', ' ');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>View Order - Infive Print</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F7F9F9;
        }

        .main-navbar,
        .navbar-bg {
            display: none;
        }

        @media (max-width: 991.98px) {

            .main-navbar,
            .navbar-bg {
                display: flex;
            }

            .main-content {
                padding-top: 140px;
            }
        }

        .navbar-bg,
        .main-navbar {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .order-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #FFF;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #1BA664;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .client-box {
            border: 2px solid #1BA664;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .payment-box {
            border: 2px solid #1BA664;
            border-radius: 15px;
            background-color: #1BA664;
            color: white;
            padding: 15px;
        }

        .delivery-box {
            border: 2px solid #1BA664;
            border-radius: 15px;
            background-color: #2E86C1;
            color: white;
            padding: 10px;
            margin-top: 10px;
        }

        .heading-16 {
            font-size: 14px;
            font-weight: 600;
        }

        .heading-20 {
            font-size: 18px;
            font-weight: 700;
        }

        .heading-22 {
            font-size: 20px;
            font-weight: 700;
        }

        .btn-success {
            background-color: #1BA664;
            border-color: #1BA664;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #148f55;
            border-color: #148f55;
        }

        .product-table {
            width: 100%;
            margin: 20px 0;
        }

        .product-table th {
            background-color: #1BA664;
            color: white;
            padding: 10px;
            font-weight: 600;
        }

        .product-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }

        .product-table tr:last-child td {
            border-bottom: none;
        }

        .spec-details {
            line-height: 1.6;
        }

        .sms-action-row {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .amount-input {
            width: 120px;
            display: inline-block;
            margin-left: 10px;
        }

        .tracking-input {
            width: 150px;
            display: inline-block;
            margin-left: 10px;
        }

        .modal-xl-custom {
            max-width: 95%;
            max-height: 90vh;
        }

        .modal-content-custom {
            height: 90vh;
        }

        .modal-body-custom {
            overflow-y: auto;
            max-height: 70vh;
        }

        .my-custom-table.table-hover tbody tr:hover {
            background-color: #d4edda;
            /* Light green example */
        }

        /* Add these new styles */
        .product-row {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .remove-product {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .remove-product:hover {
            color: #c82333;
        }

        .specification-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            background-color: white;
        }

        .product-select {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="col-md-12 offset-md-0">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg" style="background-color: #1BA664;color: white;"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
            </nav>

            <?php include('left_menu.php'); ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-body">
                        <?php
                        $statement = $pdo->prepare("SELECT o.company_name, o.payment_type, o.payment_amount, o.order_total_amount, o.delivery_address, o.phone, o.order_date, o.order_id, o.order_status, d.product_name, d.quantity, d.total FROM pixel_media_order o, pixel_media_order_details d WHERE o.order_id = d.order_id and o.order_id = ?");
                        $statement->execute(array($order_id));
                        $order = $statement->fetch(PDO::FETCH_ASSOC);

                        $advance_paid = $order['payment_amount'];
                        $order_total_amount = $order['order_total_amount'];
                        $due = $order_total_amount - $advance_paid;
                        ?>

                        <div class="order-container">
                            <h1 class="section-title">View Order</h1>
                            <div class="row">
                                <div class="col-md-4" style="color: #1BA664;font-weight: bold;">CUSTOMER DETAILS
                                </div>

                            </div><br>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="client-box">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="heading-20"><?php echo $order['company_name']; ?></div>
                                                <div class="heading-16">PHONE: <?php echo $order['phone']; ?></div>
                                                <div class="heading-16">ORDER DATE:
                                                    <?php echo date('d-F-Y', strtotime($order['order_date'])); ?>
                                                </div>
                                                <div class="heading-16">ORDER ID: <?php echo $order['order_id']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="heading-20">DELIVERY ADDRESS</div>
                                                <div><?php echo $order['delivery_address']; ?></div>
                                                <div class="heading-16">
                                                    <a href="https://wa.me/94<?php echo ltrim($order['phone'], '0'); ?>"
                                                        target="_blank" class="btn btn-success btn-sm whatsapp-btn"
                                                        style="margin-Top: 10px;">
                                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="payment-box">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="heading-20">Payment Summary</div>
                                                <div class="heading-22">Total: Rs.
                                                    <?php echo formatNumber(abs($order_total_amount)); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="heading-16">Advance: Rs.
                                                    <?php echo formatNumber(abs($advance_paid)); ?>
                                                </div>
                                                <div class="heading-16">Due: Rs. <?php echo formatNumber(abs($due)); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="delivery-box">
                                        <div class="row">
                                            <div class="col-md-6 heading-16">CASH ON DELIVERY:</div>
                                            <div class="col-md-6 heading-16">Rs. <?php echo formatNumber(abs($due)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row align-items-center">
                                        <!-- Order Update Link -->
                                        <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                                            <div class="heading-16 mb-1">CLIENT ORDER UPDATE LINK</div>
                                            <div class="input-group">
                                                <input type="text" class="form-control" readonly
                                                    value="<?php echo BASE_URL . '/order/' . $order_id; ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary"
                                                        onclick="copyToClipboard(this)">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons Group -->
                                        <div class="col-md-6 col-sm-12">
                                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                                <!-- Print Invoice Button -->
                                                <a class="btn btn-primary"
                                                    href="<?php echo BASE_URL . '/invoice.php?id=' . $order_id; ?>"
                                                    target="_blank" style="padding: 10px;">
                                                    <i class="fas fa-print"></i> Print Invoice
                                                </a>
                                                <!-- Download Invoice Button -->
                                                <a class="btn btn-success"
                                                    href="<?php echo BASE_URL . '/report/' . $order_id; ?>"
                                                    style="padding: 10px;">
                                                    <i class="fas fa-download"></i> Download Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $statement = $pdo->prepare("SELECT * FROM pixel_media_order_details d WHERE order_id = ?");
                            $statement->execute(array($order_id));
                            $order_details = $statement->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <div class="row" style="margin-top: 30px;">
                                <div class="col-md-10">
                                    <h3 class="section-title">PRODUCT DETAILS</h3>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button class="btn btn-success" id="btnEditProducts">
                                        <i class="fas fa-edit"></i> Edit Products
                                    </button>
                                </div>
                            </div>

                            <table class="product-table">
                                <thead>
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th>SPECIFICATIONS</th>
                                        <th>QTY</th>
                                        <th>UNIT PRICE</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_details as $item) { ?>
                                        <tr>
                                            <td class="heading-16"><?php echo $item['product_name']; ?></td>
                                            <td class="spec-details">
                                                <?php
                                                if (empty($item['product_specification'])) {
                                                    echo '-';
                                                } else {
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
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>Rs. <?php echo $item['price']; ?></td>
                                            <td>Rs. <?php echo $item['total']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-2 text-right">
                                    <div class="heading-16">Total</div>
                                    <div class="heading-16">Advance</div>
                                    <div class="heading-16">Due</div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <div class="heading-16">Rs. <?php echo formatNumber(abs($order_total_amount)); ?></div>
                                    <div class="heading-16">Rs. <?php echo formatNumber(abs($advance_paid)); ?></div>
                                    <div class="heading-16">Rs. <?php echo formatNumber(abs($due)); ?></div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 40px;">
                                <div class="col-md-12">
                                    <h3 class="section-title">ORDER PROCESS NOTIFICATIONS SMS</h3>
                                </div>
                            </div>

                            <div class="sms-action-row">
                                <div class="row align-items-center">
                                    <div class="col-md-4">Order Created</div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('ORDER PLACED','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                    <div class="col-md-4">Design Files Sent</div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('DESIGN SUBMITED','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="sms-action-row">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        Advance Received
                                        <input type="text" class="form-control amount-input" id="advance_amount"
                                            placeholder="Amount">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('ADVANCE RECEIVED','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                    <div class="col-md-4">Your Order Printed</div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('PRINTED','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="sms-action-row">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        Full Payment Received
                                        <input type="text" class="form-control amount-input" id="full_amount"
                                            placeholder="Amount">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('FULL PAYMENT RECEIVED','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        Printed and already Dispatched
                                        <input type="text" class="form-control tracking-input" id="tracking_no"
                                            placeholder="Tracking #">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success btn-sm"
                                            onclick="send_sms('PACKAGE ALREADY WITH COURIER SERVICE','<?php echo $order_id; ?>')">
                                            <i class="fas fa-paper-plane"></i> Send
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 40px;">
                                <div class="col-md-12">
                                    <h3 class="section-title">ENDING THE ORDER</h3>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <button class="btn btn-danger btn-lg"
                                        onclick="send_sms('COMPLETE','<?php echo $order_id; ?>')">
                                        <i class="fas fa-check-circle"></i> MARK AS ORDER COMPLETED
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Edit Products Modal -->
    <div class="modal fade" id="editProductsModal" tabindex="-1" role="dialog" aria-labelledby="editProductsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl-custom" role="document">
            <div class="modal-content modal-content-custom">
                <div class="modal-header" style="background-color: #1BA664; color: white;">
                    <h5 class="modal-title" id="editProductsModalLabel">Edit Product Details - Order
                        #<?php echo $order_id; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form id="productDetailsForm">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover my-custom-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 25%;">Product</th>
                                        <th style="width: 30%;">Specifications</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 15%;">Unit Price (Rs.)</th>
                                        <th style="width: 15%;">Total (Rs.)</th>
                                        <th style="width: 5%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productDetailsBody">
                                    <?php foreach ($order_details as $index => $item): ?>
                                        <tr>

                                            <td>
                                                <select class="form-control product-select"
                                                    name="products[<?php echo $index; ?>][product_name]" required>
                                                    <option value="">Select Product</option>
                                                    <?php
                                                    // System products
                                                    foreach ($product as $val) {
                                                        $selected = ($item['product_name'] == $val) ? 'selected' : '';
                                                        echo "<option value='$val' $selected>$val</option>";
                                                    }

                                                    // Custom products from database
                                                    $statement = $pdo->prepare("SELECT * FROM pixel_media_product");
                                                    $statement->execute();
                                                    $order_product = $statement->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($order_product as $row) {
                                                        $val = $row['product_name'];
                                                        $selected = ($item['product_name'] == $val) ? 'selected' : '';
                                                        echo "<option value='$val' $selected>$val</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>

                                            <!-- Replace the specifications textarea in your edit modal with this -->
                                            <td class="specification-column">
                                                <?php
                                                $specs = !empty($item['product_specification']) ? json_decode($item['product_specification'], true) : [];
                                                $productType = $item['product_name'];
                                                ?>

                                                <!-- Business card Print full color -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Business card Print full color') ? '' : 'display:none;' ?>">
                                                    <!-- In the Business card Print full color specification box -->
                                                    <div class="row">
                                                        <div class="col-md-12" style="color:green">Type</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control type-select"
                                                                name="products[<?= $index ?>][spec][type]">
                                                                <option value="Standard Shape" <?= ($specs['type'] ?? '') == 'Standard Shape' ? 'selected' : '' ?>>Standard
                                                                    Shape</option>
                                                                <option value="Shape CUT" <?= ($specs['type'] ?? '') == 'Shape CUT' ? 'selected' : '' ?>>Shape CUT</option>
                                                                <option value="Shape" <?= ($specs['type'] ?? '') == 'Shape' ? 'selected' : '' ?>>Shape</option>
                                                                <option value="Other" <?= ($specs['type'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control custom-type-input"
                                                                name="products[<?= $index ?>][spec][custom_type]"
                                                                value="<?= ($specs['type'] ?? '') == 'Other' ? ($specs['custom_type'] ?? '') : '' ?>"
                                                                style="margin-top:5px; <?= ($specs['type'] ?? '') == 'Other' ? '' : 'display:none;' ?>"
                                                                placeholder="Specify type">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Thikness</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][thikness]">
                                                                <option value="360gsm" <?= ($specs['Thikness'] ?? '') == '360gsm' ? 'selected' : '' ?>>360gsm</option>
                                                                <option value="760gsm (32pt)" <?= ($specs['Thikness'] ?? '') == '760gsm (32pt)' ? 'selected' : '' ?>>760gsm (32pt)
                                                                </option>
                                                                <option value="Other" <?= ($specs['Thikness'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][custom_thikness]"
                                                                value="<?= $specs['Thikness'] ?? '' ?>"
                                                                style="margin-top:5px; <?= ($specs['Thikness'] ?? '') == 'Other' ? '' : 'display:none;' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Print Type</div>
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Full color" <?= strpos($specs['Print_Type'] ?? '', 'Full color') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Full color</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Gold Foil" <?= strpos($specs['Print_Type'] ?? '', 'Gold Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Gold Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Silver Foil" <?= strpos($specs['Print_Type'] ?? '', 'Silver Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Silver Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Other" <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Other</label>
                                                                <input type="text" class="form-control"
                                                                    name="products[<?= $index ?>][spec][custom_print_type]"
                                                                    value="<?= $specs['Print_Type'] ?? '' ?>"
                                                                    style="margin-top:5px; <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? '' : 'display:none;' ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Corners</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][corners]">
                                                                <option value="Square" <?= ($specs['Corners'] ?? '') == 'Square' ? 'selected' : '' ?>>Square</option>
                                                                <option value="Rounded" <?= ($specs['Corners'] ?? '') == 'Rounded' ? 'selected' : '' ?>>Rounded</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Business card Print foil -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Business card Print foil') ? '' : 'display:none;' ?>">
                                                    <!-- Same structure as full color -->
                                                    <div class="row">
                                                        <div class="col-md-12" style="color:green">Type</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control type-select"
                                                                name="products[<?= $index ?>][spec][type]">
                                                                <option value="Standard Shape" <?= ($specs['type'] ?? '') == 'Standard Shape' ? 'selected' : '' ?>>Standard
                                                                    Shape</option>
                                                                <option value="Shape CUT" <?= ($specs['type'] ?? '') == 'Shape CUT' ? 'selected' : '' ?>>Shape CUT</option>
                                                                <option value="Shape" <?= ($specs['type'] ?? '') == 'Shape' ? 'selected' : '' ?>>Shape</option>
                                                                <option value="Other" <?= ($specs['type'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control custom-type-input"
                                                                name="products[<?= $index ?>][spec][custom_type]"
                                                                value="<?= ($specs['type'] ?? '') == 'Other' ? ($specs['custom_type'] ?? '') : '' ?>"
                                                                style="margin-top:5px; <?= ($specs['type'] ?? '') == 'Other' ? '' : 'display:none;' ?>"
                                                                placeholder="Specify type">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Thikness</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][thikness]">
                                                                <option value="360gsm" <?= ($specs['Thikness'] ?? '') == '360gsm' ? 'selected' : '' ?>>360gsm</option>
                                                                <option value="760gsm (32pt)" <?= ($specs['Thikness'] ?? '') == '760gsm (32pt)' ? 'selected' : '' ?>>760gsm (32pt)
                                                                </option>
                                                                <option value="Other" <?= ($specs['Thikness'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][custom_thikness]"
                                                                value="<?= $specs['Thikness'] ?? '' ?>"
                                                                style="margin-top:5px; <?= ($specs['Thikness'] ?? '') == 'Other' ? '' : 'display:none;' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Print Type</div>
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Full color" <?= strpos($specs['Print_Type'] ?? '', 'Full color') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Full color</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Gold Foil" <?= strpos($specs['Print_Type'] ?? '', 'Gold Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Gold Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Silver Foil" <?= strpos($specs['Print_Type'] ?? '', 'Silver Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Silver Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Other" <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Other</label>
                                                                <input type="text" class="form-control"
                                                                    name="products[<?= $index ?>][spec][custom_print_type]"
                                                                    value="<?= $specs['Print_Type'] ?? '' ?>"
                                                                    style="margin-top:5px; <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? '' : 'display:none;' ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Corners</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][corners]">
                                                                <option value="Square" <?= ($specs['Corners'] ?? '') == 'Square' ? 'selected' : '' ?>>Square</option>
                                                                <option value="Rounded" <?= ($specs['Corners'] ?? '') == 'Rounded' ? 'selected' : '' ?>>Rounded</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Business card Print matte -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Business card Print matte') ? '' : 'display:none;' ?>">
                                                    <!-- Same structure as full color -->
                                                    <div class="row">
                                                        <div class="col-md-12" style="color:green">Type</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control type-select"
                                                                name="products[<?= $index ?>][spec][type]">
                                                                <option value="Standard Shape" <?= ($specs['type'] ?? '') == 'Standard Shape' ? 'selected' : '' ?>>Standard
                                                                    Shape</option>
                                                                <option value="Shape CUT" <?= ($specs['type'] ?? '') == 'Shape CUT' ? 'selected' : '' ?>>Shape CUT</option>
                                                                <option value="Shape" <?= ($specs['type'] ?? '') == 'Shape' ? 'selected' : '' ?>>Shape</option>
                                                                <option value="Other" <?= ($specs['type'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control custom-type-input"
                                                                name="products[<?= $index ?>][spec][custom_type]"
                                                                value="<?= ($specs['type'] ?? '') == 'Other' ? ($specs['custom_type'] ?? '') : '' ?>"
                                                                style="margin-top:5px; <?= ($specs['type'] ?? '') == 'Other' ? '' : 'display:none;' ?>"
                                                                placeholder="Specify type">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Thikness</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][thikness]">
                                                                <option value="360gsm" <?= ($specs['Thikness'] ?? '') == '360gsm' ? 'selected' : '' ?>>360gsm</option>
                                                                <option value="760gsm (32pt)" <?= ($specs['Thikness'] ?? '') == '760gsm (32pt)' ? 'selected' : '' ?>>760gsm (32pt)
                                                                </option>
                                                                <option value="Other" <?= ($specs['Thikness'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][custom_thikness]"
                                                                value="<?= $specs['Thikness'] ?? '' ?>"
                                                                style="margin-top:5px; <?= ($specs['Thikness'] ?? '') == 'Other' ? '' : 'display:none;' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Print Type</div>
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Full color" <?= strpos($specs['Print_Type'] ?? '', 'Full color') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Full color</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Gold Foil" <?= strpos($specs['Print_Type'] ?? '', 'Gold Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Gold Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Silver Foil" <?= strpos($specs['Print_Type'] ?? '', 'Silver Foil') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Silver Foil</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="products[<?= $index ?>][spec][print_type][]"
                                                                    value="Other" <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? 'checked' : '' ?>>
                                                                <label class="form-check-label">Other</label>
                                                                <input type="text" class="form-control"
                                                                    name="products[<?= $index ?>][spec][custom_print_type]"
                                                                    value="<?= $specs['Print_Type'] ?? '' ?>"
                                                                    style="margin-top:5px; <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? '' : 'display:none;' ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Corners</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][corners]">
                                                                <option value="Square" <?= ($specs['Corners'] ?? '') == 'Square' ? 'selected' : '' ?>>Square</option>
                                                                <option value="Rounded" <?= ($specs['Corners'] ?? '') == 'Rounded' ? 'selected' : '' ?>>Rounded</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sticker print -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Sticker print') ? '' : 'display:none;' ?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <!-- Similar checkbox structure as above -->
                                                            <div class="row">
                                                                <div class="col-md-12" style="color:red">Print Type</div>
                                                                <div class="col-md-12">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="products[<?= $index ?>][spec][print_type][]"
                                                                            value="Full color"
                                                                            <?= strpos($specs['Print_Type'] ?? '', 'Full color') !== false ? 'checked' : '' ?>>
                                                                        <label class="form-check-label">Full color</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="products[<?= $index ?>][spec][print_type][]"
                                                                            value="Gold Foil" <?= strpos($specs['Print_Type'] ?? '', 'Gold Foil') !== false ? 'checked' : '' ?>>
                                                                        <label class="form-check-label">Gold Foil</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="products[<?= $index ?>][spec][print_type][]"
                                                                            value="Silver Foil"
                                                                            <?= strpos($specs['Print_Type'] ?? '', 'Silver Foil') !== false ? 'checked' : '' ?>>
                                                                        <label class="form-check-label">Silver Foil</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="products[<?= $index ?>][spec][print_type][]"
                                                                            value="Other" <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? 'checked' : '' ?>>
                                                                        <label class="form-check-label">Other</label>
                                                                        <input type="text" class="form-control"
                                                                            name="products[<?= $index ?>][spec][custom_print_type]"
                                                                            value="<?= $specs['Print_Type'] ?? '' ?>"
                                                                            style="margin-top:5px; <?= strpos($specs['Print_Type'] ?? '', 'Other') !== false ? '' : 'display:none;' ?>">
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Flyer Print -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Flyer Print') ? '' : 'display:none;' ?>">


                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Thikness</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][thikness]">
                                                                <option value="360gsm" <?= ($specs['Thikness'] ?? '') == '360gsm' ? 'selected' : '' ?>>360gsm</option>
                                                                <option value="760gsm (32pt)" <?= ($specs['Thikness'] ?? '') == '760gsm (32pt)' ? 'selected' : '' ?>>760gsm (32pt)
                                                                </option>
                                                                <option value="Other" <?= ($specs['Thikness'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][custom_thikness]"
                                                                value="<?= $specs['Thikness'] ?? '' ?>"
                                                                style="margin-top:5px; <?= ($specs['Thikness'] ?? '') == 'Other' ? '' : 'display:none;' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Print Type</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][print_type]">
                                                                <option value="Full color" <?= ($specs['Print_Type'] ?? '') == 'Full color' ? 'selected' : '' ?>>Full color
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tag Print -->
                                                <div class="specification-box"
                                                    style="<?= ($productType == 'Tag Print') ? '' : 'display:none;' ?>">
                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Size</div>
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][size]"
                                                                value="<?= $specs['Size'] ?? '' ?>">
                                                        </div>
                                                    </div>



                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Thikness</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][thikness]">
                                                                <option value="360gsm" <?= ($specs['Thikness'] ?? '') == '360gsm' ? 'selected' : '' ?>>360gsm</option>
                                                                <option value="760gsm (32pt)" <?= ($specs['Thikness'] ?? '') == '760gsm (32pt)' ? 'selected' : '' ?>>760gsm (32pt)
                                                                </option>
                                                                <option value="Other" <?= ($specs['Thikness'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                                            </select>
                                                            <input type="text" class="form-control"
                                                                name="products[<?= $index ?>][spec][custom_thikness]"
                                                                value="<?= $specs['Thikness'] ?? '' ?>"
                                                                style="margin-top:5px; <?= ($specs['Thikness'] ?? '') == 'Other' ? '' : 'display:none;' ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Print Type</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][print_type]">
                                                                <option value="Full color" <?= ($specs['Print_Type'] ?? '') == 'Full color' ? 'selected' : '' ?>>Full color
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12" style="color:red">Finishes</div>
                                                        <div class="col-md-12">
                                                            <select class="form-control"
                                                                name="products[<?= $index ?>][spec][finishes]">
                                                                <option value="Matte" <?= ($specs['Finishes'] ?? '') == 'Matte' ? 'selected' : '' ?>>Matte</option>
                                                                <option value="Velvet" <?= ($specs['Finishes'] ?? '') == 'Velvet' ? 'selected' : '' ?>>Velvet</option>
                                                                <option value="Gloss" <?= ($specs['Finishes'] ?? '') == 'Gloss' ? 'selected' : '' ?>>Gloss</option>
                                                                <option value="None" <?= ($specs['Finishes'] ?? '') == 'None' ? 'selected' : '' ?>>None</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- For simple note products -->
                                                <div class="specification-box"
                                                    style="<?= (in_array($productType, ['Business card Design', 'Any Other Design', 'Any Other Print', 'Delivery'])) ? '' : 'display:none;' ?>">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <textarea class="form-control"
                                                                name="products[<?= $index ?>][spec][note]"
                                                                rows="2"><?= $specs['note'] ?? '' ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control quantity"
                                                    name="products[<?php echo $index; ?>][quantity]"
                                                    value="<?php echo $item['quantity']; ?>" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control price"
                                                    name="products[<?php echo $index; ?>][price]"
                                                    value="<?php echo $item['price']; ?>" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control total"
                                                    name="products[<?php echo $index; ?>][total]"
                                                    value="<?php echo $item['total']; ?>" readonly
                                                    style="background-color: #f8f9fa;">
                                            </td>
                                            <td style="vertical-align: middle; text-align: center;">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"
                                                    title="Remove product">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right">
                                            <button type="button" class="btn btn-success btn-sm" id="btnAddProduct">
                                                <i class="fas fa-plus"></i> Add Product
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
    <div class="col-md-6 text-left">
        <div class="alert alert-info" style="margin-bottom: 0;">
            <strong>Order Summary:</strong>
            <div>Subtotal: <span id="modalSubtotal">Rs. <?php echo $order['order_total_amount']; ?></span></div>
            <div>Advance: Rs. <?php echo $advance_paid; ?></div>
            <div>Due: <span id="dueAmount">Rs. <?php echo abs($due); ?></span></div> <!-- Added abs() here -->
        </div>
    </div>
    <div class="col-md-6 text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary" id="btnSaveProducts">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</div>
            </div>
        </div>
    </div>

    <!-- Response Modal -->
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="ajaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajaxModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/popper.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/tooltip.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/moment.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/stisla.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>

    <script type="text/javascript">
        function send_sms(order_status, order_id) {
            $.ajax({
                url: "../send_sms.php",
                type: "POST",
                data: {
                    order_status: order_status,
                    order_id: order_id,
                    advance_amount: $('#advance_amount').val(),
                    full_amount: $('#full_amount').val(),
                    tracking_no: $('#tracking_no').val()
                },
                success: function (response) {
                    $('#ajaxModal .modal-body').html(response);
                    $('#ajaxModal').modal('show');

                    // Reload page for payment-related messages
                    if (order_status === 'ADVANCE RECEIVED' || order_status === 'FULL PAYMENT RECEIVED') {
                        setTimeout(function () {
                            location.reload();
                        }, 1500); // Reload after 1.5 seconds
                    }
                }
            });
        }

        $(document).ready(function () {
            // Store original advance paid amount
            var originalAdvance = <?php echo $advance_paid; ?>;

            // Show edit modal
            $('#btnEditProducts').click(function () {
                $('#editProductsModal').modal('show');
            });

            // Calculate totals when quantity or price changes
            $(document).on('input', '.quantity, .price', function () {
                var row = $(this).closest('tr');
                var quantity = parseFloat(row.find('.quantity').val()) || 0;
                var price = parseFloat(row.find('.price').val()) || 0;
                var total = quantity * price;
                row.find('.total').val(total.toFixed(2));
                updateOrderSummary();
            });

            // Handle product selection changes
            $(document).on('change', '.product-select', function () {
                var row = $(this).closest('tr');
                var productName = $(this).val();

                // Hide all specification boxes first
                row.find('.specification-box').hide();

                // Show the appropriate specification box based on product type
                if (productName.includes('Business card Print')) {
                    if (productName.includes('full color')) {
                        row.find('.specification-box').eq(0).show();
                    } else if (productName.includes('foil')) {
                        row.find('.specification-box').eq(1).show();
                    } else if (productName.includes('matte')) {
                        row.find('.specification-box').eq(2).show();
                    }
                }
                else if (productName == 'Sticker print') {
                    row.find('.specification-box').eq(3).show();
                }
                else if (productName == 'Flyer Print') {
                    row.find('.specification-box').eq(4).show();
                }
                else if (productName == 'Tag Print') {
                    row.find('.specification-box').eq(5).show();
                }
                else if (productName == 'Business card Design' ||
                    productName == 'Any Other Design' ||
                    productName == 'Any Other Print' ||
                    productName == 'Delivery') {
                    row.find('.specification-box').eq(6).show();
                }
            });

            // Handle "Other" option in type dropdown
            $(document).on('change', '.type-select', function () {
                if ($(this).val() === 'Other') {
                    $(this).next('.custom-type-input').show();
                } else {
                    $(this).next('.custom-type-input').hide();
                }
            });

            // Handle "Other" option in thickness dropdown
            $(document).on('change', 'select[name*="[thikness]"]', function () {
                if ($(this).val() == 'Other') {
                    $(this).closest('.row').find('input[name*="[custom_thikness]"]').show();
                } else {
                    $(this).closest('.row').find('input[name*="[custom_thikness]"]').hide();
                }
            });

            // Handle "Other" checkbox in Print Type
            $(document).on('change', 'input[type="checkbox"][name*="[print_type][]"][value="Other"]', function () {
                if ($(this).is(':checked')) {
                    $(this).closest('.form-check').find('input[type="text"]').show();
                } else {
                    $(this).closest('.form-check').find('input[type="text"]').hide();
                }
            });

            // Initialize visibility when modal opens
            $('#editProductsModal').on('show.bs.modal', function () {
                $('#productDetailsBody tr').each(function () {
                    var row = $(this);
                    var productName = row.find('.product-select').val();

                    if (productName) {
                        // Hide all first
                        row.find('.specification-box').hide();

                        // Show the correct one
                        if (productName.includes('Business card Print')) {
                            if (productName.includes('full color')) {
                                row.find('.specification-box').eq(0).show();
                            } else if (productName.includes('foil')) {
                                row.find('.specification-box').eq(1).show();
                            } else if (productName.includes('matte')) {
                                row.find('.specification-box').eq(2).show();
                            }
                        }
                        else if (productName == 'Sticker print') {
                            row.find('.specification-box').eq(3).show();
                        }
                        else if (productName == 'Flyer Print') {
                            row.find('.specification-box').eq(4).show();
                        }
                        else if (productName == 'Tag Print') {
                            row.find('.specification-box').eq(5).show();
                        }
                        else if (productName == 'Business card Design' ||
                            productName == 'Any Other Design' ||
                            productName == 'Any Other Print' ||
                            productName == 'Delivery') {
                            row.find('.specification-box').eq(6).show();
                        }
                    }
                });

                // Initialize "Other" options
                $('.type-select').each(function () {
                    if ($(this).val() === 'Other') {
                        $(this).next('.custom-type-input').show();
                    }
                });

                $('select[name*="[thikness]"]').each(function () {
                    if ($(this).val() == 'Other') {
                        $(this).closest('.row').find('input[name*="[custom_thikness]"]').show();
                    }
                });

                $('input[type="checkbox"][name*="[print_type][]"][value="Other"]').each(function () {
                    if ($(this).is(':checked')) {
                        $(this).closest('.form-check').find('input[type="text"]').show();
                    }
                });
            });

            // Update the add product function to include all specification boxes
            $('#btnAddProduct').click(function () {
                var newIndex = $('#productDetailsBody tr').length;
                var newRow = `
    <tr>
        <td>
            <select class="form-control product-select" name="products[${newIndex}][product_name]" required>
                <option value="">Select Product</option>
                <?php foreach ($product as $val): ?>
                    <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="specification-column">
            <!-- Business card Print full color -->
            <div class="specification-box" style="display:none;">
                <div class="row">
                    <div class="col-md-12" style="color:green">Type</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][type]">
                            <option value="Standard Shape">Standard Shape</option>
                            <option value="Shape CUT">Shape CUT</option>
                            <option value="Shape">Shape</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Thikness</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][thikness]">
                            <option value="360gsm">360gsm</option>
                            <option value="760gsm (32pt)">760gsm (32pt)</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="products[${newIndex}][spec][custom_thikness]" style="margin-top:5px; display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Full color">
                            <label class="form-check-label">Full color</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Gold Foil">
                            <label class="form-check-label">Gold Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Silver Foil">
                            <label class="form-check-label">Silver Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Other">
                            <label class="form-check-label">Other</label>
                            <input type="text" class="form-control" name="products[${newIndex}][spec][custom_print_type]" style="margin-top:5px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Corners</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][corners]">
                            <option value="Square">Square</option>
                            <option value="Rounded">Rounded</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Business card Print foil -->
            <div class="specification-box" style="display:none;">
                <!-- Same structure as full color -->
                <div class="row">
                    <div class="col-md-12" style="color:green">Type</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][type]">
                            <option value="Standard Shape">Standard Shape</option>
                            <option value="Shape CUT">Shape CUT</option>
                            <option value="Shape">Shape</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Thikness</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][thikness]">
                            <option value="360gsm">360gsm</option>
                            <option value="760gsm (32pt)">760gsm (32pt)</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="products[${newIndex}][spec][custom_thikness]" style="margin-top:5px; display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Full color">
                            <label class="form-check-label">Full color</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Gold Foil">
                            <label class="form-check-label">Gold Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Silver Foil">
                            <label class="form-check-label">Silver Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Other">
                            <label class="form-check-label">Other</label>
                            <input type="text" class="form-control" name="products[${newIndex}][spec][custom_print_type]" style="margin-top:5px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Corners</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][corners]">
                            <option value="Square">Square</option>
                            <option value="Rounded">Rounded</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Business card Print matte -->
            <div class="specification-box" style="display:none;">
                <!-- Same structure as full color -->
                <div class="row">
                    <div class="col-md-12" style="color:green">Type</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][type]">
                            <option value="Standard Shape">Standard Shape</option>
                            <option value="Shape CUT">Shape CUT</option>
                            <option value="Shape">Shape</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Thikness</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][thikness]">
                            <option value="360gsm">360gsm</option>
                            <option value="760gsm (32pt)">760gsm (32pt)</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="products[${newIndex}][spec][custom_thikness]" style="margin-top:5px; display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Full color">
                            <label class="form-check-label">Full color</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Gold Foil">
                            <label class="form-check-label">Gold Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Silver Foil">
                            <label class="form-check-label">Silver Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Other">
                            <label class="form-check-label">Other</label>
                            <input type="text" class="form-control" name="products[${newIndex}][spec][custom_print_type]" style="margin-top:5px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Corners</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][corners]">
                            <option value="Square">Square</option>
                            <option value="Rounded">Rounded</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Sticker print -->
            <div class="specification-box" style="display:none;">
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Full color">
                            <label class="form-check-label">Full color</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Gold Foil">
                            <label class="form-check-label">Gold Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Silver Foil">
                            <label class="form-check-label">Silver Foil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="products[${newIndex}][spec][print_type][]" value="Other">
                            <label class="form-check-label">Other</label>
                            <input type="text" class="form-control" name="products[${newIndex}][spec][custom_print_type]" style="margin-top:5px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Flyer Print -->
            <div class="specification-box" style="display:none;">
                <div class="row">
                    <div class="col-md-12" style="color:red">Thikness</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][thikness]">
                            <option value="360gsm">360gsm</option>
                            <option value="760gsm (32pt)">760gsm (32pt)</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="products[${newIndex}][spec][custom_thikness]" style="margin-top:5px; display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][print_type]">
                            <option value="Full color">Full color</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Tag Print -->
            <div class="specification-box" style="display:none;">
                <div class="row">
                    <div class="col-md-12" style="color:red">Size</div>
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="products[${newIndex}][spec][size]">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Thikness</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][thikness]">
                            <option value="360gsm">360gsm</option>
                            <option value="760gsm (32pt)">760gsm (32pt)</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control" name="products[${newIndex}][spec][custom_thikness]" style="margin-top:5px; display:none;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Print Type</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][print_type]">
                            <option value="Full color">Full color</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="color:red">Finishes</div>
                    <div class="col-md-12">
                        <select class="form-control" name="products[${newIndex}][spec][finishes]">
                            <option value="Matte">Matte</option>
                            <option value="Velvet">Velvet</option>
                            <option value="Gloss">Gloss</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- For simple note products -->
            <div class="specification-box" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <textarea class="form-control" name="products[${newIndex}][spec][note]" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <input type="number" class="form-control quantity" name="products[${newIndex}][quantity]" value="1" min="1" required>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control price" name="products[${newIndex}][price]" value="0.00" required>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control total" name="products[${newIndex}][total]" value="0.00" readonly style="background-color: #f8f9fa;">
        </td>
        <td style="vertical-align: middle; text-align: center;">
            <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove product">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
                $('#productDetailsBody').append(newRow);
            });

            // Initialize visibility when modal opens
            $('#editProductsModal').on('show.bs.modal', function () {
                $('select[name*="[thikness]"]').each(function () {
                    if ($(this).val() == 'Other') {
                        $(this).closest('.row').find('input[name*="[custom_thikness]"]').show();
                    }
                });

                $('input[type="checkbox"][name*="[print_type][]"][value="Other"]').each(function () {
                    if ($(this).is(':checked')) {
                        $(this).closest('.form-check').find('input[type="text"]').show();
                    }
                });
            });

            // Remove product row
            $(document).on('click', '.remove-row', function () {
                if (confirm('Are you sure you want to remove this product?')) {
                    $(this).closest('tr').remove();
                    updateOrderSummary();
                }
            });

            // Save product details
            $('#btnSaveProducts').click(function () {
                var formData = [];
                var hasErrors = false;

                $('#productDetailsBody tr').each(function (index) {
                    var row = $(this);
                    var product = {
                        product_name: row.find('.product-select').val(),
                        quantity: row.find('.quantity').val(),
                        price: row.find('.price').val(),
                        total: row.find('.total').val(),
                        spec: {}
                    };

                    // Validate required fields
                    if (!product.product_name) {
                        alert('Please select a product for row ' + (index + 1));
                        hasErrors = true;
                        return false;
                    }

                    if (product.quantity <= 0 || isNaN(product.quantity)) {
                        alert('Please enter a valid quantity for row ' + (index + 1));
                        hasErrors = true;
                        return false;
                    }

                    if (product.price < 0 || isNaN(product.price)) {
                        alert('Please enter a valid price for row ' + (index + 1));
                        hasErrors = true;
                        return false;
                    }

                    // Get the visible specification box for this product
                    var specBox = row.find('.specification-box:visible');

                    if (specBox.length) {
                        // Handle all input types in the specification box
                        specBox.find('input, select, textarea').each(function () {
                            var input = $(this);
                            var name = input.attr('name');

                            // Extract the spec key from name (e.g., "products[0][spec][type]")
                            var matches = name.match(/\[spec\]\[(.*?)\]/);
                            if (matches && matches.length > 1) {
                                var specKey = matches[1];

                                // Handle checkboxes (like print type options)
                                if (input.is(':checkbox')) {
                                    if (!product.spec.print_type) {
                                        product.spec.print_type = [];
                                    }
                                    if (input.is(':checked')) {
                                        if (input.val() === 'Other') {
                                            // For "Other" checkbox, get the custom value
                                            product.spec.print_type.push(input.closest('.form-check').find('input[type="text"]').val());
                                        } else {
                                            product.spec.print_type.push(input.val());
                                        }
                                    }
                                }
                                // Handle select dropdowns
                                else if (input.is('select')) {
                                    if (input.val() === 'Other') {
                                        // For "Other" option, get the custom value
                                        product.spec[specKey] = input.closest('.row').find('input[type="text"]').val();
                                    } else {
                                        product.spec[specKey] = input.val();
                                    }
                                }
                                // Handle text inputs and textareas
                                else {
                                    product.spec[specKey] = input.val();
                                }
                            }
                        });

                        // Convert print_type array to string if it exists
                        if (product.spec.print_type && Array.isArray(product.spec.print_type)) {
                            product.spec.print_type = product.spec.print_type.join(', ');
                        }
                    }

                    formData.push(product);
                });

                if (hasErrors) return;

                // Show loading indicator
                $('#btnSaveProducts').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

                // Send data via AJAX
                $.ajax({
                    url: '../update_order_details.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        order_id: <?php echo $order_id; ?>,
                        products: formData,
                        advance_paid: <?php echo $advance_paid; ?>
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#ajaxModal .modal-body').html(
                                '<div class="alert alert-success">' +
                                response.message + '<br>New Total: Rs. ' + response.new_total.toFixed(2) +
                                '<br>New Due: Rs. ' + response.due.toFixed(2) + '</div>'
                            );
                            $('#ajaxModal').modal('show');

                            // Close the edit modal after a delay
                            setTimeout(function () {
                                $('#editProductsModal').modal('hide');
                                // Reload the page to show updated data
                                location.reload();
                            }, 1500);
                        } else {
                            $('#ajaxModal .modal-body').html(
                                '<div class="alert alert-danger">' + response.message + '</div>'
                            );
                            $('#ajaxModal').modal('show');
                            $('#btnSaveProducts').html('<i class="fas fa-save"></i> Save Changes').prop('disabled', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#ajaxModal .modal-body').html(
                            '<div class="alert alert-danger">Error: ' + error + '</div>'
                        );
                        $('#ajaxModal').modal('show');
                        $('#btnSaveProducts').html('<i class="fas fa-save"></i> Save Changes').prop('disabled', false);
                    }
                });
            });

            // Function to update order summary in modal footer
            function updateOrderSummary() {
                var subtotal = 0;
                $('.total').each(function () {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                $('#modalSubtotal').text('Rs. ' + subtotal.toFixed(2));
                $('#dueAmount').text('Rs. ' + (subtotal - originalAdvance).toFixed(2));
            }

            // Initialize the order summary
            updateOrderSummary();
        });

        $(document).on('change', '.type-select', function () {
            if ($(this).val() === 'Other') {
                $(this).next('.custom-type-input').show();
            } else {
                $(this).next('.custom-type-input').hide();
            }
        });

        // Initialize visibility when modal opens
        $('#editProductsModal').on('show.bs.modal', function () {
            $('.type-select').each(function () {
                if ($(this).val() === 'Other') {
                    $(this).next('.custom-type-input').show();
                }
            });
        });

        $(document).on('change', '.product-select', function () {
            var row = $(this).closest('tr');
            var productName = $(this).val();

            // Hide all specification boxes first
            row.find('.specification-box').hide();

            // Show the appropriate specification box based on product type
            if (productName.includes('Business card Print')) {
                if (productName.includes('full color')) {
                    row.find('.specification-box').eq(0).show();
                } else if (productName.includes('foil')) {
                    row.find('.specification-box').eq(1).show();
                } else if (productName.includes('matte')) {
                    row.find('.specification-box').eq(2).show();
                }
            }
            else if (productName == 'Sticker print') {
                row.find('.specification-box').eq(3).show();
            }
            else if (productName == 'Flyer Print') {
                row.find('.specification-box').eq(4).show();
            }
            else if (productName == 'Tag Print') {
                row.find('.specification-box').eq(5).show();
            }
            else if (productName == 'Business card Design' ||
                productName == 'Any Other Design' ||
                productName == 'Any Other Print' ||
                productName == 'Delivery') {
                row.find('.specification-box').eq(6).show();
            }
        });

        // Handle "Other" option selections
        $(document).on('change', 'select[name*="[thikness]"], select[name*="[print_type]"]', function () {
            if ($(this).val() == 'Other') {
                $(this).next('input[type="text"]').show();
            } else {
                $(this).next('input[type="text"]').hide();
            }
        });

        // Handle checkbox "Other" option
        $(document).on('change', 'input[type="checkbox"][name*="[print_type][]"][value="Other"]', function () {
            if ($(this).is(':checked')) {
                $(this).closest('.form-check').find('input[type="text"]').show();
            } else {
                $(this).closest('.form-check').find('input[type="text"]').hide();
            }
        });

        // Update the save function to handle specifications
        $('#btnSaveProducts').click(function () {
            var formData = [];

            $('#productDetailsBody tr').each(function (index) {
                var row = $(this);
                var product = {
                    product_name: row.find('.product-select').val(),
                    quantity: row.find('.quantity').val(),
                    price: row.find('.price').val(),
                    total: row.find('.total').val(),
                    spec: {}
                };

                // Handle Type field
                var typeValue = row.find('.type-select').val();
                if (typeValue === 'Other') {
                    product.spec.type = row.find('.custom-type-input').val();
                } else {
                    product.spec.type = typeValue;
                }

                // Collect specifications based on product type
                var productType = product.product_name;

                if (productType.includes('Business card Print')) {
                    product.spec = {
                        type: row.find('select[name*="[spec][type]"]').val(),
                        thikness: row.find('select[name*="[spec][thikness]"]').val() == 'Other' ?
                            row.find('input[name*="[spec][custom_thikness]"]').val() :
                            row.find('select[name*="[spec][thikness]"]').val(),
                        print_type: [],
                        finishes: row.find('select[name*="[spec][finishes]"]').val(),
                        corners: row.find('select[name*="[spec][corners]"]').val()
                    };

                    // Handle print type checkboxes
                    row.find('input[name*="[spec][print_type][]"]:checked').each(function () {
                        var val = $(this).val();
                        if (val == 'Other') {
                            product.spec.print_type.push(row.find('input[name*="[spec][custom_print_type]"]').val());
                        } else {
                            product.spec.print_type.push(val);
                        }
                    });
                    product.spec.print_type = product.spec.print_type.join(', ');
                }
                else if (productType == 'Sticker print') {
                    product.spec = {
                        print_type: [],
                        finishes: row.find('select[name*="[spec][finishes]"]').val()
                    };

                    // Handle print type checkboxes
                    row.find('input[name*="[spec][print_type][]"]:checked').each(function () {
                        var val = $(this).val();
                        if (val == 'Other') {
                            product.spec.print_type.push(row.find('input[name*="[spec][custom_print_type]"]').val());
                        } else {
                            product.spec.print_type.push(val);
                        }
                    });
                    product.spec.print_type = product.spec.print_type.join(', ');
                }
                else if (productType == 'Flyer Print' || productType == 'Tag Print') {
                    product.spec = {
                        thikness: row.find('select[name*="[spec][thikness]"]').val() == 'Other' ?
                            row.find('input[name*="[spec][custom_thikness]"]').val() :
                            row.find('select[name*="[spec][thikness]"]').val(),
                        print_type: row.find('select[name*="[spec][print_type]"]').val(),
                        finishes: row.find('select[name*="[spec][finishes]"]').val()
                    };

                    if (productType == 'Tag Print') {
                        product.spec.size = row.find('input[name*="[spec][size]"]').val();
                    }
                }
                else if (productType == 'Business card Design' ||
                    productType == 'Any Other Design' ||
                    productType == 'Any Other Print' ||
                    productType == 'Delivery') {
                    product.spec = {
                        note: row.find('textarea[name*="[spec][note]"]').val()
                    };
                }

                formData.push(product);
            });

            // Send data via AJAX
            $.ajax({
                url: 'update_order_details.php',
                type: 'POST',
                data: {
                    order_id: <?php echo $order_id; ?>,
                    products: formData,
                    advance_paid: <?php echo $advance_paid; ?>
                },
                success: function (response) {
                    try {
                        var result = JSON.parse(response);
                        if (result.success) {
                            $('#ajaxModal .modal-body').html(
                                '<div class="alert alert-success">' +
                                result.message + '<br>New Total: Rs. ' + result.new_total.toFixed(2) +
                                '<br>New Due: Rs. ' + result.due.toFixed(2) + '</div>'
                            );
                            $('#ajaxModal').modal('show');
                            $('#editProductsModal').modal('hide');

                            // Update the main view after a short delay
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#ajaxModal .modal-body').html(
                                '<div class="alert alert-danger">' + result.message + '</div>'
                            );
                            $('#ajaxModal').modal('show');
                        }
                    } catch (e) {
                        $('#ajaxModal .modal-body').html(
                            '<div class="alert alert-danger">Error processing response</div>'
                        );
                        $('#ajaxModal').modal('show');
                    }
                },
                error: function (xhr, status, error) {
                    $('#ajaxModal .modal-body').html(
                        '<div class="alert alert-danger">Error: ' + error + '</div>'
                    );
                    $('#ajaxModal').modal('show');
                }
            });
        });


        function copyToClipboard(button) {
            const input = button.closest('.input-group').querySelector('input');
            input.select();
            document.execCommand('copy');

            // Optional: Show a tooltip or change icon briefly
            const icon = button.querySelector('i');
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');

            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 1000);
        }
    </script>
</body>

</html>