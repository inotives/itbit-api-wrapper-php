<?php
include('itbitApi.php');

$api_endpoint_beta = "https://beta-api.itbit.com/v1";
$api_endpoint_prod = "https://api.itbit.com/v1";
$api_key = "api-key";
$api_secret = "api-secret";
$user_id = "your-user-id";



$client = new itbitApi($api_endpoint_beta, $api_key, $api_secret, $user_id);


// Public Api ----------------------------------------------------------------------------------------
echo "Show Tickers: \n";
print_r( $client->getTickers('XBTUSD') );
echo "\n ------------ \n";


// Private Api ----------------------------------------------------------------------------------------

// Get All Wallets
foreach( $client->getAllWallets() as $wallet_info){
  // get the first funded wallet
  if($wallet_info->balances[0]->availableBalance > 0.00){
    $funded_wallet = $wallet_info;
  }
}

// Show the funded wallet details
echo "Show Wallets Details\n";
print_r($funded_wallet);
echo "\n ------------ \n";


echo "-- Create New Buy Order -- \n";
echo ">> Order Details: \n";
$newOrder = $client->createOrder($funded_wallet->id, 'buy', 'limit', 'XBT', '0.01', '120.12', 'XBTUSD');
print_r($newOrder);
