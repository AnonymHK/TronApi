<?php

namespace TronApi;

use TronApi\Exceptions\TransactionException;
use TronApi\Exceptions\TronErrorException;
use TronApi\Support\Formatter;
use TronApi\Support\Utils;

class TRC20 extends TRX
{
    protected $contractAddress;

    public function __construct(string $apiurl, array $options)
    {
        parent::__construct($apiurl, $options);

        $this->contractAddress = $options['contract_address'];
        $this->decimals = isset($options['decimals']) ? $options['decimals'] : 1e6;
    }

    /**
     * 获取Token余额
     */
    public function balance(string $address, bool $sun = false)
    {
        $format = Formatter::toAddressFormat($address);
        $body = $this->api->post('/wallet/triggersmartcontract', [
            'contract_address' => $this->contractAddress,
            'function_selector' => 'balanceOf(address)',
            'parameter' => $format,
            'owner_address' => $address,
        ]);

        if (isset($body->result->code)) {
            throw new TronErrorException(hex2bin($body->result->message));
        }

        $balance = Utils::formatBalance(hexdec($body->constant_result[0]), $this->decimals);
        return $balance;
    }

    /**
     * 发送TRC20交易
     */
    public function transferTRC20(string $private_key, string $from, string $to, float $amount, $extradata = null, float $fee_limit = 150000000): Transaction
    {
        $toFormat = Formatter::toAddressFormat($to);
        $trans_amount = $this->toTronValue($amount, $this->decimals);
        //$abi_encoded = ABI::EncodeParameters_External(['address', 'uint256'], [$toFormat, $trans_amount]);
        $numberFormat = Formatter::toIntegerFormat($trans_amount);

        $body = $this->api->post('/wallet/triggersmartcontract', [
            'contract_address' => $this->contractAddress,
            'function_selector' => 'transfer(address,uint256)',
            'parameter' => "{$toFormat}{$numberFormat}",
            'fee_limit' => $fee_limit,
            'owner_address' => $from,
        ]);

        if (isset($body['result']['code'])) {
            throw new TransactionException(hex2bin($body['result']['message']));
        }

        $tradeobj = $this->signTransaction($private_key, $body['transaction']);
        if(!is_null($extradata)) $tradeobj['raw_data']->data = bin2hex($extradata);
        $response = $this->sendRawTransaction($tradeobj);

        if (isset($response['result']) && $response['result'] == true) {
            return new Transaction(
                $body['transaction']['txID'],
                $body['transaction']['raw_data'],
                'PACKING'
            );
        } else {
            throw new TransactionException('Transfer Fail');
        }

        /**
         * 输出格式
        */
        // array(2) {
        //     ["result"]=>
        //     array(1) {
        //       ["result"]=>
        //       bool(true)
        //     }
        //     ["transaction"]=>
        //     array(4) {
        //       ["visible"]=>
        //       bool(false)
        //       ["txID"]=>
        //       string(64) "da36d6703ca7a2e80f541239604060e1e3ac1067918119e29016e3a6669ded7d"
        //       ["raw_data"]=>
        //       array(6) {
        //         ["contract"]=>
        //         array(1) {
        //           [0]=>
        //           array(2) {
        //             ["parameter"]=>
        //             array(2) {
        //               ["value"]=>
        //               array(3) {
        //                 ["data"]=>
        //                 string(136) "a9059cbb00000000000000000000004181675c7bfef3ed7628328c9e17bf466dcd4c0d7f00000000000000000000000000000000000000000000000000000000000f4240"
        //                 ["owner_address"]=>
        //                 string(42) "4141649143892622978c04834fe90e1d5a2b9d3bfa"
        //                 ["contract_address"]=>
        //                 string(42) "41a614f803b6fd780986a42c78ec9c7f77e6ded13c"
        //               }
        //               ["type_url"]=>
        //               string(49) "type.googleapis.com/protocol.TriggerSmartContract"
        //             }
        //             ["type"]=>
        //             string(20) "TriggerSmartContract"
        //           }
        //         }
        //         ["ref_block_bytes"]=>
        //         string(4) "b130"
        //         ["ref_block_hash"]=>
        //         string(16) "a6f4f6f62098062e"
        //         ["expiration"]=>
        //         int(1674849414000)
        //         ["fee_limit"]=>
        //         int(100000000)
        //         ["timestamp"]=>
        //         int(1674849356035)
        //       }
        //       ["raw_data_hex"]=>
        //       string(422) "0a02b1302208a6f4f6f62098062e40f0d6b7a6df305aae01081f12a9010a31747970652e676f6f676c65617069732e636f6d2f70726f746f636f6c2e54726967676572536d617274436f6e747261637412740a154141649143892622978c04834fe90e1d5a2b9d3bfa121541a614f803b6fd780986a42c78ec9c7f77e6ded13c2244a9059cbb00000000000000000000004181675c7bfef3ed7628328c9e17bf466dcd4c0d7f00000000000000000000000000000000000000000000000000000000000f4240708392b4a6df30900180c2d72f"
        //     }
        //   }
    }

    /**
     * 获取地址交易记录
     */
    public function getTransactionsByTrc20(string $address, int $mintimestamp = null, int $maxtimestamp = null, bool $confirmed = null, bool $to = false,bool $from = false, $limit = 20)
    {
        $data = [
            'contract_address' => $this->contractAddress,
            'only_to' => $to,
            'only_from' => $from,
            'limit' => max(min($limit,200),20)
        ];
        if($mintimestamp != null){
            $data['min_timestamp'] = date('Y-m-d\TH:i:s.v\Z',$mintimestamp);
        }
        if($maxtimestamp != null){
            $data['max_timestamp'] = date('Y-m-d\TH:i:s.v\Z',$maxtimestamp);
        }
        if(!is_null($confirmed)){
			$data[$confirmed ? 'only_confirmed' : 'only_unconfirmed'] = true;
		}

        $url_param = http_build_query($data);

        $url = "v1/accounts/{$address}/transactions/trc20?{$url_param}";
        $body = $this->api->get($url);
        
        if (isset($body['data']) && $body['success']) {
            return $body['data'];
        }
        throw new TransactionException('Transaction Fail');
    }

    /**
     * 获取钱包的支出记录
    */
	public function getTransactionsFromAddress(string $address,int $limit = 20) : object {
		return $this->getTransactionsByTrc20(address : $address,limit : $limit, from : true);
	}

    /**
     * 获取钱包地址的收入记录
     */
	public function getTransactionsToAddress(string $address,int $limit = 20) : object {
		return $this->getTransactionsByTrc20(address : $address,limit : $limit, to : true);
	}

    /**
     * Sign the transaction, the api has the risk of leaking the private key,
     * please make sure to call the api in a secure environment
     *
     * @param $transaction
     * @param string|null $message
     * @return array
     * @throws TronErrorException
     */
    private function signTransaction($private_key, $transaction, string $message = null): array
    {
        if(!is_array($transaction)) {
            throw new TronErrorException('Invalid transaction provided');
        }

        if(isset($transaction['Error']))
            throw new TronErrorException($transaction['Error']);


        if(isset($transaction['signature'])) {
            throw new TronErrorException('Transaction is already signed');
        }

        if(!is_null($message)) {
            $transaction['raw_data']['data'] = bin2hex($message);
        }
        $signature = Support\Secp::sign($transaction['txID'], $private_key);
        $transaction['signature'] = [$signature];

        return $transaction;
    }
}
