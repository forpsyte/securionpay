<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client;

/**
 * Sends refund charge request
 */
class TransactionRefund extends AbstractTransaction
{
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        return $this->adapterFactory->create()->refund($data);
    }
}
