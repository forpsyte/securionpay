<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\Exception\SecurionPayException;
use Simon\SecurionPay\Gateway\Http\Data\Response;

interface AdapterInterface
{
    /**
     * Send request to the Securion Pay API
     *
     * @param array $data
     * @return Response
     * @throws SecurionPayException
     */
    public function send($data);
}
