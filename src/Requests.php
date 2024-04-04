<?php

declare(strict_types = 1);

namespace TronApi;

use CurlHandle;
use TronApi\Exceptions\TronErrorException;

final class Requests {
	private CurlHandle $curl;
	private array $curl_headers = [];

	public function __construct(public string $url, public array $options = []){
		$this->curl = curl_init();
		// 设置请求超时时间，单位是秒
		$request_timeout = 10;
		// 设置连接超时时间，单位是秒
		$connect_timeout = 5;
		if(isset($options['req_timeout'])){
			$request_timeout = $options['req_timeout'];
		}
		if(isset($options['connect_timeout'])){
			$connect_timeout = $options['connect_timeout'];
		}
		if(isset($options['api_key'])){
			$this->curl_headers = [
				'TRON-PRO-API-KEY' => $options['api_key'],
				'Content-Type' => 'application/json'
			];
		}
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $request_timeout);
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
	}

    /**
     * 发起CURL请求
     * @throws TronErrorException
     */
	public function request(string $method,string $path,array $data = array()) : mixed {
		switch($method){
			case 'POST':
				curl_setopt($this->curl,CURLOPT_URL,(filter_var($path,FILTER_VALIDATE_URL) ? $path : $this->url.'/'.$path));
				curl_setopt($this->curl,CURLOPT_POSTFIELDS,json_encode($data));
			break;
			case 'GET':
				curl_setopt($this->curl,CURLOPT_URL,(filter_var($path,FILTER_VALIDATE_URL) ? $path : $this->url.'/'.$path.'?'.http_build_query($data)));
				curl_setopt($this->curl,CURLOPT_CUSTOMREQUEST,$method);
			break;
			default:
				error_log('The request method is inappropriate for the URL !');
			break;
		}
		
		curl_setopt($this->curl,CURLOPT_HTTPHEADER, $this->curl_headers);
		$result = curl_exec($this->curl);
		$error = curl_error($this->curl);
		return is_bool($result) ? throw new TronErrorException($error) : json_decode($result, true);
	}
	public function __destruct(){
		curl_close($this->curl);
	}
	public function __clone() : void {
		$this->curl = curl_init();
		curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
	}
}