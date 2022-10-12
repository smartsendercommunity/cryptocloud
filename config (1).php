<?php

// Config
$ssToken = "q6lYgUcGt0SCXuqzKHb0DSkw8ZPhTMcQC4UUZxhC7W6q2lzTyZjaNX7VDTwB";
$ccId = "xaJOzt6YZoYE4WOG";
$ccToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjU4MywiZXhwIjo4ODA2NTQwOTEzMX0._ynvsWKcqxK25P9y2V1Nl__OPOwdx44P_qk6KAxRrSo";

// Functions
function send_forward($inputJSON, $link){
    $request = 'POST';	
    $descriptor = curl_init($link);
     curl_setopt($descriptor, CURLOPT_POSTFIELDS, $inputJSON);
     curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($descriptor, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
     curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $request);
    $itog = curl_exec($descriptor);
    curl_close($descriptor);
    return $itog;
}
function send_bearer($url, $token, $type = "GET", $param = []){
    $descriptor = curl_init($url);
     curl_setopt($descriptor, CURLOPT_POSTFIELDS, json_encode($param));
     curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($descriptor, CURLOPT_HTTPHEADER, array('User-Agent: M-Soft Integration', 'Content-Type: application/json', 'Authorization: Bearer '.$token)); 
     curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $type);
    $itog = curl_exec($descriptor);
    curl_close($descriptor);
    return $itog;
}
function send_request($url, $header = [], $type = 'GET', $param = [], $raw = "json") {
    $descriptor = curl_init($url);
    if ($type != "GET") {
        if ($raw == "json") {
             curl_setopt($descriptor, CURLOPT_POSTFIELDS, json_encode($param));
            $header[] = 'Content-Type: application/json';
        } else if ($raw == "form") {
             curl_setopt($descriptor, CURLOPT_POSTFIELDS, http_build_query($param));
            $header[] = 'Content-Type: application/x-www-form-urlencoded';
        } else {
             curl_setopt($descriptor, CURLOPT_POSTFIELDS, $param);
        }
    }
    $header[] = 'User-Agent: M-Soft Integration(https://mufiksoft.com)';
     curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($descriptor, CURLOPT_HTTPHEADER, $header); 
     curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $type);
    $itog = curl_exec($descriptor);
    //$itog["code"] = curl_getinfo($descriptor, CURLINFO_RESPONSE_CODE);
    curl_close($descriptor);
    return $itog;
}