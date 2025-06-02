<?php
session_start();
include('db.php');

if ($_SESSION['logged_id'] <= 0) {
    header('Location: ./');
    exit;
}

$current_status = 'ACTIVE';
if (!empty($_POST['activeButton'])) {
    $current_status = $_POST['activeButton'];
}
if (!empty($_POST['deliveredButton'])) {
    $current_status = $_POST['deliveredButton'];
}
if (!empty($_POST['nonePaidButton'])) {
    $current_status = $_POST['nonePaidButton'];
}
if (!empty($_POST['WithCourierButton'])) {
    $current_status = $_POST['WithCourierButton'];
}

$search_order = isset($_GET['search_order']) ? trim($_GET['search_order']) : '';
$formActionUrl = BASE_URL . "/dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Infive Print</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .main-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            z-index: 1000;
            background-color: #1BA664;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .main-content {
            margin-left: 250px;
            overflow-y: auto;
            height: 100vh;
            background-color: #F7F9F9;
            padding: 20px;
        }
        .navbar-bg, .main-navbar { display: none; }
        .product { font-size: 16px; font-weight: bold; }
        .btn-success {
            background-color: #1BA664;
            border-color: #1BA664;
            color: #fff;
        }
        .search-results-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        /* Responsive Styles */
        @media (max-width: 991px) {
            .main-sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }
        @media (max-width: 767px) {
            .main-sidebar {
                width: 150px;
            }
            .main-content {
                margin-left: 150px;
                padding: 15px;
            }
            .btn-group {
                display: flex;
                flex-direction: column;
                width: 100%;
            }
            .btn-group .btn {
                margin-bottom: 5px;
                width: 100%;
            }
            .product {
                font-size: 14px;
            }
            .col-sm-3 .btn {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
            .btn-xs {
                font-size: 14px;
                padding: 8px 12px;
            }
            .form-inline .form-control {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="col-md-12 offset-md-0">
        <div class="main-wrapper main-wrapper-1">
            <?php include('left_menu.php'); ?>
            <div class="main-content">
                <section class="section">
                    <div>
                        <h1>Dashboard Work - Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h1>
                    </div>
                    <br><hr><br>
                    <div class="section-body">
                        <div class="row mb-3">
                            <div class="col-md-6 col-xs-6 col-sm-6">
                                <form method="post" action="<?php echo $formActionUrl; ?>">
                                    <div class="btn-group col-md-3 col-sm-9 col-xs-9" role="group">
                                        <input id="four" name="activeButton" type="submit" class="btn <?php echo ($current_status == 'ACTIVE') ? 'btn-success' : 'btn-default'; ?> btn-xs" value="ACTIVE">
                                        <input id="deliveredButton" name="deliveredButton" type="submit" class="btn <?php echo ($current_status == 'DELIVERED') ? 'btn-success' : 'btn-default'; ?> btn-xs" value="DELIVERED">
                                        <input id="nonePaidButton" name="nonePaidButton" type="submit" class="btn <?php echo ($current_status == 'NONE PAID') ? 'btn-success' : 'btn-default'; ?> btn-xs" value="NONE PAID">
                                        <input id="WithCourierButton" name="WithCourierButton" type="submit" class="btn <?php echo ($current_status == 'WITH COURIER') ? 'btn-success' : 'btn-default'; ?> btn-xs" value="WITH COURIER">
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-5 col-sm-3 col-xs-3 text-right">
                                <button type="button" class="btn btn-success btn-xs" onclick="add_new_order()">
                                    <i class="fas fa-plus rounded-circle"></i> Add New Order
                                </button>
                            </div>
                        </div>
                        <!-- Search by Order ID -->
                        <form method="get" class="form-inline mb-3">
                            <div class="input-group">
                                <input type="text" name="search_order" class="form-control" placeholder="Search by Order ID" value="<?php echo htmlspecialchars($search_order); ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                                <?php if (!empty($search_order)): ?>
                                    <div class="input-group-append">
                                        <a href="./dashboard" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php
                        if (!empty($search_order)) {
                            $statement = $pdo->prepare("SELECT * FROM pixel_media_order WHERE order_id = ?");
                            $statement->execute([$search_order]);
                            $order = $statement->fetch(PDO::FETCH_ASSOC);
                            if ($order) {
                                echo '<div class="search-results-header">';
                                echo "<h4>Order ID: <strong>{$order['order_id']}</strong></h4>";
                                echo "<h5>Status: <span class='badge badge-primary'>{$order['current_status']}</span></h5>";
                                $statement_details = $pdo->prepare("SELECT * FROM pixel_media_order_details WHERE order_id = ?");
                                $statement_details->execute([$order['order_id']]);
                                $order_details = $statement_details->fetchAll(PDO::FETCH_ASSOC);
                                if (count($order_details) > 0) {
                                    echo "<h5>Products:</h5>";
                                    echo "<ul class='list-group mb-3'>";
                                    foreach ($order_details as $product) {
                                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                        echo htmlspecialchars($product['product_name']);
                                        echo "<span class='badge badge-primary badge-pill'>Qty: {$product['quantity']}</span>";
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                }
                                echo '</div>';
                                ?>
                                <div class="row">
                                    <div class="col-md-12 d-none d-md-block" style="border: 1px solid #bcbcbc;border-radius:5px;background-color: #FFF;">
                                        <div class="row" style="line-height: 20px;padding: 20px;display: flex; align-items: center;">
                                            <div class="col-md-3">ORDER: <?php echo htmlspecialchars($order['order_id']); ?><br><span class='product'><?php echo htmlspecialchars($order['company_name']); ?></span><br><?php echo date('d-F-Y', strtotime($order['order_date'])); ?></div>
                                            <div class="col-md-3">PRODUCT<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>" . htmlspecialchars($row['product_name']) . "</div>"; } ?>
                                            </div>
                                            <div class="col-md-1">QTY<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>{$row['quantity']}</div>"; } ?>
                                            </div>
                                            <div class="col-md-2">TOTAL<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>{$row['total']}</div>"; } ?>
                                            </div>
                                            <div class="col-md-1"><button class="btn btn-primary btn-lg" style="height: 35px;" onclick="view_order('<?php echo htmlspecialchars($order['order_id']); ?>')">View</button></div>
                                            <div class="col-md-2"><button class="btn btn-success btn-lg" style="height: 35px;" onclick="download_invoice('<?php echo htmlspecialchars($order['order_id']); ?>')"><i class="fas fa-plus rounded-circle"></i> Download<br>INVOICE</button></div>
                                        </div>
                                    </div>
                                    <!-- Mobile View -->
                                    <div class="col-md-12 d-block d-md-none" style="border: 1px solid #bcbcbc;border-radius:5px;background-color: #FFF;">
                                        <div class="row" style="padding: 20px;">
                                            <div class="col-sm-4 col-4">ORDER: <?php echo htmlspecialchars($order['order_id']); ?><br><span class='product'><?php echo htmlspecialchars($order['company_name']); ?></span><br><?php echo date('d-F-Y', strtotime($order['order_date'])); ?></div>
                                            <div class="col-sm-5 col-5">
                                                <?php foreach ($order_details as $row) { ?>
                                                    <div class='product'><?php echo htmlspecialchars($row['product_name']); ?></div>
                                                    <div class='product'>QTY <?php echo $row['quantity']; ?></div>
                                                    <div class='product'>RS.<?php echo $row['total']; ?></div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-sm-3 col-3">
                                                <button class="btn btn-primary btn-lg" style="height: 25px;" onclick="view_order('<?php echo htmlspecialchars($order['order_id']); ?>')">View</button>
                                                <button class="btn btn-success btn-lg" style="height:30px;margin-top: 5px;" onclick="download_invoice('<?php echo htmlspecialchars($order['order_id']); ?>')"><i class="fas fa-plus rounded-circle"></i> Download<br>INVOICE</button>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                                <?php
                            } else {
                                echo "<div class='alert alert-warning'>No order found with ID: " . htmlspecialchars($search_order) . "</div>";
                            }
                        } else {
                            $statement = $pdo->prepare("SELECT * FROM pixel_media_order WHERE current_status = ? ORDER BY order_date");
                            $statement->execute([$current_status]);
                            $orders = $statement->fetchAll(PDO::FETCH_ASSOC);
                            if (count($orders) == 0) {
                                echo "<div class='alert alert-warning'>No orders found with status: {$current_status}</div>";
                            }
                            foreach ($orders as $item) {
                                $order_id = $item['order_id'];
                                $statement_details = $pdo->prepare("SELECT * FROM pixel_media_order_details WHERE order_id = ?");
                                $statement_details->execute([$order_id]);
                                $order_details = $statement_details->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <div class="row">
                                    <div class="col-md-12 d-none d-md-block" style="border: 1px solid #bcbcbc;border-radius:5px;background-color: #FFF;">
                                        <div class="row" style="line-height: 20px;padding: 20px;display: flex; align-items: center;">
                                            <div class="col-md-3">ORDER: <?php echo htmlspecialchars($order_id); ?><br><span class='product'><?php echo htmlspecialchars($item['company_name']); ?></span><br><?php echo date('d-F-Y', strtotime($item['order_date'])); ?></div>
                                            <div class="col-md-3">PRODUCT<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>" . htmlspecialchars($row['product_name']) . "</div>"; } ?>
                                            </div>
                                            <div class="col-md-1">QTY<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>{$row['quantity']}</div>"; } ?>
                                            </div>
                                            <div class="col-md-2">TOTAL<br>
                                                <?php foreach ($order_details as $row) { echo "<div class='product'>{$row['total']}</div>"; } ?>
                                            </div>
                                            <div class="col-md-1"><button class="btn btn-primary btn-lg" style="height: 35px;" onclick="view_order('<?php echo htmlspecialchars($order_id); ?>')">View</button></div>
                                            <div class="col-md-2"><button class="btn btn-success btn-lg" style="height: 35px;" onclick="download_invoice('<?php echo htmlspecialchars($order_id); ?>')"><i class="fas fa-plus rounded-circle"></i> Download<br>INVOICE</button></div>
                                        </div>
                                    </div>
                                    <!-- Mobile View -->
                                    <div class="col-md-12 d-block d-md-none" style="border: 1px solid #bcbcbc;border-radius:5px;background-color: #FFF;">
                                        <div class="row" style="padding: 20px;">
                                            <div class="col-sm-4 col-4">ORDER: <?php echo htmlspecialchars($order_id); ?><br><span class='product'><?php echo htmlspecialchars($item['company_name']); ?></span><br><?php echo date('d-F-Y', strtotime($item['order_date'])); ?></div>
                                            <div class="col-sm-5 col-5">
                                                <?php foreach ($order_details as $row) { ?>
                                                    <div class='product'><?php echo htmlspecialchars($row['product_name']); ?></div>
                                                    <div class='product'>QTY <?php echo $row['quantity']; ?></div>
                                                    <div class='product'>RS.<?php echo $row['total']; ?></div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-sm-3 col-3">
                                                <button class="btn btn-primary btn-lg" style="height: 25px;" onclick="view_order('<?php echo htmlspecialchars($order_id); ?>')">View</button>
                                                <button class="btn btn-success btn-lg" style="height:30px;margin-top: 5px;" onclick="download_invoice('<?php echo htmlspecialchars($order_id); ?>')"><i class="fas fa-plus rounded-circle"></i> Download<br>INVOICE</button>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
    <script>
        $(document).ready(function () {
            $('#activeButton').click(function () {
                $(this).removeClass('btn-default').addClass('btn-success');
                $('#deliveredButton, #nonePaidButton, #WithCourierButton').removeClass('btn-success').addClass('btn-default');
            });
            $('#deliveredButton').click(function () {
                $(this).removeClass('btn-default').addClass('btn-success');
                $('#activeButton, #nonePaidButton, #WithCourierButton').removeClass('btn-success').addClass('btn-default');
            });
            $('#nonePaidButton').click(function () {
                $(this).removeClass('btn-default').addClass('btn-success');
                $('#activeButton, #deliveredButton, #WithCourierButton').removeClass('btn-success').addClass('btn-default');
            });
            $('#WithCourierButton').click(function () {
                $(this).removeClass('btn-default').addClass('btn-success');
                $('#activeButton, #deliveredButton, #nonePaidButton').removeClass('btn-success').addClass('btn-default');
            });
        });
        function add_new_order() {
            window.location.href = './addOrder';
        }
        function view_order(order_id) {
            window.location.href = './viewOrder/' + order_id;
        }
        function download_invoice(order_id) {
            window.location.href = './report/' + order_id;
        }
    </script>
</body>
</html>