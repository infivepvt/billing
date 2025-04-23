<?php
session_start();
include('db.php');

if ($_SESSION['logged_id'] <= 0) {
    header('Location: ./');
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
    <style type="text/css">
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F7F9F9;
        }

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

        .product {
            font-size: 16px;
            font-weight: bold;
        }

        .btn-success {
            background-color: #1BA664;
            border-color: #1BA664;
            color: #fff;
        }

        /* Rectangle Calculator Styles */
        .calculator-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .calculator-container h2 {
            color: #1BA664;
            margin-bottom: 20px;
        }

        .calculator-container label {
            font-weight: bold;
        }

        .price-calculation {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .price-calculation h4 {
            color: #1BA664;
        }

        .price-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .price-col {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
            margin-bottom: 15px;
        }

        #gridContainer {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        #gridCanvas {
            border: 1px solid black;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .result-value {
            font-weight: bold;
            color: #1BA664;
            margin-left: 5px;
        }

        .price-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .price-header {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .price-header h5 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .price-body {
            padding: 15px;
        }

        .price-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .price-label {
            width: 100px;
            font-weight: 500;
            color: #555;
        }

        .result-row {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e0e0e0;
        }

        .price-value {
            font-weight: 600;
            color: #1BA664;
            font-size: 16px;
        }

        .input-group {
            flex: 1;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .quantity-input {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .sheet-calculation {
            margin-top: 15px;
            padding: 15px;
            background-color: #e9f7ef;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg" style="background-color: #1BA664;"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
                    </li>
                    <li><img src="assets/img/infive_logo.jpg"></li>
                </ul>
            </nav>

            <?php include('left_menu.php'); ?>

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Rectangle Calculator</h1>
                    </div>

                    <div class="section-body">
                        <div class="calculator-container">
                            <h2>Calculate Maximum Rectangles within A3 Paper with Safe Area and Bleed</h2>

                            <form id="rectangleForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rectWidth">Rectangle Width:</label>
                                            <div class="input-group">
                                                <input type="number" id="rectWidth" name="rectWidth"
                                                    class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="widthUnit">mm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rectHeight">Rectangle Height:</label>
                                            <div class="input-group">
                                                <input type="number" id="rectHeight" name="rectHeight"
                                                    class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="heightUnit">mm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bleed">Bleed:</label>
                                            <div class="input-group">
                                                <input type="number" id="bleed" name="bleed" class="form-control"
                                                    value="3" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">mm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit">Select Unit:</label>
                                            <select id="unit" name="unit" class="form-control" required>
                                                <option value="mm">Millimeters</option>
                                                <option value="cm">Centimeters</option>
                                                <option value="in">Inches</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" onclick="calculateRectangles()"
                                    class="btn btn-success">Calculate</button>
                            </form>

                            <div class="mt-4">
                                <h3 id="result"></h3>
                                <div class="input-group">
                                    <span>Total Rectangles per Sheet:</span>
                                    <span id="totalRectangles" class="result-value">0</span>
                                </div>
                            </div>

                            <div class="quantity-input">
                                <h4>Quantity Calculation</h4>
                                <div class="form-group">
                                    <label for="requiredQuantity">Required Quantity:</label>
                                    <div class="input-group">
                                        <input type="number" id="requiredQuantity" name="requiredQuantity"
                                            class="form-control" value="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text">cards</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="calculateSheets()" class="btn btn-success">Calculate
                                    Sheets</button>

                                <div class="sheet-calculation">
                                    <div class="input-group">
                                        <span>Sheets Required:</span>
                                        <span id="sheetsRequired" class="result-value">0</span>
                                    </div>
                                    <div class="input-group">
                                        <span>Total Cards:</span>
                                        <span id="totalCards" class="result-value">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="price-calculation">
                                <h4>Price Calculation</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="price-card">
                                            <div class="price-header" style="background-color: #f0f8ff;">
                                                <h5>Matte Finish</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="mattePrice" name="mattePrice"
                                                            class="form-control" value="330">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="matteResult" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="matteTotal" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="price-card mt-3">
                                            <div class="price-header" style="background-color: #fff0f5;">
                                                <h5>Foil Finish</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="foilPrice" name="foilPrice"
                                                            class="form-control" value="700">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="foilResult" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="foilTotal" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="price-card mt-3">
                                            <div class="price-header" style="background-color: #f0fff0;">
                                                <h5>32pt Velvet</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="velvet32Price" name="velvet32Price"
                                                            class="form-control" value="980">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="velvet32Result" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="velvet32Total" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="price-card">
                                            <div class="price-header" style="background-color: #fffaf0;">
                                                <h5>Velvet Finish</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="velvetPrice" name="velvetPrice"
                                                            class="form-control" value="480">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="velvetResult" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="velvetTotal" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="price-card mt-3">
                                            <div class="price-header" style="background-color: #f5f0ff;">
                                                <h5>Shape Cut</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="shapeCutPrice" name="shapeCutPrice"
                                                            class="form-control" value="900">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="shapeCutResult" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="shapeCutTotal" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="price-card mt-3">
                                            <div class="price-header" style="background-color: #f0f8ff;">
                                                <h5>32pt Foil</h5>
                                            </div>
                                            <div class="price-body">
                                                <div class="price-row">
                                                    <span class="price-label">Sheet Price:</span>
                                                    <div class="input-group">
                                                        <input type="number" id="foil32Price" name="foil32Price"
                                                            class="form-control" value="1280">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">LKR</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Per Card:</span>
                                                    <span id="foil32Result" class="price-value">0.00 LKR</span>
                                                </div>
                                                <div class="price-row result-row">
                                                    <span class="price-label">Total Cost:</span>
                                                    <span id="foil32Total" class="price-value">0.00 LKR</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="gridContainer">
                                <canvas id="gridCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </section>
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

    <script>
        // Update unit display when selection changes
        document.getElementById('unit').addEventListener('change', function () {
            const unit = this.value;
            document.getElementById('widthUnit').textContent = unit;
            document.getElementById('heightUnit').textContent = unit;
        });

        let rectanglesPerSheet = 0;

        function calculateRectangles() {
            const A3_WIDTH_MM = 420;
            const A3_HEIGHT_MM = 297;
            const SAFE_LEFT_RIGHT = 10;
            const SAFE_TOP_BOTTOM = 5;

            const usableWidth = A3_WIDTH_MM - 2 * SAFE_LEFT_RIGHT;
            const usableHeight = A3_HEIGHT_MM - 2 * SAFE_TOP_BOTTOM;

            let rectWidth = parseFloat(document.getElementById("rectWidth").value);
            let rectHeight = parseFloat(document.getElementById("rectHeight").value);
            const bleed = parseFloat(document.getElementById("bleed").value) || 0;
            const unit = document.getElementById("unit").value;

            if (unit === "cm") {
                rectWidth *= 10;
                rectHeight *= 10;
            } else if (unit === "in") {
                rectWidth *= 25.4;
                rectHeight *= 25.4;
            }

            if (rectWidth <= 0 || rectHeight <= 0) {
                document.getElementById("result").innerText = "Please enter valid dimensions.";
                document.getElementById("totalRectangles").textContent = "0";
                resetPriceResults();
                return;
            }

            const rectWidthWithBleed = rectWidth + 2 * bleed;
            const rectHeightWithBleed = rectHeight + 2 * bleed;

            const countWidth = Math.floor(usableWidth / rectWidthWithBleed);
            const countHeight = Math.floor(usableHeight / rectHeightWithBleed);
            rectanglesPerSheet = countWidth * countHeight;

            document.getElementById("result").innerText = `Maximum rectangles (with ${bleed}mm bleed and safe area):`;
            document.getElementById("totalRectangles").textContent = rectanglesPerSheet;

            drawGrid(rectWidthWithBleed, rectHeightWithBleed, SAFE_LEFT_RIGHT, SAFE_TOP_BOTTOM, usableWidth, usableHeight, countWidth, countHeight);
            calculatePrices(rectanglesPerSheet);

            // Auto-calculate sheets if quantity is already entered
            if (document.getElementById("requiredQuantity").value > 0) {
                calculateSheets();
            }
        }

        function calculateSheets() {
            const requiredQuantity = parseInt(document.getElementById("requiredQuantity").value) || 0;
            if (rectanglesPerSheet <= 0 || requiredQuantity <= 0) {
                document.getElementById("sheetsRequired").textContent = "0";
                document.getElementById("totalCards").textContent = "0";
                resetTotalCosts();
                return;
            }

            const sheetsRequired = Math.ceil(requiredQuantity / rectanglesPerSheet);
            const totalCards = sheetsRequired * rectanglesPerSheet;

            document.getElementById("sheetsRequired").textContent = sheetsRequired;
            document.getElementById("totalCards").textContent = totalCards;

            // Update price calculations
            calculatePrices(rectanglesPerSheet);
        }

        function drawGrid(rectWidthWithBleed, rectHeightWithBleed, offsetX, offsetY, usableWidth, usableHeight, countWidth, countHeight) {
            const scale = 2;
            const canvas = document.getElementById("gridCanvas");
            const canvasWidth = 420 * scale;
            const canvasHeight = 297 * scale;
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;

            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);

            // Draw safe area outline (Green)
            ctx.strokeStyle = "#1BA664";
            ctx.lineWidth = 2;
            const safeX = offsetX * scale;
            const safeY = offsetY * scale;
            const safeWidth = usableWidth * scale;
            const safeHeight = usableHeight * scale;
            ctx.strokeRect(safeX, safeY, safeWidth, safeHeight);

            const gridOffsetX = (safeWidth - countWidth * rectWidthWithBleed * scale) / 2;
            const gridOffsetY = (safeHeight - countHeight * rectHeightWithBleed * scale) / 2;

            for (let i = 0; i < countWidth; i++) {
                for (let j = 0; j < countHeight; j++) {
                    const x = safeX + gridOffsetX + i * rectWidthWithBleed * scale;
                    const y = safeY + gridOffsetY + j * rectHeightWithBleed * scale;

                    ctx.strokeStyle = "#333";
                    ctx.lineWidth = 1;
                    ctx.strokeRect(x, y, rectWidthWithBleed * scale, rectHeightWithBleed * scale);
                }
            }
        }

        function resetPriceResults() {
            const prices = ["matte", "velvet", "foil", "shapeCut", "velvet32", "foil32"];
            prices.forEach(type => {
                document.getElementById(type + "Result").textContent = "0.00 LKR";
                document.getElementById(type + "Total").textContent = "0.00 LKR";
            });
        }

        function resetTotalCosts() {
            const prices = ["matte", "velvet", "foil", "shapeCut", "velvet32", "foil32"];
            prices.forEach(type => {
                document.getElementById(type + "Total").textContent = "0.00 LKR";
            });
        }

        function calculatePrices(totalRectangles) {
            const requiredQuantity = parseInt(document.getElementById("requiredQuantity").value) || 0;
            if (totalRectangles <= 0 || requiredQuantity <= 0) {
                resetPriceResults();
                return;
            }

            const prices = ["matte", "velvet", "foil", "shapeCut", "velvet32", "foil32"];
            prices.forEach(type => {
                const price = parseFloat(document.getElementById(type + "Price").value) || 0;
                const sheetsRequired = Math.ceil(requiredQuantity / totalRectangles);
                const totalCost = (price * sheetsRequired).toFixed(2);
                const perCardCost = (totalCost / requiredQuantity).toFixed(2);

                document.getElementById(type + "Result").textContent = `${perCardCost} LKR`;
                document.getElementById(type + "Total").textContent = `${totalCost} LKR`;
            });
        }

        function updateTotalCosts(sheetsRequired) {
            const requiredQuantity = parseInt(document.getElementById("requiredQuantity").value) || 0;
            if (sheetsRequired <= 0 || requiredQuantity <= 0) {
                resetTotalCosts();
                return;
            }

            const prices = ["matte", "velvet", "foil", "shapeCut", "velvet32", "foil32"];
            prices.forEach(type => {
                const price = parseFloat(document.getElementById(type + "Price").value) || 0;
                const totalCost = (price * sheetsRequired).toFixed(2);
                const perCardCost = (totalCost / requiredQuantity).toFixed(2);

                document.getElementById(type + "Result").textContent = `${perCardCost} LKR`;
                document.getElementById(type + "Total").textContent = `${totalCost} LKR`;
            });
        }
    </script>
</body>

</html>