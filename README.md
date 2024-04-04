## 概述
波场API 接口封装, 目前支持波场的 TRX 和 TRC20 中常用生成地址，发起转账，离线签名等功能.
库基于官方Tron API实现, 除密码学ECC库外无第三方依赖引用.

## 特点

1. 一套写法兼容 TRON 网络中 TRX 货币和 TRC 系列所有通证
1. 接口方法可可灵活增减

Tips:  php版本最低支持8.0且需要安装GMP扩展!

ApiKey需自行申请

Mainnet：		https://api.trongrid.io
Shasta Testnet:	https://api.shasta.trongrid.io
Nile Testnet:	https://nile.trongrid.io

## 支持方法

- 生成地址 `generateAddress()`
- 生成带助记词的钱包地址 `generateAddressWithMnemonic()`
- 从私钥获取助记词 `getPhraseFromPrivateKey()`
- 从助记词获取私钥 `getPrivateKeyFromPhrase()`
- 从公钥获取钱包地址 `getAddressHexFromPublicKey()`
- 使用Net方式验证钱包地址 `validateAddress()`
- 获取账户信息 `accountInfo(string $address)`
- 使用TXID获取交易信息 `getTransactionInfoById(string $txid)`
- 私钥获取钱包地址 `getAddressByPrivateKey(string $privateKeyHex)`
- 查询TRX余额 `balance(string $address)`
- 查询Token余额 `$trc20->balance(string $address)`
- 交易转账(离线签名、带转账备注) `transfer(string $private_key, string $from, string $to, float $amount, string $message = null)`
- TRC20交易转账(离线签名、带转账备注) `transferTRC20(string $private_key, string $from, string $to, float $amount, string $message = null, float $fee_limit = 150000000)`
- 查询最新区块 `getNowBlock()`
- 使用blockID获取区块信息 `getBlockByNum(int $blockID)`
- 根据交易哈希查询信息 `getTransactionById(string $txHash)`
- 查询钱包地址交易记录 `getTransactionsByAddress(string $address,int $limit = 20)`
- 快捷方法:  获取钱包支出记录 `getTransactionsFromAddress(string $address)` 获取钱包收入记录 `getTransactionsToAddress(string $address)`
- 查询TRC20地址交易记录 `getTransactionsByTrc20(string $address, int $mintimestamp = null, int $maxtimestamp = null, bool $confirmed = null, bool $to = false,bool $from = false, $limit = 20)`

## 快速开始

### 安装

```bash
> composer require anonymhk/tronApi
```

### 接口调用

``` php

$uri = 'https://api.shasta.trongrid.io';// shasta testnet
$trxWallet = new TRX(apiurl: $uri);

$addressData = $trxWallet->generateAddress();
var_dump($addressData);


$option = [
    'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',// USDT TRC20
    'decimals' => 6,
];
$trc20Wallet = new TRC20(apiurl: $uri, option: $option);
$addressData = $trc20Wallet->balance(ADDRESS);
var_dump($addressData);

```


