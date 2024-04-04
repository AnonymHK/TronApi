<?php

declare(strict_types = 1);

namespace TronApi;

use TronApi\Exceptions\TronErrorException;

class Api
{
    protected Requests $sender;

    public function __construct(string $apiurl, array $options = [])
    {        
        $this->sender = new Requests($apiurl, $options);
    }
    
    /**
     * Abstracts some common functionality like formatting the post data
     * along with error handling.
     *
     * @throws TronErrorException
     */
    public function post(string $endpoint, array $data = [])
    {
        $res_data = $this->sender->request('POST', $endpoint, $data);
        $this->checkForErrorResponse($res_data);
        return $res_data;
    }

    /**
     * Abstracts some common functionality like
     * along with error handling.
     *
     * @throws TronErrorException
     */
    public function get(string $endpoint, array $data = [])
    {
        $res_data = $this->sender->request('GET', $endpoint, $data);        
        $this->checkForErrorResponse($res_data);
        return $res_data;
    }

    /**
     * Check if the response has an error and throw it.
     *
     * @param $data
     * @throws TronErrorException
     */
    private function checkForErrorResponse($data)
    {
        if (isset($data['Error'])) {
            throw new TronErrorException($data['Error']);
        }
    }
}
