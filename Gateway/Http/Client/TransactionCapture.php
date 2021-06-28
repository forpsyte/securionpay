<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Http\Client;


use Simon\SecurionPay\Gateway\Http\Data\Response;

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
