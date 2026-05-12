<?php

require 'vendor/autoload.php';

use Twilio\Rest\Client;

function sendSMS($phone, $message)
{
    $sid = "YOUR_TWILIO_SID";
    $token = "YOUR_TWILIO_AUTH_TOKEN";
    $twilioNumber = "YOUR_TWILIO_PHONE_NUMBER";

    $client = new Client($sid, $token);

    try {

        $client->messages->create($phone, [
            'from' => $twilioNumber,
            'body' => $message
        ]);

        return true;

    } catch (Exception $e) {

        return false;
    }
}
?>