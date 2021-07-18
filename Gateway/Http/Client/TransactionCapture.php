<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client;

/**
 * Sends capture charge request
 */
class TransactionCapture extends AbstractTransaction
{
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        return $this->adapterFactory->create()->capture($data);
    }
}
