<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Checkout;

use Simon\SecurionPay\Gateway\Http\Client\AbstractTransaction;

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
