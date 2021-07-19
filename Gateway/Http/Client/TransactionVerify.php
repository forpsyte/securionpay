<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client;

use Exception;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;

class TransactionVerify extends AbstractTransaction
{
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        return $this->adapterFactory->create()->getCharge($data);
    }
}
