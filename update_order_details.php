<?php
session_start();
include('db.php');

if(empty($_SESSION['logged_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $products = $_POST['products'];
    $advance_paid = $_POST['advance_paid'];
    
    try {
        $pdo->beginTransaction();
        
        // First delete all existing products for this order
        $stmt = $pdo->prepare("DELETE FROM pixel_media_order_details WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        // Insert new products
        $new_total = 0;
        foreach($products as $product) {
            // Convert specifications to JSON
            $specification = isset($product['spec']) ? json_encode($product['spec']) : '';
            
            $stmt = $pdo->prepare("INSERT INTO pixel_media_order_details 
                (order_id, product_name, product_specification, quantity, price, total) 
                VALUES (?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $order_id,
                $product['product_name'],
                $specification,
                $product['quantity'],
                $product['price'],
                $product['total']
            ]);
            
            $new_total += $product['total'];
        }
        
        // Update order total
        $stmt = $pdo->prepare("UPDATE pixel_media_order SET 
            order_total_amount = ?,
            payment_amount = ?
            WHERE order_id = ?");
            
        $stmt->execute([
            $new_total,
            $advance_paid,
            $order_id
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Order updated successfully',
            'new_total' => $new_total,
            'due' => $new_total - $advance_paid
        ]);
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>