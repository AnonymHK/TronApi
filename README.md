## 概述
波场API 接口封装, 目前支持波场的 TRX 和 TRC20 中常用生成地址，发起转账，离线签名等功能.
库基于官方Tron API实现, 除密码学ECC库外无第三方依赖引用.

## 特点

1. 一套写法兼容 TRON 网络中 TRX 货币和 TRC 系列所有通证
1. 接口方法可可灵活增减

Tips:  php版本最低支持8.0且需要安装GMP扩展!

## 支持方法

- 生成地址 `generateAddress()`
- 验证地址 `validateAddress(Address $address)`
- 根据私钥得到地址 `privateKeyToAddress(string $privateKeyHex)`
- 查询余额 `balance(Address $address)`
- 交易转账(离线签名) `transfer(Address $from, Address $to, float $amount)`
- 查询最新区块 `blockNumber()`
- 根据区块链查询信息 `blockByNumber(int $blockID)`
- 根据交易哈希查询信息 `transactionReceipt(string $txHash)`

## 快速开始

### 安装

```bash
> composer require anonymhk/tronApi
```

### 接口调用

``` php

$uri = 'https://api.shasta.trongrid.io';// shasta testnet
$api = new \TronApi\Api(new Client(['base_uri' => $uri]));

$trxWallet = new \TronApi\TRX($api);
$addressData = $trxWallet->generateAddress();
// $addressData->privateKey
// $addressData->address

$config = [
    'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',// USDT TRC20
    'decimals' => 6,
];
$trc20Wallet = new \TronApi\TRC20($api, $this->config);
$addressData = $trc20Wallet->generateAddress();
```

## 计划

- 测试用例
- ...

