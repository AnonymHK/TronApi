<?php

namespace TronApi;

use TronApi\Support\Utils;

class Address
{
    public $privateKey,
        $address;

    const ADDRESS_SIZE = 34;
    const ADDRESS_PREFIX = "41";
    const ADDRESS_PREFIX_BYTE = 0x41;

    public function __construct(string $address = '', string $privateKey = '')
    {
        if (strlen($address) === 0) {
            throw new \InvalidArgumentException('Address can not be empty');
        }

        $this->privateKey = $privateKey;
        $this->address = $address;        
    }

    /**
     * Dont rely on this. Always use Wallet::validateAddress to double check
     * against tronGrid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (strlen($this->address) !== Address::ADDRESS_SIZE) {
            return false;
        }

        return Utils::verifyAddress($this->address);
    }
}
