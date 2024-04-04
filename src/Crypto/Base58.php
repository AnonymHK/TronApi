<?php

declare(strict_types = 1);

namespace TronApi\Crypto;

use InvalidArgumentException;

use Exception;

class Base58 {
	static public function dec2base(string | int $dec,int $base,string $digits = null) : string {
		if(extension_loaded('gmp')):
			if($base < 2 or $base > 256):
				throw new InvalidArgumentException('Invalid base argument , The base must be between 2 and 256');
			endif;
			$value = strval(null);
			if(is_null($digits)) $digits = self::digits($base);
			while($dec > $base - 1):
				$rest = gmp_mod($dec,$base);
				$dec = gmp_div($dec,$base);
				$value = $digits[intval($rest)].$value;
			endwhile;
			$value = $digits[intval($dec)].$value;
			return $value;
		else:
			throw new Exception('gmp extension is needed !');
		endif;
	}
	static public function bc2bin(string | int $dec) : string {
		return self::dec2base($dec,256);
	}
	static public function encode(string | int $dec,int $length = 58) : string {
		return self::dec2base($dec,$length,'123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
	}
	static public function encodeAddress(string $string,int $prefix = 0,bool $compressed = false) : string {
		$string = hex2bin($string);
		if($prefix) $string = chr($prefix).$string;
		if($compressed) $string = $string.chr($compressed);
		$hash = hash('sha256',$string,true);
		$hash = hash('sha256',$hash,true);
		$checksum = $string.substr($hash,0,4);
		return self::encode(self::bin2bc($checksum));
	}
	static public function base2dec(string $value,int $base,$digits = false) : string | int {
		if(extension_loaded('gmp')):
			if($base < 2 or $base > 256):
				throw new InvalidArgumentException('Invalid base argument , The base must be between 2 and 256');
			endif;
			if($base < 37) $value = strtolower($value);
			if($digits === false) $digits = self::digits($base);
			$size = strlen($value);
			$dec = strval(0);
			for($loop = 0;$loop < $size;$loop++):
				$element = strpos($digits,$value[$loop]);
				$power = gmp_pow($base,$size - $loop - 1);
				$dec = gmp_add($dec,gmp_mul($element,$power));
			endfor;
			return ($dec <= PHP_INT_MAX and $dec >= PHP_INT_MIN) ? gmp_intval($dec) : gmp_strval($dec);
		else:
			throw new Exception('gmp extension is needed !');
		endif;
	}
	static public function bin2bc(string $value) : string | int {
		return self::base2dec($value,256);
	}
	static public function decode(string $value,int $length = 58) : string | int {
		return self::base2dec($value,$length,'123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
	}
	static public function decodeAddress(string $string,int $prefix = 0,bool $compressed = false) : string {
		$string = self::bc2bin(self::decode($string));
		$string = substr($string,0,-4);
		if($prefix) $string = substr($string,$prefix);
		if($compressed) $string = substr($string,0,-$compressed);
		return bin2hex($string);
	}
	static public function digits(int $base) : string {
		if($base > 64):
			$digits = implode(array_map(chr(...),range(0,255)));
		else:
			$digits = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
		endif;
		$digits = substr($digits,0,$base);
		return $digits;
	}
}