<?php

namespace Forpsyte\SecurionPay\Api;

interface GuestThreeDSecureManagementInterface
{
    /**
     * @param string $cartId
     * @param \Forpsyte\SecurionPay\Api\Data\TokenInformationInterface $tokenInformation
     * @return \Forpsyte\SecurionPay\Api\Data\ThreeDSecureInformationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThreeDSecureParams(
        $cartId,
        \Forpsyte\SecurionPay\Api\Data\TokenInformationInterface $tokenInformation
    );
}
