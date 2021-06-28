<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Http\Client;


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
