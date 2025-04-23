<?php
include('db.php');

header('Content-Type: application/json');

$quotation_number = isset($_POST['quotation_number']) ? $_POST['quotation_number'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (empty($quotation_number)) {
    echo json_encode(['success' => false, 'message' => 'Quotation number is required']);
    exit;
}

$allowed_statuses = ['draft', 'sent', 'accepted', 'rejected'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE pixel_media_quotations SET status = ? WHERE quotation_number = ?");
    $stmt->execute([$status, $quotation_number]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}