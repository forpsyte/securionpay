<?php

namespace Forpsyte\SecurionPay\Api;

/**
 * Interface for retrieving checkout request signature
 */
interface CheckoutRequestGeneratorInterface
{
    /**
     * Get payment information
     *
     * @param int $cartId
     * @return \Forpsyte\SecurionPay\Api\Data\CheckoutRequestDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCheckoutRequest($cartId);
}
