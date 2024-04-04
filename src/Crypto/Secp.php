<?php

declare(strict_types = 1);

namespace TronApi\Crypto;

use Elliptic\EC;

abstract class Secp {
	static public function sign(string $message,string $privatekey) : string {
		$secp = new EC('secp256k1');
		$sign = $secp->sign($message,$privatekey);
		$recoveryparam = bin2hex(chr($sign->recoveryParam));
		$r = $sign->r->toString(16);
		$s = $sign->s->toString(16);
		return $r.$s.$recoveryparam;
	}
}