<?php
session_start();
include('db.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Order Tracking | Infive Print</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/animate.css/animate.min.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">

    <style>
        :root {
            --primary-color: #1BA664;
            --secondary-color: #47AA7B;
            --light-color: #F7F9F9;
            --dark-color: #2C3E50;
            --accent-color: #E74C3C;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
        }
        
        .tracking-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .tracking-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem !important;
            text-align: center;
        }
        
        .tracking-logo {
            width: 150px !important;
            height: auto;
            margin-bottom: 0.5rem !important;
            transition: transform 0.3s;
        }
        
        .tracking-logo:hover {
            transform: scale(1.05);
        }
        
        .welcome-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-top: 0.5rem !important;
        }
        
        .welcome-text h2 {
            font-size: 1.5rem !important;
            margin-bottom: 0.5rem !important;
        }
        
        .welcome-text p {
            margin-bottom: 0.3rem !important;
            font-size: 0.9rem !important;
            line-height: 1.4 !important;
        }
        
        .order-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Timeline Styles */
        .timeline {
            position: relative;
            padding: 2rem 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .timeline-step {
            position: relative;
            margin-bottom: 3rem;
            z-index: 2;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .timeline-step.animate {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Step Icon Styles */
        .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 3px solid;
            position: relative;
            margin: 0 auto 1rem;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform-style: preserve-3d;
        }
        
        .step-icon.completed {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: scale(1.1) rotate(0deg);
            box-shadow: 0 5px 15px rgba(27, 166, 100, 0.3);
            animation: bounceIn 0.5s ease forwards;
        }
        
        .step-icon.current {
            background-color: white;
            border-color: var(--primary-color);
            color: var(--primary-color);
            animation: pulse 1.5s infinite, blink 1.5s infinite;
            position: relative;
        }
        
        .step-icon.pending {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
            transform: scale(0.95);
        }
        
        .step-icon:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .pulse-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: pulse 2s infinite;
            opacity: 0;
        }
        
        .step-content {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            position: relative;
        }
        
        .step-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .step-date {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .step-connector {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: calc(100% - 60px);
            background-color: #e0e0e6;
        }
        
        .step-connector.completed {
            background-color: var(--primary-color);
        }
        
        /* Horizontal Timeline for Desktop */
        .timeline-horizontal {
            display: flex;
            position: relative;
            padding: 2rem 0;
            overflow-x: auto;
        }
        
        .timeline-horizontal::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .timeline-step-horizontal {
            flex: 1;
            position: relative;
            padding: 0 1rem;
            min-width: 150px;
            z-index: 2;
        }
        
        .step-icon-horizontal {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 3px solid;
            margin: 0 auto 1rem;
            background: white;
            position: relative;
            z-index: 3;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .step-icon-horizontal.completed {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: scale(1.1) rotate(0deg);
            box-shadow: 0 5px 15px rgba(27, 166, 100, 0.3);
        }
        
        .step-icon-horizontal.current {
            background-color: white;
            border-color: var(--primary-color);
            color: var(--primary-color);
            animation: pulse 1.5s infinite, blink 1.5s infinite;
        }
        
        .step-icon-horizontal.pending {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
        }
        
        .step-icon-horizontal:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .step-connector-horizontal {
            position: absolute;
            top: 50%;
            left: calc(50% + 25px);
            right: calc(-50% + 25px);
            height: 4px;
            background-color: #e0e0e6;
            z-index: 1;
        }
        
        .step-connector-horizontal.completed {
            background-color: var(--primary-color);
        }
        
        .step-content-horizontal {
            text-align: center;
            padding: 0.5rem;
        }
        
        .step-title-horizontal {
            font-weight: 600;
            font-size: 0.7rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .step-date-horizontal {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .btn-download {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-download:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Animations */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(27, 166, 100, 0.4);
            }
            70% {
                box-shadow: 0 0 0 12px rgba(27, 166, 100, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(27, 166, 100, 0);
            }
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-5px) scale(1.05);
            }
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Blinking Animation for Current Status Only */
        @keyframes blink {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        .current-status-blink {
            animation: blink 1.5s infinite;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .timeline::before {
                left: 30px;
                background-color: #e0e0e0;
            }
            
            .timeline-step {
                padding-left: 70px;
                position: relative;
            }
            
            .step-icon {
                position: absolute;
                left: 0;
                margin: 0;
                width: 60px;
                height: 60px;
                font-size: 1.25rem;
                border-width: 3px;
            }
            
            .step-connector {
                position: absolute;
                top: 60px;
                left: 30px;
                width: 4px;
                height: calc(100% - 60px);
                transform: none;
            }
            
            .step-content {
                text-align: left;
                margin-bottom: 2rem;
                padding: 1rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .tracking-header {
                padding: 1.5rem;
            }
            
            .welcome-text {
                font-size: 1rem;
            }
            
            .order-info {
                padding: 1rem;
            }
            
            .step-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
        }
        
        /* Order Summary Table */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .table tbody tr:hover {
            background-color: rgba(27, 166, 100, 0.05);
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: var(--primary-color);
        }
        
        .badge-primary {
            background-color: var(--secondary-color);
        }

        .current-row {
            background-color: rgba(27, 166, 100, 0.1) !important;
            animation: blink 1.5s infinite;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="tracking-card">
                    <!-- Header Section -->
                    <div class="tracking-header">
                        <img src="../assets/img/logo.png" alt="Infive Print" class="tracking-logo img-fluid">
                        <div class="welcome-text">
                            <h2 class="mb-2">Track Your Order</h2>
                            <p>Welcome to Infive Print's Order Tracking Page!</p>
                            <p>Here, you can follow your order's journey from creation to delivery.</p>
                            <p class="mb-0">If you have any questions or need assistance, feel free to reach out to us.</p>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="p-4 p-md-5">
                        <?php
                        $statusArray = array('ORDER PLACED', 'ADVANCE RECEIVED', 'FULL PAYMENT RECEIVED', 'DESIGN SUBMITED', 'PRINTED', 'PACKAGE ALREADY WITH COURIER SERVICE', 'COMPLETE');
                        $statement = $pdo->prepare("SELECT * FROM pixel_media_order WHERE order_id = ?");
                        $statement->execute(array($order_id));
                        $order = $statement->fetch(PDO::FETCH_ASSOC);
                        
                        $currentStatus = $order['order_status'];
                        $currentPosition = array_search($currentStatus, $statusArray);
                        ?>
                        
                        <!-- Order Information -->
                        <div class="order-info mb-5">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-alt mr-2"></i> ORDER DATE: <strong><?php echo date('d F Y', strtotime($order['order_date'])); ?></strong></h5>
                                    <h5><i class="fas fa-receipt mr-2"></i> ORDER ID: <strong><?php echo $order_id; ?></strong></h5>
                                </div>
                                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                                    <a class="btn btn-download" href="<?php echo BASE_URL . "/report/$order_id"; ?>">
                                        <i class="fas fa-download mr-2"></i> Download Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Status -->
                        <div class="alert alert-primary mb-5 current-status-blink">
                            <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i> Current Status: <strong><?php echo $currentStatus; ?></strong></h5>
                        </div>
                        
                        <!-- Desktop Timeline (Horizontal) -->
                        <div class="d-none d-lg-block">
                            <div class="timeline-horizontal">
                                <?php foreach ($statusArray as $index => $status): ?>
                                    <?php 
                                    $isCompleted = $index < $currentPosition;
                                    $isCurrent = $index == $currentPosition;
                                    $dateField = strtolower(str_replace(' ', '_', $status)) . '_date';
                                    $statusDate = !empty($order[$dateField]) ? date('d M Y', strtotime($order[$dateField])) : '';
                                    ?>
                                    
                                    <div class="timeline-step-horizontal">
                                        <div class="step-icon-horizontal <?php echo $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending'); ?>">
                                            <?php echo $index + 1; ?>
                                        </div>
                                        
                                        <?php if ($index < count($statusArray) - 1): ?>
                                            <div class="step-connector-horizontal <?php echo $isCompleted ? 'completed' : ''; ?>"></div>
                                        <?php endif; ?>
                                        
                                        <div class="step-content-horizontal">
                                            <h6 class="step-title-horizontal"><?php echo $status; ?></h6>
                                            <?php if ($isCompleted && $statusDate): ?>
                                                <div class="step-date-horizontal"><?php echo $statusDate; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Mobile Timeline (Vertical) -->
                        <div class="d-lg-none">
                            <div class="timeline">
                                <?php foreach ($statusArray as $index => $status): ?>
                                    <?php 
                                    $isCompleted = $index < $currentPosition;
                                    $isCurrent = $index == $currentPosition;
                                    $dateField = strtolower(str_replace(' ', '_', $status)) . '_date';
                                    $statusDate = !empty($order[$dateField]) ? date('d M Y', strtotime($order[$dateField])) : '';
                                    ?>
                                    
                                    <div class="timeline-step" style="--animation-order: <?php echo $index; ?>">
                                        <div class="step-icon <?php echo $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending'); ?>">
                                            <?php echo $index + 1; ?>
                                            <?php if ($isCurrent): ?>
                                                <div class="pulse-ring"></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($index < count($statusArray) - 1): ?>
                                            <div class="step-connector <?php echo $isCompleted ? 'completed' : ''; ?>"></div>
                                        <?php endif; ?>
                                        
                                        <div class="step-content">
                                            <h5 class="step-title"><?php echo $status; ?></h5>
                                            <?php if ($isCompleted && $statusDate): ?>
                                                <div class="step-date"><i class="far fa-calendar-alt mr-2"></i><?php echo $statusDate; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="mt-5 pt-4">
                            <h4 class="mb-4"><i class="fas fa-clipboard-list mr-2"></i> Order Summary</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Status</th>                                           
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($statusArray as $index => $status): ?>
                                            <?php 
                                            $dateField = strtolower(str_replace(' ', '_', $status)) . '_date';
                                            $statusDate = !empty($order[$dateField]) ? date('d M Y, h:i A', strtotime($order[$dateField])) : 'Pending';
                                            ?>
                                            <tr class="<?php echo $index <= $currentPosition ? 'table-success' : ''; ?> <?php echo $index == $currentPosition ? 'current-row' : ''; ?>">
                                                <td><?php echo $status; ?></td>
                                                <td>
                                                    <?php if ($index < $currentPosition): ?>
                                                        <span class="badge badge-success">Completed</span>
                                                    <?php elseif ($index == $currentPosition): ?>
                                                        <span class="badge badge-primary">Current</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="p-4 bg-light text-center">
                        <p class="mb-0">Thank you for choosing <strong>Infive Print</strong>!</p>
                        <p class="mb-0">For any inquiries, please contact our customer support.</p>
                    </div>
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

    <!-- Template JS File -->
    <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize all steps as not animated
            $('.timeline-step').each(function() {
                $(this).addClass('pending-animation');
            });
            
            // Function to animate steps
            function animateSteps() {
                $('.timeline-step.pending-animation').each(function(index) {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    // If element is in viewport
                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        setTimeout(function() {
                            $(this).removeClass('pending-animation').addClass('animate');
                        }.bind(this), index * 150); // Increased delay for better sequencing
                    }
                });
            }
            
            // Animate on scroll and on load
            $(window).scroll(animateSteps);
            animateSteps();
            
            // Add hover effects for desktop
            if ($(window).width() > 992) {
                $('.step-icon, .step-icon-horizontal').hover(
                    function() {
                        $(this).css('transform', 'scale(1.15) rotate(5deg)');
                    },
                    function() {
                        if (!$(this).hasClass('current') && !$(this).hasClass('completed')) {
                            $(this).css('transform', 'scale(1)');
                        }
                    }
                );
            }
        });
    </script>
</body>

</html>