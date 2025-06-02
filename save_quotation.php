<?php
include('db.php');

header('Content-Type: application/json');

try {
    // Get the posted data
    $quotationData = json_decode($_POST['quotation_data'], true);
    
    // Validate data
    if(empty($quotationData['customer']['company_name'])){
        throw new Exception("Company name is required");
    }
    
    if(empty($quotationData['customer']['phone'])) {
        throw new Exception("Phone number is required");
    }
    
    if(empty($quotationData['items']) || count($quotationData['items']) === 0) {
        throw new Exception("At least one product item is required");
    }
    
    // Generate a unique quotation number
    $quotation_number = 'QTN-' . date('Ymd') . '-' . strtoupper(uniqid());
    
    // Prepare the data
    $valid_until = date('Y-m-d', strtotime('+7 days'));
    $quotation_items = json_encode($quotationData['items']);
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO pixel_media_quotations 
        (quotation_number, order_id, company_name, contact_person, phone, email, 
         delivery_address, subtotal, discount, total_amount, valid_until, quotation_items)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $quotation_number,
        $quotationData['order_id'],
        $quotationData['customer']['company_name'],
        $quotationData['customer']['contact_person'],
        $quotationData['customer']['phone'],
        $quotationData['customer']['email'],
        $quotationData['customer']['delivery_address'],
        $quotationData['subtotal'],
        $quotationData['discount'],
        $quotationData['total'],
        $valid_until,
        $quotation_items
    ]);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'quotation_number' => $quotation_number,
        'message' => 'Quotation saved successfully'
    ]);
    
} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>