<?php

$host = 'localhost';
$name = 'u263749830_bill_invoice';
$user = 'u263749830_billing';
$password = 'KE#iU@H0d^';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}

define('BASE_URL', 'https://infiveprint.com/billing/'); // Change this to your local project URL

function generateUniqueRandomNumber($pdo) {
    do {
        // Generate a random 6-digit number
        $randomNumber = mt_rand(100000, 999999);

        // Check if the number exists in the database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pixel_media_order WHERE order_id = ?");
        $stmt->execute([$randomNumber]);
        $count = $stmt->fetchColumn();
    } while ($count > 0); // Ensure it's unique

    return $randomNumber;
}

?>
