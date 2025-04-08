<?php
extract($_POST);
include('db.php'); 
$payment_amount = 0;

if($order_status == 'ORDER PLACED' )
	$current_status = 'None Paid';
else if($order_status == 'COMPLETE')
	$current_status = 'Delivered';
else if ($order_status == 'PACKAGE ALREADY WITH COURIER SERVICE' )
	$current_status = 'With Courier';
else{

	$current_status = 'Active';
	if($order_status == 'ADVANCE RECEIVED' )
		$payment_amount = $advance_amount;
	if($order_status == 'FULL PAYMENT RECEIVED' )
		$payment_amount = $full_amount;
	
}

$order_page_link = BASE_URL.'/order/'.$order_id;

$statement = $pdo->prepare(" UPDATE pixel_media_order SET order_status = ?, current_status = ?, payment_amount= payment_amount + ? WHERE order_id = ?");
$statement->execute(array($order_status, $current_status, $payment_amount, $order_id));
//print $order_status;

if($order_status == 'ORDER PLACED')
	$sms = "Your print order has been successfully created. Please check the process here: $order_page_link. - Infive Print";


if($order_status == 'ADVANCE RECEIVED' )
	$sms = "Your print order has been successfully created and Advance payment of RS.$advance_amount received! Track your order process here: $order_page_link Thank you. - Infive Print";


if($order_status == 'FULL PAYMENT RECEIVED' )
	$sms = "Your print order has been successfully created and full payment of RS.$full_amount received! Track your order process here: $order_page_link Thank you. - Infive Print";

if($order_status == 'DESIGN SUBMITED' )
	$sms = "We've sent the design files to your WhatsApp number. Please review and confirm. - Infive Print";

if($order_status == 'PRINTED' )
	$sms = "Your order has been printed and will be handed over to the courier soon. Thank you for choosing Infive Print!";

if($order_status == 'PACKAGE ALREADY WITH COURIER SERVICE' ){
	$tracking_no = $_POST['tracking_no'];
	$sms = "Your order has been handed over to the courier. Track it here: $tracking_no. Thank you for choosing Infive Print!";
}

if($order_status == 'COMPLETE' )
	$sms = "Hey there! We'd love to hear your thoughts. Mind sharing a quick feedback to Google Place and Facebook?
https://search.google.com/local/writereview?placeid=ChIJIZiGeXg74zoRWfOQQBwFj_k";

print $sms;

$statement = $pdo->prepare(" SELECT * FROM pixel_media_order  WHERE order_id = ?");
$statement->execute(array( $order_id));
$order = $statement->fetch(PDO::FETCH_ASSOC);
$MSISDN = $order['phone'];

//$MSISDN = '0775524866';
$SRC = 'InfivePrint';
$MESSAGE = urldecode($sms);
$AUTH = "2001|d904j2TA6FS18E1XsQIyo8vTyqgfegcvfUsFimjZ";  //Replace your Access Token

$msgdata = array("recipient"=>$MSISDN, "sender_id"=>$SRC, "message"=>$MESSAGE);
		
$curl = curl_init();

//IF you are running in locally and if you don't have https/SSL. then uncomment bellow two lines
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://sms.send.lk/api/v3/sms/send",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($msgdata),
  CURLOPT_HTTPHEADER => array(
	"accept: application/json",
	"authorization: Bearer $AUTH",
	"cache-control: no-cache",
	"content-type: application/x-www-form-urlencoded",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  //echo "cURL Error #:" . $err;
} else {
  //echo $response;
}

?>