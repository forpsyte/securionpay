<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Api;

/**
 * Interface for retrieving checkout request signature
 */
interface GuestCheckoutRequestGeneratorInterface
{
    /**
     * Get payment information
     *
     * @param string $cartId
     * @return \Simon\SecurionPay\Api\Data\CheckoutRequestDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCheckoutRequest($cartId);
}
