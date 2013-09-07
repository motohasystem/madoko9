<?php
// Get the PHP helper library from twilio.com/docs/php/install
require_once('/Services/Twilio.php'); // Loads the library
 
    $To = $_REQUEST['To'];
    $Message = $_REQUEST['Message'];

// Your Account Sid and Auth Token from twilio.com/user/account
$sid = "ACb6a2a22711d50f770902b427997bfbb9"; 
$token = getenv("TWILIO_AUTH_TOKEN"); 
$client = new Services_Twilio($sid, $token);
 
$message = $client->account->sms_messages->create("+12077473988", $To, $Message, array());
echo $message->sid;
?>
