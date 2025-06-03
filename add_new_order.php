<?php
session_start();
include('db.php');

// Define your product list
$product = array(
    'Business card Design',
    'Business card Print full color',
    'Business card Print foil',
    'Business card Print matte',
    'Sticker print',
    'Flyer Print',
    'Tag Print',
    'Any Other Design',
    'Any Other Print',
    'Delivery'
);

// Make sure order_id and order_total_amount come from somewhere, e.g., POST
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$order_total_amount = isset($_POST['order_total_amount']) ? floatval($_POST['order_total_amount']) : 0.00;

// Apply discount
$discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0.00;
$order_total_amount -= $discount_amount;

// Update database
if ($order_id > 0) {
    $statement = $pdo->prepare("UPDATE pixel_media_order SET order_total_amount = ?, discount_amount = ? WHERE order_id = ?");
    $statement->execute(array($order_total_amount, $discount_amount, $order_id));
} else {
    echo "Invalid Order ID.";
}

function saveQuotation($pdo, $order_id, $customerDetails, $items, $subtotal, $discount, $total, $design_charges)
{
    // Generate a unique quotation number
    $quotation_number = 'QTN-' . date('Ymd') . '-' . strtoupper(uniqid());

    // Prepare the data
    $valid_until = date('Y-m-d', strtotime('+14 days'));
    $quotation_items = json_encode($items);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO pixel_media_quotations 
        (quotation_number, order_id, company_name, contact_person, phone, email, 
         delivery_address, subtotal, discount, total_amount, valid_until, quotation_items, design_charges)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $quotation_number,
        $order_id,
        $customerDetails['company_name'],
        $customerDetails['contact_person'],
        $customerDetails['phone'],
        $customerDetails['email'],
        $customerDetails['delivery_address'],
        $subtotal,
        $discount,
        $total,
        $valid_until,
        $quotation_items,
        $design_charges
    ]);

    return $quotation_number;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Infive Print</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        /* Hide the navbar by default */
        .main-navbar {
            display: none;
        }

        .navbar-bg {
            display: none;
        }

        /* Show the navbar on screens smaller than 992px (bootstrap's lg breakpoint) */
        @media (max-width: 991.98px) {
            .main-navbar {
                display: flex;
            }

            .navbar-bg {
                display: flex;
            }

            /* Ensure the main content is pushed down when the navbar is visible */

            .main-content {
                padding-top: 140px;
                /* Adjust according to your navbar height */
            }
        }

        /* Ensure the mobile menu is positioned correctly */
        .navbar-bg,
        .main-navbar {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .star {
            color: red;
        }

        .row {
            line-height: 35px;

        }

        .frmRadio {
            width: 20px;
            height: 20px;
        }

        .frmCheck {
            width: 20px;
            height: 20px;
        }

        .heading_16 {
            font-size: 16px;
            font-weight: bold;
        }

        .btn-success {
            background-color: #1BA664;
            /* Custom green color */
            border-color: #1BA664;
            /* Custom green color */
            color: #fff;
            /* Ensure text color remains white */
        }

        .star {
            color: red;
            font-weight: bold;
        }

        .specs-heading {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 4px solid #1BA664;
        }

        /* Add to your existing style section */
        .remove-row {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .remove-row:hover {
            transform: scale(1.1);
            background-color: #c82333;
        }

        .product-row {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            align-items: center;
        }

        .product-row:last-child {
            border-bottom: none;
        }

        /* New styles for quotation */
        .quotation-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .quotation-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1BA664;
            padding-bottom: 10px;
        }

        .quotation-title {
            color: #1BA664;
            font-size: 24px;
            font-weight: bold;
        }

        .quotation-details {
            margin-bottom: 20px;
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

        .quotation-totals {
            text-align: right;
            margin-top: 20px;
        }

        .quotation-totals table {
            margin-left: auto;
            border-collapse: collapse;
        }

        .quotation-totals td {
            padding: 8px 15px;
            text-align: right;
        }

        .quotation-totals .total-row {
            font-weight: bold;
            border-top: 2px solid #1BA664;
        }

        .print-button {
            background-color: #1BA664;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .print-button:hover {
            background-color: #168955;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background-color: white;
                font-size: 12pt;
            }

            .quotation-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>


</head>

<body>
    <div class="col-md-12 offset-md-0">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg" style="background-color: #1BA664;color: white;"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <!-- Navbar content -->
            </nav>
            <?php include('left_menu.php'); ?>
            <div class="main-content" style="background-color:#F7F9F9;">
                <div>
                        <h1>Dashboard Work - Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
                    </div>
                <br>
                <hr><br>
                <?php
                $recordAdded = false;
                if (isset($_POST['btnCreateOrder'])) {
                    extract($_POST);
                    $statement = $pdo->prepare("
                        INSERT INTO pixel_media_order (
                            company_name, contact_person, phone, email, delivery_address, 
                            payment_type, payment_amount, order_status, advance_receive_date, 
                            current_status, order_total_amount, discount_amount
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    try {
                        foreach ($payment_type as $ptype) {
                            $advance_receive_date = '0000-00-00';
                            if ($ptype == 'Advance Paid') {
                                $payment_amount = $advance_payment_amount;
                                $order_status = 'ADVANCE RECEIVED';
                                $advance_receive_date = date('Y-m-d');
                                $current_status = 'Active';
                            }
                            if ($ptype == 'Full Payment Paid') {
                                $payment_amount = $full_payment_amount;
                                $order_status = 'FULL PAYMENT RECEIVED';
                                $current_status = 'Active';
                            }
                            if ($ptype == 'NO PAYMENT YET') {
                                $current_status = 'None Paid';
                                $payment_amount = 0;
                                $order_status = 'ORDER PLACED';
                            }
                            if ($ptype == 'COD') {
                                $payment_amount = $total_order_amount - $due_amount;
                                if ($payment_amount == 0)
                                    $order_status = 'ORDER PLACED';
                                else
                                    $order_status = 'ADVANCE RECEIVED';
                            }
                        }
                        $payment_type = implode(',', $payment_type);
                        // Ensure discount amount is included in the insert query
                        $statement->execute(array(
                            $company_name,
                            $contact_person,
                            $phone,
                            $email,
                            $delivery_address,
                            $payment_type,
                            $payment_amount,
                            $order_status,
                            $advance_receive_date,
                            $current_status,
                            $total_order_amount,
                            $discount_amount
                        ));
                        $id = $pdo->lastInsertId();
                        $order_id = generateUniqueRandomNumber($pdo);
                        $statement = $pdo->prepare("UPDATE pixel_media_order SET order_id =? WHERE id = ?");
                        $statement->execute(array($order_id, $id));
                        $product_info = array();
                        $i = 0;
                        $order_total_amount = 0;


                        foreach ($products as $product) {
                            unset($product_info);
                            $product_specification = '-';

                            if ($product == 'Business card Print full color') {
                                if ($business_card_print_type_full == 'Shape')
                                    $business_card_print_type_full = $business_card_print_txtType_full[0];
                                if ($business_card_print_thikness_full == 'Other')
                                    $business_card_print_thikness_full = $business_card_print_txtThikness_full[0];

                                $product_info['Type'] = $business_card_print_type_full;
                                $product_info['Thikness'] = $business_card_print_thikness_full;

                                $search = 'Other';
                                $key = array_search($search, $business_card_print_print_type_full);
                                if (!empty($key)) {

                                    $business_card_print_print_type_full[$key] = $business_card_print_txtPrintType_full[0];
                                }

                                $product_info['Print_Type'] = implode(', ', $business_card_print_print_type_full);

                                $product_info['Finishes'] = $business_card_print_finishes_full;
                                $product_info['Corners'] = $business_card_print_corners_full;
                                $product_specification = json_encode($product_info);

                            }

                            if ($product == 'Business card Print foil') {
                                if ($business_card_print_type_foil == 'Shape')
                                    $business_card_print_type_foil = $business_card_print_txtType_foil[0];
                                if ($business_card_print_thikness_foil == 'Other')
                                    $business_card_print_thikness_foil = $business_card_print_txtThikness_foil[0];

                                $product_info['Type'] = $business_card_print_type_foil;
                                $product_info['Thikness'] = $business_card_print_thikness_foil;

                                $search = 'Other';
                                $key = array_search($search, $business_card_print_print_type_foil);
                                if (!empty($key)) {

                                    $business_card_print_print_type_foil[$key] = $business_card_print_txtPrintType_foil[0];
                                }

                                $product_info['Print_Type'] = implode(', ', $business_card_print_print_type_foil);

                                $product_info['Finishes'] = $business_card_print_finishes_foil;
                                $product_info['Corners'] = $business_card_print_corners_foil;
                                $product_specification = json_encode($product_info);

                            }

                            if ($product == 'Business card Print matte') {
                                if ($business_card_print_type_matte == 'Shape')
                                    $business_card_print_type_matte = $business_card_print_txtType_matte[0];
                                if ($business_card_print_thikness_matte == 'Other')
                                    $business_card_print_thikness_matte = $business_card_print_txtThikness_matte[0];

                                $product_info['Type'] = $business_card_print_type_matte;
                                $product_info['Thikness'] = $business_card_print_thikness_matte;

                                $search = 'Other';
                                $key = array_search($search, $business_card_print_print_type_matte);
                                if (!empty($key)) {

                                    $business_card_print_print_type_matte[$key] = $business_card_print_txtPrintType_matte[0];
                                }

                                $product_info['Print_Type'] = implode(', ', $business_card_print_print_type_matte);

                                $product_info['Finishes'] = $business_card_print_finishes_matte;
                                $product_info['Corners'] = $business_card_print_corners_matte;
                                $product_specification = json_encode($product_info);

                            }


                            if ($product == 'Sticker print') {

                                $search = 'Other';
                                $key = array_search($search, $sticker_print_print_type);
                                if ($key !== false) {
                                    $sticker_print_print_type[$key] = $sticker_print_txtPrintType[0];
                                }

                                $product_info['Print_Type'] = implode(', ', $sticker_print_print_type);

                                //$product_info['Print_Type'] = $sticker_print_print_type;
                                $product_info['Finishes'] = $sticker_print_finishes;
                                $product_specification = json_encode($product_info);
                            }

                            if ($product == 'Flyer Print') {
                                if ($flyer_print_thikness == 'Other')
                                    $flyer_print_thikness = $flyer_print_txtThikness[0];

                                $product_info['Thikness'] = $flyer_print_thikness;
                                $product_info['Print_Type'] = $flyer_print_print_type;
                                $product_info['Finishes'] = $flyer_print_finishes;
                                $product_specification = json_encode($product_info);

                            }

                            if ($product == 'Tag Print') {
                                if ($tag_print_thikness == 'Other')
                                    $tag_print_thikness = $tag_print_txtThikness[0];

                                $product_info['Size'] = $tag_print_txtsize;
                                $product_info['Thikness'] = $tag_print_thikness;
                                $product_info['Print_Type'] = $tag_print_print_type;
                                $product_info['Finishes'] = $tag_print_finishes;
                                $product_specification = json_encode($product_info);
                            }

                            if ($product == 'Any Other Design') {
                                $product_info['note'] = $any_other_design[0];
                                $product_specification = json_encode($product_info);
                            }

                            if ($product == 'Any Other Print') {
                                $product_info['note'] = $any_other_print[0];
                                $product_specification = json_encode($product_info);
                            }

                            if ($product == 'Delivery') {
                                $product_specification = '';
                            }

                            if ($product == 'Business card Design') {
                                $product_info['note'] = $txt_business_card_design[0];
                                $product_specification = json_encode($product_info);
                            }


                            $order_total_amount += $total[$i];
                            $statement = $pdo->prepare("INSERT INTO pixel_media_order_details (order_id, product_name, product_specification, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)");
                            try {
                                $statement->execute(array($order_id, $product, $product_specification, $quantity[$i], $price[$i], $total[$i]));
                                $i++;
                            } catch (PDOException $e) {
                                // Handle any exceptions that occur during the execution
                            }
                            $statement = $pdo->prepare("UPDATE pixel_media_order SET order_total_amount =? WHERE order_id = ?");
                            $statement->execute(array($order_total_amount, $order_id));
                            $recordAdded = true;
                            echo "<script>
                            $(document).ready(function() {
                                $('#confirmationModal').modal('show');
                            });
                        </script>";
                        }
                        //echo "New record created successfully";
                
                        $lastElement = end($Notification_type);

                        $order_page_link = BASE_URL . '/order/' . $order_id;
                        $advance_amount = $_POST['advance_payment_amount'];
                        $full_amount = $_POST['full_payment_amount'];

                        //CODE FOR SENDING MESSAGE
                        if (strtoupper($lastElement) == 'ORDER CREATED') {
                            $sms = "Your print order has been successfully created. Please check the process here: $order_page_link. - Infive Print";

                        }


                        if (strtoupper($lastElement) == 'ADVANCE RECEIVED') {
                            $sms = "Your print order has been successfully created and Advance payment of RS.$advance_amount received! Track your order process here: $order_page_link Thank you. - Infive Print";

                        }

                        if (strtoupper($lastElement) == 'FULL PAYMENT RECEIVED') {
                            $sms = "Your print order has been successfully created and full payment of RS.$full_amount received! Track your order process here: $order_page_link Thank you. - Infive Print";

                        }


                        //print $sms;
                
                        $statement = $pdo->prepare(" SELECT * FROM pixel_media_order  WHERE order_id = ?");
                        $statement->execute(array($order_id));
                        $order = $statement->fetch(PDO::FETCH_ASSOC);
                        $MSISDN = $order['phone'];
                        //print $MSISDN;
                
                        //$MSISDN = '0775524866';
                        $SRC = 'InfivePrint';
                        $MESSAGE = urldecode($sms);
                        $AUTH = "2001|d904j2TA6FS18E1XsQIyo8vTyqgfegcvfUsFimjZ";  //Replace your Access Token
                
                        $msgdata = array("recipient" => $MSISDN, "sender_id" => $SRC, "message" => $MESSAGE);

                        $curl = curl_init();

                        //IF you are running in locally and if you don't have https/SSL. then uncomment bellow two lines
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://sms.send.lk/api/v3/sms/send",
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => json_encode($msgdata),
                            CURLOPT_HTTPHEADER => array(
                                "accept: application/json",
                                "authorization: Bearer $AUTH",
                                "cache-control: no-cache",
                                "content-type: application/x-www-form-urlencoded",
                            ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);
                        if ($err) {
                            // echo "cURL Error #:" . $err;
                        } else {
                            //echo $response;
                        }
                        //
                
                    } catch (PDOException $e) {
                        //Handle any exceptions that occur during the execution
                        //echo "Error From Main: " . $e->getMessage();
                    }


                }
                ?>

                <section class="section">
                    <form method="post" accept="./addOrder">
                        <div class="section-body">

                            <div id="alertMessage"></div>
                            <div class="row">
                                <div class="col-md-12"
                                    style="border: 1px solid black;border-radius:5px;background-color: #FFF;padding-left:50px">
                                    <div>
                                        <h1>Add New Order</h1>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4" style="color: #1BA664;font-weight: bold;">CUSTOMER DETAILS
                                        </div>

                                    </div><br>

                                    <div class="row" style="font-size: 16px;font-weight: bold;">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    Company Name <span class='star'>*</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" name="company_name" class="form-control"
                                                        style="height:30px;" required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-5">
                                                    Contact Person
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" name="contact_person" class="form-control"
                                                        style="height:30px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-5">
                                                    Phone <span class='star'>*</span>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" name="phone" class="form-control"
                                                        style="height:30px;" required pattern="\d{10}"
                                                        title="Please enter exactly 10 digits" maxlength="10">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-5">
                                                    Email
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="email" name="email" class="form-control"
                                                        style="height:30px;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="col-md-12">Delivery Address<span class='star'>*</span></div>
                                            <div class="col-md-12">
                                                <textarea name="delivery_address" class="form-control" rows="50"
                                                    cols="30" required style="height:110px"></textarea>
                                            </div>
                                        </div>


                                    </div><br>

                                    <div id="div_add_more_product">
                                        <div id="div_product">
                                            <div class="row" style="font-size: 16px;font-weight: bold;">
                                                <div class="col-md-3">Product</div>
                                                <div class="col-md-2">Qty</div>
                                                <div class="col-md-3">Unit price</div>
                                                <div class="col-md-3">Total</div>
                                            </div>

                                            <div class="row" style="font-size: 16px;font-weight: bold;">

                                                <div class="col-md-3">
                                                    <select class="form-control" name="products[]" id="productCombo"
                                                        onchange="get_specification(this.value)" style="width: 100%;">
                                                        <option value="">Select</option>
                                                        <?php
                                                        foreach ($product as $val) {
                                                            echo "<option value='$val'>$val</option>";
                                                        }

                                                        $statement = $pdo->prepare("SELECT * FROM pixel_media_product");
                                                        $statement->execute();
                                                        $order_product = $statement->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($order_product as $row) {
                                                            $val = $row['product_name'];
                                                            echo "<option value='$val'>$val</option>";
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                                <div class="col-md-2"><input type="text" name="quantity[]"
                                                        class="form-control quantity" style="width:100px;height: 40px;">
                                                </div>
                                                <div class="col-md-3"><input type="text" name="price[]"
                                                        class="form-control price" style="width:100px;height: 40px;">
                                                </div>
                                                <div class="col-md-3"><input type="text" name="total[]"
                                                        class="form-control total" style="width:150px;height: 40px;"
                                                        readonly></div>
                                            </div>


                                        </div>


                                        <div id="div_product_spec_1"></div>

                                    </div>

                                    <div class="row" style="padding-top: 10px;">
                                        <div class="col-md-12"><input type="button" class="btn btn-success"
                                                value="Add More Product" onclick="add_more_product()"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8" align="right">Total Order Amount</div>
                                        <div class="col-md-3 heading_16" id="div_total_amount"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8" align="right">Discount Amount</div>
                                        <div class="col-md-3">
                                            <input type="text" name="discount_amount" id="discount_amount"
                                                class="form-control" style="width:150px;height: 40px;" value="0">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8" align="right" style="font-weight: bold; font-size: 15px;">
                                            Final Amount After Discount
                                        </div>
                                        <div class="col-md-3 heading_16" id="div_final_amount"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8" align="right">
                                            If there are more additional fees, add them.
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="design_charges" id="design_charges"
                                                class="form-control" style="width:150px;height: 40px;" value="0">
                                        </div>
                                    </div>



                                    <!-- BUSINESS CARD DESIGN SPECIFICATION START -->
                                    <div class="row" id="business_card_design" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;padding-top: 10px;padding-bottom: 10px;margin-bottom: 10px;">

                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    Add Note &nbsp;
                                                    <input type="text" name="txt_business_card_design[]"
                                                        style="width:150px;height: 35px;" class="form-control">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- BUSINESS CARD DESIGN SPECIFICATION END -->

                                    <!-- ANY OTHER DESIGN SPECIFICATION START -->
                                    <div class="row" id="any_other_design" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;padding-top: 10px;padding-bottom: 10px;margin-bottom: 10px;">

                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    Add Note &nbsp;
                                                    <input type="text" name="txt_any_other_design[]"
                                                        style="width:150px;height: 35px;" class="form-control">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- ANY OTHER DESIGN SPECIFICATION END -->


                                    <!-- ANY OTHER PRINT SPECIFICATION START -->
                                    <div class="row" id="any_other_print" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;padding-top: 10px;padding-bottom: 10px;margin-bottom: 10px;">

                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    Add Note &nbsp;
                                                    <input type="text" name="txt_any_other_print[]"
                                                        style="width:150px;height: 35px;" class="form-control">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- ANY OTHER PRINT SPECIFICATION END -->




                                    <!-- BUSINESS CARD PRINT  FULL COLOR SPECIFICATION START -->
                                    <div class="row" id="business_card_print_full" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:green">Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_type_full"
                                                        value="Standard Shape" class="form-control frmRadio">&nbsp;
                                                    Standard Shape &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_full"
                                                        value="Shape CUT" class="form-control frmRadio">&nbsp; Shape CUT
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_full"
                                                        value="Shape" class="form-control frmRadio">&nbsp;&nbsp; Shape
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtType_full[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Thikness</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_thikness_full"
                                                        value="360gsm" class="form-control frmRadio">&nbsp; 360gsm
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_full"
                                                        value="760gsm (32pt)" class="form-control frmRadio">&nbsp;
                                                    760gsm (32pt) &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_full"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtThikness_full[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="checkbox" name="business_card_print_print_type_full[]"
                                                        value="Full color" class="form-control frmRadio">&nbsp; Full
                                                    color &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_full[]"
                                                        value="Gold Foil" class="form-control frmRadio">&nbsp; Gold Foil
                                                    &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_full[]"
                                                        value="Silver Foil" class="form-control frmRadio">&nbsp; Silver
                                                    Foil &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_full[]"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtPrintType_full[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_finishes_full"
                                                        value="Matte" class="form-control frmRadio">&nbsp; Matte
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_full"
                                                        value="Velvet" class="form-control frmRadio">&nbsp; Velvet
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_full"
                                                        value="Gloss" class="form-control frmRadio">&nbsp; Gloss
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_full"
                                                        value="None" class="form-control frmRadio">&nbsp;&nbsp; None
                                                    &nbsp;

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Corners</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_corners_full"
                                                        value="Square" class="form-control frmRadio">&nbsp; Square
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_corners_full"
                                                        value="Rounded" class="form-control frmRadio">&nbsp; Rounded
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- BUSINESS CARD PRINT FULL SPECIFICATION END -->

                                    <!-- BUSINESS CARD PRINT  FOIL COLOR SPECIFICATION START -->
                                    <div class="row" id="business_card_print_foil" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:green">Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_type_foil"
                                                        value="Standard Shape" class="form-control frmRadio">&nbsp;
                                                    Standard Shape &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_foil"
                                                        value="Shape CUT" class="form-control frmRadio">&nbsp; Shape CUT
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_foil"
                                                        value="Shape" class="form-control frmRadio">&nbsp;&nbsp; Shape
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtType_foil[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Thikness</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_thikness_foil"
                                                        value="360gsm" class="form-control frmRadio">&nbsp; 360gsm
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_foil"
                                                        value="760gsm (32pt)" class="form-control frmRadio">&nbsp;
                                                    760gsm (32pt) &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_foil"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtThikness_foil[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="checkbox" name="business_card_print_print_type_foil[]"
                                                        value="Full color" class="form-control frmRadio">&nbsp; Full
                                                    color &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_foil[]"
                                                        value="Gold Foil" class="form-control frmRadio">&nbsp; Gold Foil
                                                    &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_foil[]"
                                                        value="Silver Foil" class="form-control frmRadio">&nbsp; Silver
                                                    Foil &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_foil[]"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtPrintType_foil[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_finishes_foil"
                                                        value="Matte" class="form-control frmRadio">&nbsp; Matte
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_foil"
                                                        value="Velvet" class="form-control frmRadio">&nbsp; Velvet
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_foil"
                                                        value="Gloss" class="form-control frmRadio">&nbsp; Gloss
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_foil"
                                                        value="None" class="form-control frmRadio">&nbsp;&nbsp; None
                                                    &nbsp;

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Corners</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_corners_foil"
                                                        value="Square" class="form-control frmRadio">&nbsp; Square
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_corners_foil"
                                                        value="Rounded" class="form-control frmRadio">&nbsp; Rounded
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- BUSINESS CARD PRINT FOIL SPECIFICATION END -->

                                    <!-- BUSINESS CARD PRINT  MATTE COLOR SPECIFICATION START -->
                                    <div class="row" id="business_card_print_matte" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:green">Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_type_matte"
                                                        value="Standard Shape" class="form-control frmRadio">&nbsp;
                                                    Standard Shape &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_matte"
                                                        value="Shape CUT" class="form-control frmRadio">&nbsp; Shape CUT
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_type_matte"
                                                        value="Shape" class="form-control frmRadio">&nbsp;&nbsp; Shape
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtType_matte[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Thikness</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_thikness_matte"
                                                        value="360gsm" class="form-control frmRadio">&nbsp; 360gsm
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_matte"
                                                        value="760gsm (32pt)" class="form-control frmRadio">&nbsp;
                                                    760gsm (32pt) &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_thikness_matte"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtThikness_matte[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="checkbox" name="business_card_print_print_type_matte[]"
                                                        value="Full color" class="form-control frmRadio">&nbsp; Full
                                                    color &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_matte[]"
                                                        value="Gold Foil" class="form-control frmRadio">&nbsp; Gold Foil
                                                    &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_matte[]"
                                                        value="Silver Foil" class="form-control frmRadio">&nbsp; Silver
                                                    Foil &nbsp;&nbsp;

                                                    <input type="checkbox" name="business_card_print_print_type_matte[]"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="business_card_print_txtPrintType_matte[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_finishes_matte"
                                                        value="Matte" class="form-control frmRadio">&nbsp; Matte
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_matte"
                                                        value="Velvet" class="form-control frmRadio">&nbsp; Velvet
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_matte"
                                                        value="Gloss" class="form-control frmRadio">&nbsp; Gloss
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_finishes_matte"
                                                        value="None" class="form-control frmRadio">&nbsp;&nbsp; None
                                                    &nbsp;

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Corners</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="business_card_print_corners_matte"
                                                        value="Square" class="form-control frmRadio">&nbsp; Square
                                                    &nbsp;&nbsp;

                                                    <input type="radio" name="business_card_print_corners_matte"
                                                        value="Rounded" class="form-control frmRadio">&nbsp; Rounded
                                                    &nbsp;&nbsp;

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- BUSINESS CARD PRINT MATTE SPECIFICATION END -->

                                    <!-- STICKER PRINT SPECIFICATION START -->
                                    <div class="row" id="sticker_print" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="checkbox" name="sticker_print_print_type[]"
                                                        value="Full color" class="form-control frmRadio">&nbsp; Full
                                                    color &nbsp;&nbsp;

                                                    <input type="checkbox" name="sticker_print_print_type[]"
                                                        value="Gold Foil" class="form-control frmRadio">&nbsp; Gold Foil
                                                    &nbsp;&nbsp;

                                                    <input type="checkbox" name="sticker_print_print_type[]"
                                                        value="Silver Foil" class="form-control frmRadio">&nbsp; Silver
                                                    Foil &nbsp;&nbsp;

                                                    <input type="checkbox" name="sticker_print_print_type[]"
                                                        value="Other" class="form-control frmRadio">&nbsp;&nbsp; Other
                                                    &nbsp;

                                                    <input type="text" name="sticker_print_txtPrintType[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="sticker_print_finishes" value="Matte"
                                                        class="form-control frmRadio">&nbsp; Matte &nbsp;&nbsp;

                                                    <input type="radio" name="sticker_print_finishes" value="Velvet"
                                                        class="form-control frmRadio">&nbsp; Velvet &nbsp;&nbsp;

                                                    <input type="radio" name="sticker_print_finishes" value="Gloss"
                                                        class="form-control frmRadio">&nbsp; Gloss &nbsp;&nbsp;

                                                    <input type="radio" name="sticker_print_finishes" value="None"
                                                        class="form-control frmRadio">&nbsp;&nbsp; None &nbsp;

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <!-- STICKER PRINT SPECIFICATION END -->


                                    <!-- FLYER PRINT SPECIFICATION START -->
                                    <div class="row" id="flyer_print" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Thikness</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="flyer_print_thikness" value="360gsm"
                                                        class="form-control frmRadio">&nbsp; 360gsm &nbsp;&nbsp;

                                                    <input type="radio" name="flyer_print_thikness"
                                                        value="760gsm (32pt)" class="form-control frmRadio">&nbsp;
                                                    760gsm (32pt) &nbsp;&nbsp;

                                                    <input type="radio" name="flyer_print_thikness" value="Other"
                                                        class="form-control frmRadio">&nbsp;&nbsp; Other &nbsp;

                                                    <input type="text" name="flyer_print_txtThikness[]"
                                                        class="form-control" style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="flyer_print_print_type" value="Full color"
                                                        class="form-control frmRadio">&nbsp; Full color &nbsp;&nbsp;


                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="flyer_print_finishes" value="Matte"
                                                        class="form-control frmRadio">&nbsp; Matte &nbsp;&nbsp;

                                                    <input type="radio" name="flyer_print_finishes" value="Velvet"
                                                        class="form-control frmRadio">&nbsp; Velvet &nbsp;&nbsp;

                                                    <input type="radio" name="flyer_print_finishes" value="Gloss"
                                                        class="form-control frmRadio">&nbsp; Gloss &nbsp;&nbsp;

                                                    <input type="radio" name="flyer_print_finishes" value="None"
                                                        class="form-control frmRadio">&nbsp;&nbsp; None &nbsp;

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <!-- FLYER PRINT SPECIFICATION END -->


                                    <!-- TAG PRINT SPECIFICATION START -->
                                    <div class="row" id="tag_print" style="display:none;">
                                        <div class="col-md-6"
                                            style="border:1px solid black;border-radius: 5px;background-color: white;">


                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Size</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    Type Size&nbsp;&nbsp;
                                                    <input type="text" name="tag_print_txtsize" class="form-control"
                                                        style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Thikness</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="tag_print_thikness" value="360gsm"
                                                        class="form-control frmRadio">&nbsp; 360gsm &nbsp;&nbsp;

                                                    <input type="radio" name="tag_print_thikness" value="760gsm (32pt)"
                                                        class="form-control frmRadio">&nbsp; 760gsm (32pt) &nbsp;&nbsp;

                                                    <input type="radio" name="tag_print_thikness" value="Other"
                                                        class="form-control frmRadio">&nbsp;&nbsp; Other &nbsp;

                                                    <input type="text" name="tag_print_txtThikness" class="form-control"
                                                        style="width:150px;height:35px;">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Print Type</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="tag_print_print_type" value="Full color"
                                                        class="form-control frmRadio">&nbsp; Full color &nbsp;&nbsp;


                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" style="color:red">Finishes</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="display:flex;align-items: center;">
                                                    <input type="radio" name="tag_print_finishes" value="Matte"
                                                        class="form-control frmRadio">&nbsp; Matte &nbsp;&nbsp;

                                                    <input type="radio" name="tag_print_finishes" value="Velvet"
                                                        class="form-control frmRadio">&nbsp; Velvet &nbsp;&nbsp;

                                                    <input type="radio" name="tag_print_finishes" value="Gloss"
                                                        class="form-control frmRadio">&nbsp; Gloss &nbsp;&nbsp;

                                                    <input type="radio" name="tag_print_finishes" value="None"
                                                        class="form-control frmRadio">&nbsp;&nbsp; None &nbsp;

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <!-- TAG PRINT SPECIFICATION END -->

                                    <div class="row" style="font-size: 20px;font-weight: bold;margin-bottom: 10px;">
                                        <div class="col-md-10">PAYMENT DETAILS</div>
                                    </div>

                                    <div class="row" style="font-size: 14px;">
                                        <div class="col-md-10" style="display:flex;align-items: center;"><input
                                                type="checkbox" class="payment_type" name="payment_type[]"
                                                value="Advance Paid" style="width:20px;height:20px;">&nbsp;&nbsp;Advance
                                            Paid &nbsp;&nbsp;<input type="text" name="advance_payment_amount"
                                                id="advance_payment_amount" class="form-control"
                                                style="width:100px;height: 30px;"></div>
                                    </div>

                                    <div class="row" style="font-size: 14px;">
                                        <div class="col-md-10" style="display:flex;align-items: center;"><input
                                                type="checkbox" class="payment_type" name="payment_type[]"
                                                value="Full Payment Paid"
                                                style="width:20px;height:20px;">&nbsp;&nbsp;Full Payment Paid
                                            &nbsp;&nbsp;<input type="text" name="full_payment_amount"
                                                id="full_payment_amount" class="form-control"
                                                style="width:100px;height: 30px;"></div>
                                    </div>

                                    <div class="row" style="font-size: 14px;">
                                        <div class="col-md-10" style="display:flex;align-items: center;"><input
                                                type="checkbox" class="payment_type" name="payment_type[]" value="COD"
                                                style="width:20px;height:20px;">&nbsp;&nbsp;COD &nbsp;&nbsp;<input
                                                type="text" name="due_amount" id="due_amount" class="form-control"
                                                style="width:100px;height: 30px;"></div>
                                    </div>

                                    <div class="row" style="font-size: 14px;color: red;">
                                        <div class="col-md-10" style="display:flex;align-items: center;"><input
                                                type="checkbox" class="payment_type" name="payment_type[]"
                                                value="NO PAYMENT YET" style="width:20px;height:20px;">&nbsp;&nbsp;NO
                                            PAYMENT YET </div>
                                    </div>

                                    <div class="row"
                                        style="font-size: 20px;font-weight: bold;margin-bottom: 10px;margin-top:20px;">
                                        <div class="col-md-10">Notifications SMS</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" style="display:flex;align-items:center;"><input
                                                type="checkbox" style="width:20px;height:20px;"
                                                name="Notification_type[]" class="form-control"
                                                value="Order Created">&nbsp;&nbsp;Order Created
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" style="display:flex;align-items:center;"><input
                                                type="checkbox" style="width:20px;height:20px;" class="form-control"
                                                value="Advance Received" name="Notification_type[]">&nbsp;&nbsp;Advance
                                            Received
                                        </div>


                                    </div>

                                    <div class="row">
                                        <div class="col-md-12" style="display:flex;align-items:center;"><input
                                                type="checkbox" style="width:20px;height:20px;" class="form-control"
                                                value="Full Payment Received"
                                                name="Notification_type[]">&nbsp;&nbsp;Full Payment Received
                                        </div>
                                        <!--<div class="col-md-6" style="border:1px solid black;border-radius: 10px;margin-left: 40px;" >
                            Your print order has been successfully created. Check
the process here: [Insert Link]. - Infive Print
                        </div> -->
                                        <input type="hidden" id="total_order_amount" name="total_order_amount">

                                    </div><br>


                                    <div class="row">
                                        <div class="col-md-3 me-2">
                                            <input type="submit" name="btnCreateOrder" value="Save"
                                                class="btn btn-success btn-lg">
                                        </div>
                                        <div class="col-md-3 me-2">
                                            <button type="button" class="btn btn-success btn-lg"
                                                onclick="generateQuotation()">
                                                Generate Quotation
                                            </button>
                                            <button type="button" class="btn btn-danger btn-lg"
                                                onclick="saveQuotation()" id="saveBtn" disabled>
                                                Save Quotation
                                            </button>
                                        </div>
                                        <div class="col-md-3 me-2">
                                            <button type="button" class="btn btn-info btn-lg "
                                                onclick="downloadQuotationPDF()" id="downloadBtn">
                                                Download PDF
                                            </button>
                                            <button type="button" class="btn btn-primary btn-lg"
                                                onclick="printQuotationOnly()" id="printBtn">
                                                Print Quotation
                                            </button>
                                        </div>

                                    </div><br>




                                </div>
                            </div>

                        </div>

                    </form>
                    <div class="main-content" style="background-color:#F7F9F9; padding: 20px;">

                        <div class="quotation-container no-print"
                            style="background-color:#fff; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">



                            <div id="quotation-preview" style="margin-top: 30px;display: none;">


                                <!-- HEADER START -->
                                <div class="quotation-header"
                                    style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 30px;">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: flex-start;">

                                        <!-- Left side: Logo + Company Info -->
                                        <div style="max-width: 60%;">
                                            <img src="assets/img/infive_logo.png" alt="logo" width="200">

                                        </div>

                                        <!-- Right side: Quotation title + number -->
                                        <div style="text-align: right; max-width: 40%;">
                                            <div class="quotation-title" style="font-size: 40px; font-weight: bold;">
                                                QUOTATION</div>
                                            <h5 style="margin: 0;">Infive (Pvt) Ltd</h5>
                                            <p style="margin: 5px 0;">infivellc@gmail.com | InfivePrint.com |
                                                Info@infive.lk</p>
                                            <p style="margin: 5px 0;"> +94 71 4994579 | +937 4 300 250</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- HEADER END -->


                                <div class="quotation-details">
                                    <div class="row" style="display: flex; justify-content: space-between;">
                                        <div style="width: 48%;">
                                            <strong>To:</strong><br>
                                            <div id="preview-customer-details"></div>
                                        </div>
                                        <div style="width: 48%; text-align: right;">
                                            <strong>Date:</strong> <?php echo date('Y-m-d'); ?><br>
                                            <strong>Valid Until:</strong>
                                            <?php echo date('Y-m-d', strtotime('+14 days')); ?>
                                        </div>
                                    </div>
                                </div>

                                <table class="quotation-table" style="width:100%; border-collapse: collapse; ">
                                    <thead>
                                        <tr style="background-color: #eee;">
                                            <th style="border:1px solid #ccc; padding:10px;">No</th>
                                            <th style="border:1px solid #ccc; padding:10px;">Description</th>
                                            <th style="border:1px solid #ccc; padding:10px;">Qty</th>
                                            <th style="border:1px solid #ccc; padding:10px;">Unit Price</th>
                                            <th style="border:1px solid #ccc; padding:10px;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quotation-items">
                                        <!-- Quotation items will be populated here -->
                                    </tbody>
                                </table>

                                <div class="quotation-totals" style="text-align: right;">
                                    <table style="width: 300px; float: right;">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td id="quotation-subtotal">Rs. 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td id="quotation-discount">Rs. 0.00</td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td>Total:</td>
                                            <td id="quotation-total">Rs. 0.00</td>
                                        </tr>
                                    </table>
                                    <div style="clear: both;"></div>
                                </div>

                                <div>

                                    <p style="font-size: 20px;"><strong>Terms & Conditions:</strong></p>
                                    <ol>
                                        <li>This quotation is valid for 14 days from the date of issue.</li>
                                        <li>60% advance payment required to confirm the order.</li>
                                        <li>Delivery time starts after the approval of final artwork.</li>
                                        <li>Prices are subject to change without prior notice.</li>


                                    </ol>
                                </div>

                                <div style="text-align: right;">

                                    <p><strong>This is an electronic invoice, no signature required</strong></p>
                                </div>
                                <!-- FOOTER START -->
                                <div class="quotation-footer"
                                    style="border-top: 1px solid #ccc; padding-top: 15px;  font-size: 13px; text-align: center; color: #555;">
                                    <p style="font-size: 15px;"><Strong>Payment Details :-
                                            Infive (Private) Limited |
                                            000610017878 |
                                            Sampath Bank -
                                            Kurunegala</Strong></> <br>
                                        Thank you for your business! | Infive (Private) Limited

                                </div>
                                <!-- FOOTER END -->
                            </div>
                        </div>
                    </div>

            </div>
            </section>
        </div>
        <?php //include('footer.php'); ?>
    </div>
    </div>

    <!-- General JS Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/popper.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/tooltip.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/moment.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/stisla.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Function to generate quotation preview

        let quotationGenerated = false;
        let quotationSaved = false;

        function generateQuotation() {
            quotationGenerated = true;
            quotationSaved = false;
            document.getElementById("saveBtn").disabled = false;

            // Get customer details
            const companyName = $('input[name="company_name"]').val();
            const contactPerson = $('input[name="contact_person"]').val();
            const phone = $('input[name="phone"]').val();
            const email = $('input[name="email"]').val();
            const deliveryAddress = $('textarea[name="delivery_address"]').val();

            if (!companyName || !phone || !deliveryAddress) {
                alert("Please fill in all required customer details (Company Name, Phone, and Delivery Address)");
                return;
            }

            // Display customer details in preview
            let customerDetails = '';
            if (companyName) customerDetails += companyName + '<br>';
            if (contactPerson) customerDetails += 'Attn: ' + contactPerson + '<br>';
            if (phone) customerDetails += 'Phone: ' + phone + '<br>';
            if (email) customerDetails += 'Email: ' + email + '<br>';
            if (deliveryAddress) customerDetails += 'Address: ' + deliveryAddress;

            $('#preview-customer-details').html(customerDetails);

            // Get all product items
            let itemsHtml = '';
            let subtotal = 0;
            let designCharges = 0;
            let courierCharges = 0;
            let itemCount = 1;
            let items = [];

            $('.product-row').each(function () {
                const product = $(this).find('select').val();
                const quantity = $(this).find('.quantity').val();
                const price = $(this).find('.price').val();
                const total = $(this).find('.total').val();

                if (product && quantity && price) {
                    itemsHtml += `
                <tr>
                    <td>${itemCount++}</td>
                    <td>${product}</td>
                    <td>${quantity}</td>
                    <td>Rs. ${parseFloat(price).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(total).toFixed(2)}</td>
                </tr>
            `;
                    subtotal += parseFloat(total);
                    items.push({
                        product: product,
                        quantity: quantity,
                        price: price,
                        total: total
                    });

                    // Check if product name contains "design" or is "Delivery"
                    if (product.toLowerCase().includes('design')) {
                        designCharges += parseFloat(total);
                    } else if (product === 'Delivery') {
                        courierCharges += parseFloat(total);
                    }
                }
            });

            if (itemCount === 1) {
                alert("Please add at least one product to generate a quotation");
                return;
            }

            // Get manually entered design charges
            let manualDesignCharges = parseFloat($('#design_charges').val()) || 0;

            // Calculate adjusted total for coupon eligibility
            let adjustedTotal = subtotal - designCharges - courierCharges - manualDesignCharges;

            // Get manually entered discount
            let discount = parseFloat($('#discount_amount').val()) || 0;
            let couponType = 'None';
            let finalTotal = subtotal - discount;

            // Determine coupon type based on adjusted total
            if (adjustedTotal >= 3500 && adjustedTotal < 6999) {
                couponType = 'Free Delivery';
                // Prompt user to apply Free Delivery
                if (courierCharges > 0 && confirm("Eligible for Free Delivery. Do you want to apply it?")) {
                    items = items.map(item => {
                        if (item.product === 'Delivery') {
                            item.price = 0;
                            item.total = 0;
                            // Update the table row for Delivery
                            itemsHtml = itemsHtml.replace(
                                `<td>Rs. ${parseFloat(item.price).toFixed(2)}</td><td>Rs. ${parseFloat(item.total).toFixed(2)}</td>`,
                                `<td>Rs. 0.00</td><td>Rs. 0.00</td>`
                            );
                        }
                        return item;
                    });
                    finalTotal = items.reduce((sum, item) => sum + parseFloat(item.total), 0) - discount;
                    $('#quotation-items').html(itemsHtml);
                }
            } else if (adjustedTotal >= 6999 && adjustedTotal < 14999) {
                couponType = 'Rs. 500 Discount';
            } else if (adjustedTotal >= 15000) {
                couponType = 'Rs. 1000 Discount';
            }

            // Update quotation items
            $('#quotation-items').html(itemsHtml);

            // Update totals in preview
            $('#quotation-subtotal').text('Rs. ' + subtotal.toFixed(2));
            $('#quotation-discount').text('Rs. ' + discount.toFixed(2));
            $('#quotation-total').text('Rs. ' + finalTotal.toFixed(2));

            // Update total order amount
            $('#total_order_amount').val(finalTotal.toFixed(2));
            $('#div_total_amount').html('Rs. ' + subtotal.toFixed(2));
            $('#div_final_amount').html('Rs. ' + finalTotal.toFixed(2));

            // Store coupon type in hidden field
            $('#coupon_type').val(couponType);

            // Show the quotation preview
            $('#quotation-preview').show();

            // Scroll to the quotation preview
            $('html, body').animate({
                scrollTop: $('#quotation-preview').offset().top
            }, 500);
            alert("Quotation Generated!");
        }

        // Previous JavaScript functions remain the same

        $(document).ready(function () {
            // Previous document ready code remains the same

            // Update customer details in real-time
            $('input[name="company_name"], input[name="contact_person"], input[name="phone"], input[name="email"], textarea[name="delivery_address"]').on('keyup', function () {
                const companyName = $('input[name="company_name"]').val();
                const contactPerson = $('input[name="contact_person"]').val();
                const phone = $('input[name="phone"]').val();
                const email = $('input[name="email"]').val();
                const deliveryAddress = $('textarea[name="delivery_address"]').val();

                let customerDetails = '';
                if (companyName) customerDetails += '<strong>Company:</strong> ' + companyName + '<br>';
                if (contactPerson) customerDetails += '<strong>Contact:</strong> ' + contactPerson + '<br>';
                if (phone) customerDetails += '<strong>Phone:</strong> ' + phone + '<br>';
                if (email) customerDetails += '<strong>Email:</strong> ' + email + '<br>';
                if (deliveryAddress) customerDetails += '<strong>Address:</strong> ' + deliveryAddress;

                $('#customer-details').html(customerDetails);
            });
        });

        function saveQuotation() {
            if (!quotationGenerated) {
                alert("Please generate the quotation first!");
                return;
            }

            // Get customer details
            const companyName = $('input[name="company_name"]').val();
            const contactPerson = $('input[name="contact_person"]').val();
            const phone = $('input[name="phone"]').val();
            const email = $('input[name="email"]').val();
            const deliveryAddress = $('textarea[name="delivery_address"]').val();
            const order_id = <?php echo $order_id ?: 'null'; ?>;

            // Get all product items
            let items = [];
            let subtotal = 0;
            let designCharges = 0;
            let courierCharges = 0;

            $('.product-row').each(function () {
                const product = $(this).find('select').val();
                const quantity = $(this).find('.quantity').val();
                const price = $(this).find('.price').val();
                const total = $(this).find('.total').val();

                if (product && quantity && price) {
                    items.push({
                        product: product,
                        quantity: quantity,
                        price: price,
                        total: total
                    });
                    subtotal += parseFloat(total);

                    // Check if product name contains "design" or is "Delivery"
                    if (product.toLowerCase().includes('design')) {
                        designCharges += parseFloat(total);
                    } else if (product === 'Delivery') {
                        courierCharges += parseFloat(total);
                    }
                }
            });

            // Get manually entered design charges
            let manualDesignCharges = parseFloat($('#design_charges').val()) || 0;

            // Calculate adjusted total for coupon eligibility
            let adjustedTotal = subtotal - designCharges - courierCharges - manualDesignCharges;

            // Get manually entered discount
            let discount = parseFloat($('#discount_amount').val()) || 0;
            let couponType = 'None';
            let finalTotal = subtotal - discount;

            // Determine coupon type based on adjusted total
            if (adjustedTotal >= 3500 && adjustedTotal < 6999) {
                couponType = 'Free Delivery';
                // Check if Free Delivery was applied
                items.forEach(item => {
                    if (item.product === 'Delivery' && parseFloat(item.price) === 0) {
                        finalTotal = items.reduce((sum, item) => sum + parseFloat(item.total), 0) - discount;
                    }
                });
            } else if (adjustedTotal >= 6999 && adjustedTotal < 14999) {
                couponType = 'Rs. 500 Discount';
            } else if (adjustedTotal >= 15000) {
                couponType = 'Rs. 1000 Discount';
            }

            // Prepare data for AJAX request
            const quotationData = {
                order_id: order_id,
                customer: {
                    company_name: companyName,
                    contact_person: contactPerson,
                    phone: phone,
                    email: email,
                    delivery_address: deliveryAddress
                },
                items: items,
                subtotal: subtotal,
                discount: discount,
                coupon_type: couponType,
                total: finalTotal,
                design_charges: manualDesignCharges
            };

            // In your frontend JavaScript (e.g., within saveQuotation function)
            $.ajax({
                url: 'save_quotation.php',
                type: 'POST',
                data: { quotation_data: JSON.stringify(quotationData) },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        quotationSaved = true;
                        // Update the quotation number in the preview
                        $('.quotation-title').after('<div>' + response.quotation_number + '</div>');
                        // Optionally display coupon type
                        console.log('Coupon Type:', response.coupon_type);
                        console.log('Adjusted Total:', response.adjusted_total);

                        $('#quotation-preview').show();
                        $('html, body').animate({
                            scrollTop: $('#quotation-preview').offset().top
                        }, 500);

                        alert('Quotation saved successfully!');
                    } else {
                        alert('Error saving quotation: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        }

        // Previous JavaScript functions remain the same

        $(document).ready(function () {
            // Previous document ready code remains the same

            // Update customer details in real-time
            $('input[name="company_name"], input[name="contact_person"], input[name="phone"], input[name="email"], textarea[name="delivery_address"]').on('keyup', function () {
                const companyName = $('input[name="company_name"]').val();
                const contactPerson = $('input[name="contact_person"]').val();
                const phone = $('input[name="phone"]').val();
                const email = $('input[name="email"]').val();
                const deliveryAddress = $('textarea[name="delivery_address"]').val();

                let customerDetails = '';
                if (companyName) customerDetails += '<strong>Company:</strong> ' + companyName + '<br>';
                if (contactPerson) customerDetails += '<strong>Contact:</strong> ' + contactPerson + '<br>';
                if (phone) customerDetails += '<strong>Phone:</strong> ' + phone + '<br>';
                if (email) customerDetails += '<strong>Email:</strong> ' + email + '<br>';
                if (deliveryAddress) customerDetails += '<strong>Address:</strong> ' + deliveryAddress;

                $('#customer-details').html(customerDetails);
            });
        });
        function printQuotationOnly() {
            if (!quotationGenerated) {
                alert("Please generate the quotation first!");
                return;
            }

            if (!quotationSaved) {
                if (confirm("Quotation hasn't been saved yet. Do you want to save it first?")) {
                    saveQuotation();
                    return;
                }
            }

            // Get the HTML content of the quotation
            const quotationContent = document.getElementById("quotation-preview").innerHTML;

            // Create a new window with the quotation content
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
        <html>
        <head>
            <title>Quotation</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    padding: 20px; 
                    background-color: white;
                }
                .quotation-header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                }
                .quotation-title { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #1BA664;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th { 
                    background-color: #1BA664;
                    color: white;
                    padding: 10px;
                    text-align: left;
                }
                td { 
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                }
                .quotation-totals table {
                    width: 300px;
                    float: right;
                    margin-top: 20px;
                    border-collapse: collapse;
                }
                .quotation-totals td {
                    padding: 8px 15px;
                    text-align: right;
                    border: 1px solid #000;
                }
                .total-row { 
                    font-weight: bold; 
                    border-top: 2px solid #1BA664;
                }
                @media print {
                    body { 
                        font-size: 12pt; 
                        padding: 0;
                    }
                    .quotation-container {
                        box-shadow: none;
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${quotationContent}
        </body>
        </html>
    `);
            printWindow.document.close();
        }

    </script>

    <!-- JS Libraies -->

    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>

    <!-- Page Specific JS File -->



    <script type="text/javascript">
        $(document).ready(function () {
            var total_order_amount = 0;
            <?php if ($recordAdded): ?>
            setTimeout(function () {
                window.location.href = 'dashboard.php';
            }, 30000); // 3 seconds delay before redirecting
            <?php endif; ?>

            $('#productCombo').select2({
                placeholder: "Search product...",
                allowClear: true,
                width: 'resolve'
            });

            // Event handler for the first dropdown
            $('#productCombo').on('select2:select', function (e) {
                var selectedVal = $(this).val();
                get_specification(selectedVal);
            });

            function calculateTotal() {
                let total = 0;
                let designCharges = 0;
                let courierCharges = 0;

                $('#div_add_more_product .product-row').each(function () {
                    var quantity = $(this).find('.quantity').val();
                    var price = $(this).find('.price').val();
                    var totalField = $(this).find('.total');
                    var product = $(this).find('select').val();

                    if (quantity && price && product) {
                        var result = parseFloat(quantity) * parseFloat(price);
                        totalField.val(result.toFixed(2));
                        total += result;

                        // Check if product name contains "design" (case-insensitive) or is "Delivery"
                        if (product.toLowerCase().includes('design')) {
                            designCharges += result;
                        } else if (product === 'Delivery') {
                            courierCharges += result;
                        }
                    } else {
                        totalField.val('');
                    }
                });

                // Update total order amount
                $('#total_order_amount').val(total.toFixed(2));
                $('#div_total_amount').html('Rs. ' + total.toFixed(2));

                // Get manually entered discount
                var discount = parseFloat($('#discount_amount').val()) || 0;
                var finalAmount = total - discount;
                $('#div_final_amount').html('Rs. ' + finalAmount.toFixed(2));

                // Get manually entered design charges (if any)
                var manualDesignCharges = parseFloat($('#design_charges').val()) || 0;

                // Calculate adjusted total for coupon eligibility
                var adjustedTotal = total - designCharges - courierCharges - manualDesignCharges;

                // Determine coupon type based on adjusted total
                // Determine coupon type based on adjusted total
                var couponType = 'No Coupon';
                if (adjustedTotal >= 3500 && adjustedTotal < 6999) {
                    couponType = 'Free Delivery';
                } else if (adjustedTotal >= 6999 && adjustedTotal < 14999) {
                    couponType = 'Rs. 500 Discount';
                } else if (adjustedTotal >= 15000) {
                    couponType = 'Rs. 1000 Discount';
                }

                // Update total order amount (based on manually entered discount)
                $('#total_order_amount').val(finalAmount.toFixed(2));

                // Display coupon type below design charges field
                var couponRowId = 'coupon-type-row';
                var couponDisplay = $('#' + couponRowId);
                if (couponType !== 'No Coupon') {
                    if (couponDisplay.length === 0) {
                        // Create the coupon row below the design charges field
                        $('#design_charges').closest('.row').after(`
                    <div class="magic-coupon-container" style="position: relative; max-width: 80%; margin: 30px auto; height: 160px; perspective: 1000px;">
    <!-- Magic Flash Effect -->
    <div class="magic-flash" style="position: absolute; width: 100%; height: 100%; background: white; opacity: 0; animation: magicFlash 1.2s ease-out;"></div>
    
    <!-- Coupon Card -->
    <div class="magic-coupon" id="${couponRowId}" style="background: linear-gradient(135deg, #e3f2fd, #b3e5fc); border: 1px solid #90caf9; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); height: 150px; display: flex; align-items: center; transform-style: preserve-3d; animation: cardAppear 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s both; opacity: 0; padding: 0 20px;">
        
        <!-- Sparkle Particles -->
        <div class="sparkle" style="position: absolute; width: 8px; height: 8px; background: white; border-radius: 50%; opacity: 0; animation: sparklePop 0.8s ease-out 0.8s forwards; top: 20%; left: 15%;"></div>
        <div class="sparkle" style="position: absolute; width: 6px; height: 6px; background: gold; border-radius: 50%; opacity: 0; animation: sparklePop 0.8s ease-out 1s forwards; bottom: 30%; right: 10%;"></div>
        
        <div class="col-8 text-end" style="font-weight: 700; font-size: 28px; color: #1a237e; letter-spacing: 1px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            ✨ Magic Coupon
        </div>
        <div class="col-4" id="coupon-type" style="font-size: 24px; color: #1b5e20; font-weight: 600; text-align: left; animation: textGlow 2s ease-in-out infinite 1.5s alternate;"></div>
    </div>
</div>

<style>
/* Magic Flash Effect */
@keyframes magicFlash {
    0% { opacity: 0.9; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.2); }
    100% { opacity: 0; transform: scale(1.5); }
}

/* Card Appearance */
@keyframes cardAppear {
    0% { 
        opacity: 0;
        transform: rotateY(90deg) scale(0.7);
    }
    70% {
        transform: rotateY(-10deg) scale(1.05);
    }
    100% { 
        opacity: 1;
        transform: rotateY(0) scale(1);
    }
}

/* Sparkle Effects */
@keyframes sparklePop {
    0% {
        opacity: 0;
        transform: scale(0);
    }
    50% {
        opacity: 1;
        transform: scale(1.5);
    }
    100% {
        opacity: 0;
        transform: scale(0.5);
    }
}

/* Text Glow Effect */
@keyframes textGlow {
    from { text-shadow: 0 0 5px rgba(30, 180, 30, 0.3); }
    to { text-shadow: 0 0 15px rgba(30, 220, 30, 0.7); }
}

/* Hover Effect */
#${couponRowId}:hover {
    animation: gentleFloat 3s ease-in-out infinite;
    transform-origin: center;
}

@keyframes gentleFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(1deg); }
}
</style>
                `);
                    }
                    $('#coupon-type').html(couponType);
                } else {
                    // Remove the coupon row if no coupon is applicable
                    if (couponDisplay.length > 0) {
                        couponDisplay.remove();
                    }
                }

                // Store coupon type in hidden field for quotation preview
                if ($('#coupon_type').length === 0) {
                    $('form').append('<input type="hidden" id="coupon_type" name="coupon_type">');
                }
                $('#coupon_type').val(couponType);
            }

            // Add event listener for design charges changes
            $('#design_charges').on('keyup', function () {
                calculateTotal();
            });

            // Add event listener for discount amount changes
            $('#discount_amount').on('keyup', function () {
                calculateTotal();
            });

            $('#discount_amount').on('change', function () {
                var discount = parseFloat($(this).val()) || 0;
                var total = parseFloat($('#total_order_amount').val()) || 0;
                if (discount > total) {
                    alert('Discount cannot be greater than total amount!');
                    $(this).val(total.toFixed(2));
                    calculateTotal();
                }
            });

            // Attach event listener to dynamically added rows
            $('#div_add_more_product').on('keyup', '.quantity, .price', function () {
                calculateTotal();
            });

            $('.payment_type').on('click', function () {
                if ($(this).is(':checked')) {
                    var checkboxValue = $(this).val();
                    if (checkboxValue == 'COD') {
                        var due_amount = $('#total_order_amount').val() - $('#advance_payment_amount').val();
                        $('#due_amount').val(due_amount);
                    }
                }
            });
        });
    </script>

    <script type="text/javascript">

        var product_cnt = 1;
        function get_specification(val, rowId) {
            var targetDiv = rowId ? $('#div_product_spec_' + rowId) : $('#div_product_spec_' + product_cnt);

            // Clear previous specifications
            targetDiv.html('');

            // Define which products have specifications
            const productsWithSpecs = [
                'Business card Design',
                'Business card Print full color',
                'Business card Print foil',
                'Business card Print matte',
                'Sticker print',
                'Flyer Print',
                'Tag Print',
                'Any Other Design',
                'Any Other Print'
            ];

            // Check if the selected product has specifications
            if (productsWithSpecs.includes(val)) {
                // Show the "Select Specs" heading
                targetDiv.append('<div class="row"><div class="col-md-6 specs-heading">Select Specs<span class="star">**</span></div></div>');

                // Add the appropriate specifications based on the product
                if (val == 'Business card Design') {
                    targetDiv.append($('#business_card_design').html());
                }
                else if (val == 'Business card Print full color') {
                    targetDiv.append($('#business_card_print_full').html());
                }
                else if (val == 'Business card Print foil') {
                    targetDiv.append($('#business_card_print_foil').html());
                }
                else if (val == 'Business card Print matte') {
                    targetDiv.append($('#business_card_print_matte').html());
                }
                else if (val == 'Sticker print') {
                    targetDiv.append($('#sticker_print').html());
                }
                else if (val == 'Flyer Print') {
                    targetDiv.append($('#flyer_print').html());
                }
                else if (val == 'Tag Print') {
                    targetDiv.append($('#tag_print').html());
                }
                else if (val == 'Any Other Design') {
                    targetDiv.append($('#any_other_design').html());
                }
                else if (val == 'Any Other Print') {
                    targetDiv.append($('#any_other_print').html());
                }
            } else {
                // For products without specs (like Delivery), clear the div
                targetDiv.html('');
            }
        }
        // Initialize product counter
        var product_cnt = 1;

        // Function to add a new product row
        function add_more_product() {
            product_cnt++;
            create_product_row(product_cnt);
        }

        // Function to create a product row (used for both initial and additional rows)
        function create_product_row(rowId) {
            // Create a new row container
            var newRow = $('<div class="row product-row" style="font-size: 16px;font-weight: bold; margin-bottom: 15px;" id="row_' + rowId + '"></div>');

            // Create product dropdown
            var productSelect = $('<select class="form-control product-select" name="products[]" onchange="get_specification(this.value, ' + rowId + ')" style="width: 100%;"><option value="">Select</option></select>');

            // Add all product options
            <?php
            foreach ($product as $val) {
                echo "productSelect.append('<option value=\"$val\">$val</option>');";
            }
            foreach ($order_product as $row) {
                $val = $row['product_name'];
                echo "productSelect.append('<option value=\"$val\">$val</option>');";
            }
            ?>

            // Create quantity, price, and total inputs
            var quantityInput = $('<input type="text" name="quantity[]" class="form-control quantity" style="width:100px;height: 40px;">');
            var priceInput = $('<input type="text" name="price[]" class="form-control price" style="width:100px;height: 40px;">');
            var totalInput = $('<input type="text" name="total[]" class="form-control total" style="width:150px;height: 40px;" readonly>');

            // Create remove button
            var removeBtn = $('<button type="button" class="btn btn-danger btn-sm remove-row" style="height: 40px; margin-left: 10px;"><i class="fas fa-trash"></i></button>');
            removeBtn.click(function () {
                remove_product_row(rowId);
            });

            // Create columns and append elements
            var productCol = $('<div class="col-md-3"></div>').append(productSelect);
            var quantityCol = $('<div class="col-md-2"></div>').append(quantityInput);
            var priceCol = $('<div class="col-md-3"></div>').append(priceInput);
            var totalCol = $('<div class="col-md-3"></div>').append(totalInput);
            var removeCol = $('<div class="col-md-1"></div>').append(removeBtn);

            // Append columns to row
            newRow.append(productCol, quantityCol, priceCol, totalCol, removeCol);

            // Add the new row to the container
            $('#div_add_more_product').append(newRow);

            // Initialize Select2 for the new product dropdown
            productSelect.select2({
                placeholder: "Search product...",
                allowClear: true,
                width: 'resolve'
            });

            // Add specification div after the new row
            var specDiv = $('<div style="margin-top:10px;" id="div_product_spec_' + rowId + '"></div>');
            $('#div_add_more_product').append(specDiv);
        }

        // Function to remove a product row
        function remove_product_row(rowId) {
            // Don't allow removing if it's the only row left
            if ($('.product-row').length <= 1) {
                alert("You need to keep at least one product row");
                return;
            }

            // Remove the row and its specification div
            $('#row_' + rowId).remove();
            $('#div_product_spec_' + rowId).remove();

            // Recalculate totals after removal
            calculateTotal();
        }

        // Initialize the first row when page loads
        $(document).ready(function () {
            // Modify the initial row to include remove button
            $('#div_product').addClass('product-row').attr('id', 'row_1');

            // Add remove button to first row
            var removeBtn = $('<button type="button" class="btn btn-danger btn-sm remove-row" style="height: 40px; margin-left: 10px;"><i class="fas fa-trash"></i></button>');
            removeBtn.click(function () {
                remove_product_row(1);
            });

            // Add the remove button to the first row
            $('#div_product .row > div:last-child').after('<div class="col-md-1"></div>').next().append(removeBtn);

            // Initialize Select2 for the first product dropdown
            $('#productCombo').select2({
                placeholder: "Search product...",
                allowClear: true,
                width: 'resolve'
            });
        });

        function downloadQuotationPDF() {
            if (!quotationGenerated) {
                alert("Please generate the quotation first!");
                return;
            }

            if (!quotationSaved) {
                if (confirm("Quotation hasn't been saved yet. Do you want to save it first?")) {
                    saveQuotation();
                    return;
                }
            }

            // Create a clone of the quotation preview
            const element = document.getElementById("quotation-preview").cloneNode(true);

            // Add a container div with proper styling for PDF
            const container = document.createElement('div');
            container.style.padding = '20px';
            container.style.fontFamily = 'Arial, sans-serif';
            container.appendChild(element);

            // Add specific styles for PDF
            const style = document.createElement('style');
            style.innerHTML = `
        body { margin: 0; padding: 0; }
        .quotation-header { text-align: right; margin-top: 0; margin-bottom: 20px; }
        .quotation-title { color: #1BA664; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background-color: #1BA664; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .quotation-totals table { width: 300px; margin-left: auto; }
        .quotation-totals td { padding: 8px 15px; text-align: right; border: 1px solid #000; }
        .total-row { font-weight: bold; border-top: 2px solid #1BA664; }
    `;
            container.appendChild(style);

            // Options for PDF generation
            const opt = {
                margin: 10,
                filename: 'Quotation_' + new Date().toISOString().slice(0, 10) + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    logging: true,
                    useCORS: true,
                    allowTaint: true,
                    scrollX: 0,
                    scrollY: 0
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate and download PDF
            html2pdf()
                .set(opt)
                .from(container)
                .save()
                .then(() => {
                    // Clean up
                    container.remove();
                })
                .catch(err => {
                    console.error('PDF generation error:', err);
                    alert('Error generating PDF: ' + err.message);
                });
        }

        function printQuotationOnly() {
            if (!quotationGenerated) {
                alert("Please generate the quotation first!");
                return;
            }

            if (!quotationSaved) {
                if (confirm("Quotation hasn't been saved yet. Do you want to save it first?")) {
                    saveQuotation();
                    return;
                }
            }

            // Get the HTML content of the quotation
            const quotationContent = document.getElementById("quotation-preview").innerHTML;

            // Create a new window with the quotation content
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
        <html>
        <head>
            <title>Quotation</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    padding: 20px; 
                    background-color: white;
                }
                .quotation-header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                }
                .quotation-title { 
                    font-size: 24px; 
                    font-weight: bold; 
                    color: #1BA664;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th { 
                    background-color: #1BA664;
                    color: white;
                    padding: 10px;
                    text-align: left;
                }
                td { 
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                }
                .total-row { 
                    font-weight: bold; 
                    border-top: 2px solid #1BA664;
                }
                @media print {
                    body { 
                        font-size: 12pt; 
                        padding: 0;
                    }
                    .quotation-container {
                        box-shadow: none;
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${quotationContent}
        </body>
        </html>
    `);
            printWindow.document.close();
        }

    </script>
</body>

</html>