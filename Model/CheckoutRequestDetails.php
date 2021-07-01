<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Model;

class CheckoutRequestDetails extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Simon\SecurionPay\Api\Data\CheckoutRequestDetailsInterface
{
    /**
     * @inheritDoc
     */
    public function setSignature($signature)
    {
        return $this->setData(self::SIGNATURE, $signature);
    }

    /**
     * @inheritDoc
     */
    public function getSignature()
    {
        return $this->getData(self::SIGNATURE);
    }
}
