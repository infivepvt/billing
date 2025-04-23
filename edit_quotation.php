<?php
include('db.php');

// Get the quotation number from URL
$quotation_number = isset($_GET['quotation_number']) ? $_GET['quotation_number'] : '';

// Fetch quotation details from database
$stmt = $pdo->prepare("SELECT * FROM pixel_media_quotations WHERE quotation_number = ?");
$stmt->execute([$quotation_number]);
$quotation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quotation) {
    die("Quotation not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the form and update the quotation
    // Similar to your existing order creation logic
    // Then redirect back to view page
    header("Location: view_quotation.php?quotation_number=" . urlencode($quotation_number));
    exit;
}

// Decode the items
$items = json_decode($quotation['quotation_items'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quotation <?php echo $quotation_number; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Edit Quotation <?php echo $quotation_number; ?></h2>
        
        <form method="post">
            <!-- Similar to your order creation form, but pre-filled with quotation data -->
            <!-- Include all the fields from your order form -->
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Quotation</button>
                <a href="view_quotation.php?quotation_number=<?php echo urlencode($quotation_number); ?>" 
                   class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>