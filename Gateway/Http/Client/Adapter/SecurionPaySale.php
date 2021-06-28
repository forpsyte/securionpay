<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

/**
 * Authorize and capture charge on payment provider system
 */
class SecurionPaySale extends AbstractClient
{
    /**
     * @inheritDoc
     */
    public function willCapture()
    {
        return true;
    }
}
