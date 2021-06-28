<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

/**
 * Authorize charge on payment provider system
 */
class SecurionPayAuthorize extends AbstractClient
{
    /**
     * @inheritDoc
     */
    public function willCapture()
    {
        return false;
    }
}
