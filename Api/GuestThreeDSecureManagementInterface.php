<?php

namespace Simon\SecurionPay\Api;

interface GuestThreeDSecureManagementInterface
{
    /**
     * @param string $cartId
     * @param \Simon\SecurionPay\Api\Data\TokenInformationInterface $tokenInformation
     * @return \Simon\SecurionPay\Api\Data\ThreeDSecureInformationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThreeDSecureParams(
        $cartId,
        \Simon\SecurionPay\Api\Data\TokenInformationInterface $tokenInformation
    );
}
