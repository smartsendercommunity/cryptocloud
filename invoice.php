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

// Filter Data
if ($input["userId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'userId' is missing";
}
if ($input["amount"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'amount' is missing";
}
if ($input["action"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'action' is missing";
}
if ($result["state"] === false) {
    echo json_encode($result);
    exit;
}

// Checkout
if ($input["amount"] == "checkout") {
    $pages = 1;
    for ($page = 1; $page <= $pages; $page++) {
        $getCheckout = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$input["userId"]."/checkout?limitation=20&page=".$page, $ssToken), true);
        $log["checkout"][$page] = $getCheckout;
        $pages = $getCheckout["cursor"]["pages"];
        if ($getCheckout["collection"] != NULL && is_array($getCheckout["collection"])) {
            foreach ($getCheckout["collection"] as $product) {
                $sum[] = $product["cash"]["amount"] * $product["pivot"]["quantity"];
                $input["currency"] = $product["cash"]["currency"];
            }
        } else {
            $result["state"] = false;
            $result["error"]["message"][] = "failed get checkout";
            $result["error"]["Smart Sender"] = $getCheckout;
            echo json_encode($result);
            exit;
        }
    }
    $input["amount"] = array_sum($sum);
}

// Create invoice
$invoice["shop_id"] = $ccId;
$invoice["amount"] = $input["amount"];
$invoice["order_id"] = time()."--".mt_rand(100000, 999999)."--".$input["userId"]."--".$input["action"];
if ($input["failAction"] != NULL) {
    $invoice["order_id"] = $invoice["order_id"]."--".$input["failAction"];
}
if ($input["currency"] != NULL) {
    $invoice["currency"] = $input["currency"];
}
if ($input["email"] != NULL) {
    $invoice["email"] = $input["email"];
}
$headers[] = "Authorization: Token ".$ccToken;

$log["send"] = $invoice;
$result["result"] = json_decode(send_request("https://api.cryptocloud.plus/v1/invoice/create", $headers, "POST", $invoice), true);
$log["result"] = $result["result"];
send_forward(json_encode($log), "https://log.mufiksoft.com/cryptoCloud");

// print result
echo json_encode($result);
