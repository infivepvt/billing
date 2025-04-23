<?php
include('db.php');

if (isset($_GET['quotation_number'])) {
    $quotation_number = $_GET['quotation_number'];
    
    $stmt = $pdo->prepare("DELETE FROM pixel_media_quotations WHERE quotation_number = ?");
    $stmt->execute([$quotation_number]);
    
    header("Location: quotation_history.php");
    exit;
}
?>