<?php

namespace TronApi\Interfaces;

use TronApi\Address;
use TronApi\Block;
use TronApi\Transaction;

interface WalletInterface
{
    public function generateAddress(): array;

    public function validateAddress(string $address): bool;

    public function getAddressByPrivateKey(string $privateKeyHex): Address;

    public function balance(string $address);

    public function transfer(string $private_key, string $from, string $to, float $amount, $message = null): Transaction;

    public function getNowBlock(): Block;

    public function getBlockByNum(int $blockID): Block;

    public function getTransactionById(string $txHash);
}
