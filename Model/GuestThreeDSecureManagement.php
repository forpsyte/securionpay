<?php

namespace Simon\SecurionPay\Model;

use Magento\Quote\Model\Quote;
use Simon\SecurionPay\Api\Data\ThreeDSecureInformationInterface;

class GuestThreeDSecureManagement implements \Simon\SecurionPay\Api\GuestThreeDSecureManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * @var \Simon\SecurionPay\Helper\Currency
     */
    protected $currencyHelper;
    /**
     * @var \Simon\SecurionPay\Model\ThreeDSecureInformationFactory
     */
    protected $threeDSecureInformationFactory;

    /**
     * GuestCheckoutRequestGenerator constructor.
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Simon\SecurionPay\Helper\Currency $currencyHelper
     * @param ThreeDSecureInformationFactory $threeDSecureInformationFactory
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Simon\SecurionPay\Helper\Currency $currencyHelper,
        \Simon\SecurionPay\Model\ThreeDSecureInformationFactory $threeDSecureInformationFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->currencyHelper = $currencyHelper;
        $this->threeDSecureInformationFactory = $threeDSecureInformationFactory;
    }

    /**
     * @inheritDoc
     */
    public function getThreeDSecureParams(
        $cartId,
        \Simon\SecurionPay\Api\Data\TokenInformationInterface $tokenInformation
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getData('quote_id'));
        /** @var  ThreeDSecureInformationInterface $threeDSecureInfo */
        $threeDSecureInfo = $this->threeDSecureInformationFactory->create();
        $threeDSecureInfo->setAmount($this->currencyHelper->getMinorUnits($quote->getGrandTotal()));
        $threeDSecureInfo->setCurrency($quote->getBaseCurrencyCode());
        $threeDSecureInfo->setToken($tokenInformation->getId());
        return $threeDSecureInfo;
    }
}
