<?php

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
