<?php

class itbitApi
{

  var $base_url,
      $client_key,
      $secret,
      $user_id;

  function __construct($apiEndpoint, $apiKey, $apiSecret, $userId){
    $this->base_url         = $apiEndpoint;
    $this->client_key       = $apiKey;
    $this->secret           = $apiSecret;
    $this->user_id          = $userId;
  }

  // Function that make the request header
  private function makeHeaders($method, $url, $body=''){
    // get current timestamp
    $timestamp = time() * 1000;

    // generate a nonce from timestamp + random number
    $nonce = $timestamp + mt_rand(1,1000);

    if($body != '') $body = json_encode($body);

    $signature = $this->signMessage($method, $url, $body, $nonce, $timestamp);

    return [
        "Authorization: $this->client_key:$signature",
        "Content-Type: application/json",
        "X-Auth-Timestamp: $timestamp",
        "X-Auth-Nonce: $nonce"
    ];
  }


  // Function that make the request header

  private function signMessage($method, $url, $body, $nonce, $timestamp){

    $message = $nonce . stripslashes(json_encode([$method, $url, addslashes($body), (string)$nonce, (string)$timestamp]));
    //echo $message;
    // make the hast digest using the message;
    $hash_digest = hash('sha256',$message, true);

    // make the hmac with the hash digest
    $hmac_digest = hash_hmac('sha512', utf8_encode($url) . $hash_digest, utf8_encode($this->secret),true);

    $signature = base64_encode($hmac_digest);

    return $signature;
  }

  // function that send the request by using curl
  private function sendRequest($method, $url, $body=""){

    $headers = $this->makeHeaders($method, $url, $body);
    //print_r($headers);
    //echo $url;

    if($body != "") $body = json_encode($body);

    //echo $body;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method );
    if($method != 'GET'){
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body );
    }

    // return request object
    $rawData = curl_exec($curl);
    $info = curl_getinfo ($curl);
    curl_close($curl);

    //print_r($info);   // uncomment this to check the request object of curl

    $json = json_decode(trim($rawData));

    if($json) return $json;

    return trim($rawData);
  }


  /**
  ------------------------------------------------------------------------------------------------------
  PUBLIC API
  ------------------------------------------------------------------------------------------------------
  **/


  // API: get tickers
  public function getTickers($tickerSymbol){
    $url = "$this->base_url/markets/".$tickerSymbol."/ticker";
    return $this->sendRequest('GET', $url);
  }

  // API: get Order Book
  public function getOrderBooks($tickerSymbol){
    $url = "$this->base_url/markets/".$tickerSymbol."/order_book";
    return $this->sendRequest('GET', $url);
  }


  /**
  ------------------------------------------------------------------------------------------------------
  PRIVATE API
  ------------------------------------------------------------------------------------------------------
  **/

  // API: Create a new wallet
  public function createWallet($walletName){

    $post_body = [
        "name"=>$walletName,
        "userId"=>$this->user_id
    ];
    $url = "$this->base_url/wallets";
    return $this->sendRequest("POST", $url, $post_body);
  }



  // API: get all wallets
  public function getAllWallets(){
    $url = "$this->base_url/wallets?userId=".$this->user_id;
    return $this->sendRequest('GET', $url);
  }



  // API: get Wallet info
  public function getWallet($wallet_id){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $url = "$this->base_url/wallets/$walwallet_idlet";
    return $this->sendRequest('GET', $url);
  }


  // API: get Wallet Balances
  public function getWalletBalance($wallet_id, $currencyCode = 'USD'){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $url = "$this->base_url/wallets/$walwallet_idlet/balances/$currencyCode";
    return $this->sendRequest('GET', $url);
  }


  // API: get Wallet Trades
  public function getWalletTrades($wallet_id){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $url = "$this->base_url/wallets/$wallet_id/trades";
    return $this->sendRequest('GET', $url);
  }

  // API: creating new Order
  public function createOrder($wallet_id, $side, $type, $currency, $amount, $price, $instrument){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $order_data = [
                    'side' => $side,
                    'type' => $type,
                    'currency' => $currency,
                    'amount' => (string)$amount,
                    'price' => (string)$price,
                    'instrument' => $instrument
                  ];

    $url = "$this->base_url/wallets/$wallet_id/orders";
    return $this->sendRequest('POST', $url, $order_data);
  }

  // API: creating new Order
  public function createOrderWithDisplay($wallet_id, $side, $type, $currency, $amount, $price, $display, $instrument){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $order_data = [
                    'side' => $side,
                    'type' => $type,
                    'currency' => $currency,
                    'amount' => (string)$amount,
                    'price' => (string)$price,
                    'display' => (string)$display,
                    'instrument' => $instrument
                  ];

    $url = "$this->base_url/wallets/$wallet_id/orders";
    return $this->sendRequest('POST', $url, $order_data);
  }


  // API: Get All Orders in wallet
  // Remarks: Might need to create more filters ...
  public function getWalletOrders($wallet_id, $instrument=null, $status=null ){

    $qs = [ ]; // Querystring array
    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $url = "$this->api_address/wallets/$wallet_id/orders";

    if($status || $instrument) {

      if($status) $qs['status'] = $status;
      if($instrument) $qs['instrument'] = $instrument;
      $querystring = http_build_query($qs); // build queries here
      $url = "$url?$querystring";
    }

    return $this->sendRequest('GET', $url);
  }


  // API: Get specific order information
  public function getOrder($orderId ,$wallet_id){

    if(!$wallet) $wallet = $this->default_wallet;

    $url = "$this->base_url/wallets/$wallet_id/orders/$orderId";

    return $this->sendRequest('GET', $url);
  }


  // API: Cancel an Orders in wallet
  public function cancelOrder($orderId ,$wallet_id){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $url = "$this->base_url/wallets/$wallet_id/orders/$orderId";

    return $this->sendRequest("DELETE", $url);
  }


  // API: withdraw crypto currency
  public function cryptocurrencyWithdrawalRequest($currency, $amount, $address ,$wallet_id){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $post_body = [
        'currency'=>$currency,
        'amount'=>$amount,
        'address'=>$address
    ];

    $url = "$this->base_url/wallets/$wallet_id/cryptocurrency_withdrawals";

    return $this->sendRequest("POST", $url, $post_body);
  }

  // API: generate an address for user to send in cryptocurrency
  public function cryptocurrencyDepositRequest($currency, $wallet_id){

    if(!$wallet_id) return 'Wallet ID is missing, please enter the wallet id';

    $post_body = [
        'currency'=>$currency
    ];

    $url = "$this->base_url/wallets/$wallet_id/cryptocurrency_deposits";

    return $this->sendRequest("POST", $url, $post_body);
  }


  // API: generate an address for user to send in cryptocurrency
  public function createWalletTransfer($srcWallet, $destWallet, $amount, $currCode){

    $post_body = [
      "sourceWalletId"=>$srcWallet,
      "destinationWalletId"=>$destWallet,
      "amount"=>$amount,
      "currencyCode"=>$currCode
    ];

    $url = "$this->base_url/wallet_transfers";

    return $this->sendRequest("POST", $url, $post_body);
  }



}# end of class
