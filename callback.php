<?php

// Headers
ini_set('max_execution_time', '1700');
set_time_limit(1700);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);

// Input Data
include('config.php');
$input = json_decode(file_get_contents("php://input"), true);
$log["input"] = $input;
$result["state"] = true;

// Check Sign

// Filter Data
$orderData = explode("--", $input["order_id"]);
if ($input["status"] == "success") {
    $log["Smart Sender"]["success"]["send"]["name"] = $orderData[3];
    $log["Smart Sender"]["success"]["result"] = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$orderData[2]."/fire", $ssToken, "POST", $log["Smart Sender"]["success"]["send"]), true);
}
if ($input["status"] == "fail" && $orderData[4] != NULL) {
    $log["Smart Sender"]["fail"]["send"]["name"] = $orderData[4];
    $log["Smart Sender"]["fail"]["result"] = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$orderData[2]."/fire", $ssToken, "POST", $log["Smart Sender"]["fail"]["send"]), true);
}

// Loging
send_forward(json_encode($log), "https://log.mufiksoft.com/cryptoCloud?identifier=callback");
