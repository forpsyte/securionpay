<?php

declare(strict_types=1);

namespace Simon\SecurionPay\Model;

use Magento\Quote\Model\Quote;
use Simon\SecurionPay\Gateway\Http\Data\Request;

/**
 * Guest checkout request generator model.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestCheckoutRequestGenerator implements \Simon\SecurionPay\Api\GuestCheckoutRequestGeneratorInterface
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
     * @var \Simon\SecurionPay\Model\CheckoutRequestDetailsFactory
     */
    protected $checkoutRequestDetailsFactory;
    /**
     * @var Adapter\SecurionPayAdapterFactory
     */
    protected $securionPayAdapterFactory;
    /**
     * @var \Simon\SecurionPay\Helper\Currency
     */
    protected $currencyHelper;
    /**
     * @var \Simon\SecurionPay\Gateway\Config\Config
     */
    protected $config;


    /**
     * GuestCheckoutRequestGenerator constructor.
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Simon\SecurionPay\Model\CheckoutRequestDetailsFactory $checkoutRequestDetailsFactory
     * @param Adapter\SecurionPayAdapterFactory $securionPayAdapterFactory
     * @param \Simon\SecurionPay\Helper\Currency $currencyHelper
     * @param \Simon\SecurionPay\Gateway\Config\Config $config
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Simon\SecurionPay\Model\CheckoutRequestDetailsFactory $checkoutRequestDetailsFactory,
        \Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory $securionPayAdapterFactory,
        \Simon\SecurionPay\Helper\Currency $currencyHelper,
        \Simon\SecurionPay\Gateway\Config\Config $config
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->securionPayAdapterFactory = $securionPayAdapterFactory;
        $this->currencyHelper = $currencyHelper;
        $this->config = $config;
        $this->checkoutRequestDetailsFactory = $checkoutRequestDetailsFactory;
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutRequest($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getData('quote_id'));
        $response = $this->securionPayAdapterFactory->create()->getCheckout([
            Request::FIELD_AMOUNT => $this->currencyHelper->getMinorUnits($quote->getGrandTotal()),
            Request::FIELD_CURRENCY => $quote->getBaseCurrencyCode(),
            Request::FIELD_REQUIRE_ATTEMPT => $this->config->requireAttempt($quote->getStoreId())
        ])->getBody();
        /** @var CheckoutRequestDetails $checkoutRequestDetails */
        $checkoutRequestDetails = $this->checkoutRequestDetailsFactory->create();
        $checkoutRequestDetails->setSignature($response['signature']);
        return $checkoutRequestDetails;
    }
}
