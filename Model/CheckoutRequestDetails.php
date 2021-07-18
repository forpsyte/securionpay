<?php

namespace Forpsyte\SecurionPay\Model;

class CheckoutRequestDetails extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Forpsyte\SecurionPay\Api\Data\CheckoutRequestDetailsInterface
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
