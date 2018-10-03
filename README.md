# itBit API Wrapper for PHP

This is the api wrapper written in PHP for itbit exchange api. The purpose of this wrapper is to make things easier for api user when using the API endpoint and also serve as the example on how to interact with itBit Api. Example code is also included on how to use the Wrapper class.

## Installation Guide

First you will need to have signup an account with itbit and request the API credential from api@itbit.com, itbit personnel will get back to you as soon as possible.
All you need is to copy the itbitApi.php into your project folder. Include the itbitApi.php into your code, instantiate it and start using it. Here is the list of methods provided by the wrapper:

#### Public API
- getTickers($tickerSymbol)
- getOrderBooks($tickerSymbol)

#### Private API
- createWallet($walletName)
- getWallets() - get all wallets
- getWallet($walletId) - get specific wallet by the id given
- getBalances($walletId, $currCode) -- default currency code it USD, other option are SGD and EUR
- getWalletTrades($walletId)
- createOrder($side, $type, $currency, $amount, $price, $instrument, $wallet)
- createOrderWithDisplay($side, $type, $currency, $amount, $price, $display, $instrument, $wallet)
- getWalletOrders($wallet_id, $instrument=null, $status=null)
- getOrder($orderId, $wallet_id)
- cancelOrder($orderId ,$wallet_id)
- cryptocurrencyWithdrawalRequest($currency, $amount, $address ,$wallet_id)
- cryptocurrencyDepositRequest($currency, $wallet_id)
- createWalletTransfer($srcWallet, $destWallet, $amount, $currCode)

## Code Sample
+ refer to example.php
