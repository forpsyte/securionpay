<?php

namespace Simon\SecurionPay\Model\Ui\Adminhtml\Checkout;

use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\ConfigProviderInterface;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Gateway\Config\Checkout\Config;
use Simon\SecurionPay\Gateway\Config\Config as ScpConfig;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Helper\Currency as CurrencyHelper;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'securionpay_checkout';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Quote
     */
    protected $quote;
    /**
     * @var CurrencyHelper
     */
    protected $currencyHelper;
    /**
     * @var SecurionPayAdapterFactory
     */
    protected $adapterFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ScpConfig
     */
    protected $scpConfig;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param ScpConfig $scpConfig
     * @param Quote $quote
     * @param CurrencyHelper $currencyHelper
     * @param SecurionPayAdapterFactory $adapterFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ScpConfig $scpConfig,
        Quote $quote,
        CurrencyHelper $currencyHelper,
        SecurionPayAdapterFactory $adapterFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->scpConfig = $scpConfig;
        $this->quote = $quote;
        $this->currencyHelper = $currencyHelper;
        $this->adapterFactory = $adapterFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $storeId = $this->quote->getStoreId();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'storeName' => $this->config->getStoreName($storeId),
                    'storeDescription' => $this->config->getStoreDescription($storeId),
                    'publicKey' => $this->scpConfig->getPublicKey($storeId),
                    'signature' => $this->getSignature()
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    private function getSignature()
    {
        try {
            $storeId = $this->quote->getStoreId();
            $amount = $this->currencyHelper->getMinorUnits($this->quote->getQuote()->getGrandTotal());
            $currency = $this->quote->getQuote()->getQuoteCurrencyCode();
            $requireThreeDSecure = $this->scpConfig->requireAttempt($storeId);
            $response = $this->adapterFactory->create()->getCheckout([
                AdapterInterface::FIELD_CURRENCY => $currency,
                AdapterInterface::FIELD_AMOUNT => $amount,
                AdapterInterface::FIELD_REQUIRE_ATTEMPT => $requireThreeDSecure
            ]);
            $body = $response->getBody();
            return $body['signature'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return null;
        }

    }
}
