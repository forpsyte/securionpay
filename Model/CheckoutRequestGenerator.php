<?php

declare(strict_types=1);

namespace Simon\SecurionPay\Model;

use Magento\Quote\Model\Quote;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;

/**
 * Checkout request generator model.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CheckoutRequestGenerator implements \Simon\SecurionPay\Api\CheckoutRequestGeneratorInterface
{
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
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Simon\SecurionPay\Model\CheckoutRequestDetailsFactory $checkoutRequestDetailsFactory
     * @param Adapter\SecurionPayAdapterFactory $securionPayAdapterFactory
     * @param \Simon\SecurionPay\Helper\Currency $currencyHelper
     * @param \Simon\SecurionPay\Gateway\Config\Config $config
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Simon\SecurionPay\Model\CheckoutRequestDetailsFactory $checkoutRequestDetailsFactory,
        \Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory $securionPayAdapterFactory,
        \Simon\SecurionPay\Helper\Currency $currencyHelper,
        \Simon\SecurionPay\Gateway\Config\Config $config
    ) {
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
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $response = $this->securionPayAdapterFactory->create()->getCheckout([
            AdapterInterface::FIELD_AMOUNT => $this->currencyHelper->getMinorUnits($quote->getGrandTotal()),
            AdapterInterface::FIELD_CURRENCY => $quote->getBaseCurrencyCode(),
            AdapterInterface::FIELD_REQUIRE_ATTEMPT => $this->config->requireAttempt($quote->getStoreId())
        ])->getBody();
        /** @var CheckoutRequestDetails $checkoutRequestDetails */
        $checkoutRequestDetails = $this->checkoutRequestDetailsFactory->create();
        $checkoutRequestDetails->setSignature($response['signature']);
        return $checkoutRequestDetails;
    }
}
