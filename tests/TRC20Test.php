<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TronApi\TRC20;

const URI = 'https://api.shasta.trongrid.io'; // shasta testnet
const ADDRESS = 'TGytofNKuSReFmFxsgnNx19em3BAVBTpVB';
const PRIVATE_KEY = '0xf1b4b7d86a3eff98f1bace9cb2665d0cad3a3f949bc74a7ffb2aaa968c07f521';
const BLOCK_ID = 13402554;
const TX_HASH = '539e6c2429f19a8626fadc1211985728e310f5bd5d2749c88db2e3f22a8fdf69';
const OPTIONS = [
    'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t', // USDT TRC20
    'decimals' => 6,
];

$trc20Wallet = new TRC20(apiurl: URI, options: OPTIONS);

//获取Token余额
$balanceData = $trc20Wallet->balance($address);
var_dump($balanceData);

//转账
$amount = 1;
$from = $trc20Wallet->getAddressByPrivateKey($privateKey)->address;
$transferData = $trc20Wallet->transferTRC20(self::PRIVATE_KEY, $from, self::ADDRESS, $amount);
var_dump($transferData);
