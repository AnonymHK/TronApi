#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use TronApi\TRX;

const URI = 'https://api.shasta.trongrid.io'; // shasta testnet
const ADDRESS = 'TGytofNKuSReFmFxsgnNx19em3BAVBTpVB';
const PRIVATE_KEY = '0xf1b4b7d86a3eff98f1bace9cb2665d0cad3a3f949bc74a7ffb2aaa968c07f521';
const BLOCK_ID = 13402554;
const TX_HASH = '539e6c2429f19a8626fadc1211985728e310f5bd5d2749c88db2e3f22a8fdf69';


$trxWallet = new TRX(apiurl: URI);

//测试生成地址
// $addressData = $trxWallet->generateAddress();
// var_dump($addressData);

//测试生成带助记词的地址
// $addressData = $trxWallet->generateAddressWithMnemonic();
// var_dump($addressData);

// //私钥生成地址
// $privateKey = PRIVATE_KEY;
// $addressData = $trxWallet->getAddressByPrivateKey($privateKey);
// var_dump($addressData);

// //获取钱包余额
// $balanceData = $trxWallet->balance(ADDRESS);
// var_dump($balanceData);

// //转账
// $amount = 0.1;
// $from = $trxWallet->getAddressByPrivateKey(PRIVATE_KEY);
// $transferData = $trxWallet->transfer(PRIVATE_KEY, $from->address, ADDRESS, $amount);
// var_dump($transferData);

// //获取当前最新区块
// $blockData = $trxWallet->getNowBlock();
// var_dump($blockData);

// //获取指定区块高度信息
// $blockData = $trxWallet->getBlockByNum(BLOCK_ID);
// var_dump($blockData);

// //获取指定Hash交易信息
$txData = $trxWallet->getTransactionById(TX_HASH);
var_dump($txData);