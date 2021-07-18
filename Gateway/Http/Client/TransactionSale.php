<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client;

/**
 * Sends sale charge request
 */
class TransactionSale extends AbstractTransaction
{
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        return $this->adapterFactory->create()->sale($data);
    }
}
