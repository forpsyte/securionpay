<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Checkout;

use Forpsyte\SecurionPay\Gateway\Http\Client\AbstractTransaction;

class TransactionGetCharge extends AbstractTransaction
{
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        return $this->adapterFactory->create()->getCharge($data);
    }
}
