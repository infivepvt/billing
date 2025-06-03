<?php
include('db.php');

header('Content-Type: application/json');

try {
    // Get the posted data
    if (!isset($_POST['quotation_data'])) {
        throw new Exception("No quotation data provided");
    }

    $quotationData = json_decode($_POST['quotation_data'], true);
    
    // Validate data
    if (empty($quotationData['customer']['company_name'])) {
        throw new Exception("Company name is required");
    }
    
    if (empty($quotationData['customer']['phone'])) {
        throw new Exception("Phone number is required");
    }
    
    if (empty($quotationData['items']) || count($quotationData['items']) === 0) {
        throw new Exception("At least one product item is required");
    }
    
    // Calculate adjusted total for coupon eligibility
    $subtotal = floatval($quotationData['subtotal']);
    $design_charges = floatval($quotationData['design_charges'] ?? 0.00);
    $delivery_charges = 0.00;
    $design_product_charges = 0.00;

    // Iterate through items to identify Delivery and Design-related products
    foreach ($quotationData['items'] as $item) {
        if (!isset($item['product']) || !isset($item['total'])) {
            throw new Exception("Invalid item data");
        }

        $product_name = strtolower($item['product']);
        $item_total = floatval($item['total']);

        if ($product_name === 'delivery') {
            $delivery_charges += $item_total;
        } elseif (strpos($product_name, 'design') !== false) {
            $design_product_charges += $item_total;
        }
    }

    // Calculate adjusted total for coupon eligibility
    $adjusted_total = $subtotal - $design_charges - $delivery_charges - $design_product_charges;

    // Determine coupon type based on adjusted total
    $coupon_type = $quotationData['coupon_type'] ?? 'None';
    $expected_coupon_type = 'None';

    if ($adjusted_total >= 3000 && $adjusted_total < 5000) {
        $expected_coupon_type = 'Free Delivery';
    } elseif ($adjusted_total >= 5000 && $adjusted_total < 8000) {
        $expected_coupon_type = 'Rs. 500 Discount';
    } elseif ($adjusted_total >= 8000) {
        $expected_coupon_type = 'Rs. 1000 Discount';
    }

    // Validate coupon type (ensure frontend and backend agree)
    if ($coupon_type !== $expected_coupon_type) {
        // Log discrepancy for debugging but proceed with backend-calculated coupon type
        error_log("Coupon type mismatch: Frontend sent '$coupon_type', Backend calculated '$expected_coupon_type'");
        $coupon_type = $expected_coupon_type;
    }

    // Verify total amount (ensure it matches expected calculation)
    $discount = floatval($quotationData['discount'] ?? 0.00);
    $total = floatval($quotationData['total']);
    $expected_total = $subtotal - $discount;

    // If Free Delivery was applied, ensure Delivery price is 0
    if ($coupon_type === 'Free Delivery' && $delivery_charges > 0) {
        // Adjust total if Free Delivery was applied but Delivery charges are still present
        $total = $expected_total - $delivery_charges;
        // Update items to reflect Free Delivery
        foreach ($quotationData['items'] as &$item) {
            if (strtolower($item['product']) === 'delivery') {
                $item['price'] = 0;
                $item['total'] = 0;
            }
        }
        unset($item); // Unset reference to avoid issues
    }

    if (abs($total - $expected_total) > 0.01 && $coupon_type !== 'Free Delivery') {
        throw new Exception("Total amount mismatch");
    }

    // Generate a unique quotation number
    $quotation_number = 'QTN-' . date('Ymd') . '-' . strtoupper(uniqid());
    
    // Prepare the data
    $valid_until = date('Y-m-d', strtotime('+7 days'));
    $quotation_items = json_encode($quotationData['items']);
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO pixel_media_quotations 
        (quotation_number, order_id, company_name, contact_person, phone, email, 
         delivery_address, subtotal, discount, coupon_type, total_amount, valid_until, quotation_items, design_charges)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $quotation_number,
        $quotationData['order_id'] ?? null,
        $quotationData['customer']['company_name'],
        $quotationData['customer']['contact_person'] ?? '',
        $quotationData['customer']['phone'],
        $quotationData['customer']['email'] ?? '',
        $quotationData['customer']['delivery_address'] ?? '',
        $subtotal,
        $discount,
        $coupon_type,
        $total,
        $valid_until,
        $quotation_items,
        $design_charges
    ]);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'quotation_number' => $quotation_number,
        'message' => 'Quotation saved successfully',
        'coupon_type' => $coupon_type,
        'adjusted_total' => $adjusted_total
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>