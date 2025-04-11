<?php
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
                    <h1>Dashboard Work</h1>
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
                                                        style="height:30px;" required>
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
                                            Final Amount After Discount</div>
                                        <div class="col-md-3 heading_16" id="div_final_amount"></div>
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
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="submit" name="btnCreateOrder" value="Save"
                                                    class="btn btn-success btn-lg">
                                            </div>
                                        </div><br>


                                    </div>
                                </div>

                            </div>

                    </form>

                </section>
            </div>
            <?php //include('footer.php'); ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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



            // Auto-call your function on select
            $('#productCombo').on('select2:select', function (e) {
                var selectedVal = $(this).val();
                get_specification(selectedVal);
            });

            function calculateTotal() {
                $('#div_add_more_product .row').each(function () {
                    var quantity = $(this).find('.quantity').val();
                    var price = $(this).find('.price').val();
                    var total = $(this).find('.total');
                    if (quantity && price) {
                        var result = parseFloat(quantity) * parseFloat(price);
                        total.val(result.toFixed(2));
                        var total = 0;
                        const quantities = document.querySelectorAll('input[name="total[]"]');
                        quantities.forEach(quantity => {
                            if (!isNaN(quantity.value) && quantity.value.trim() !== '') {
                                total += parseFloat(quantity.value);
                            }
                        });
                        $('#total_order_amount').val(total);
                        $('#div_total_amount').html('RS.' + total.toFixed(2));
                        // Calculate final amount after discount
                        var discount = parseFloat($('#discount_amount').val()) || 0;
                        var finalAmount = total - discount;
                        $('#div_final_amount').html('RS.' + finalAmount.toFixed(2));
                    } else {
                        total.val('');
                    }
                });
            }

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
                        $('#due_amount').val(due_amount)
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

    </script>
</body>

</html>