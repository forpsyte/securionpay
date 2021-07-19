<?php

namespace Forpsyte\SecurionPay\Api\Data;

/**
 * Interface CheckoutRequestDetailsInterface
 */
interface CheckoutRequestDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SIGNATURE = 'signature';

    /**
     * Set the checkout request signature.
     *
     * @param string $signature
     * @return $this
     */
    public function setSignature($signature);

    /**
     * Get the check request signature.
     *
     * @return string
     */
    public function getSignature();
}
