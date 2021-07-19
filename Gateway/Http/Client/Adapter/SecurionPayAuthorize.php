<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Adapter;

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
