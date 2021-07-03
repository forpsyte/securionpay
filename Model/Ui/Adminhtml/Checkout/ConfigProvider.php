<?php

namespace Simon\SecurionPay\Model\Ui\Adminhtml\Checkout;

use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Gateway\Config\Checkout\Config;
use Simon\SecurionPay\Gateway\Config\Config as ScpConfig;
use Simon\SecurionPay\Helper\Currency as CurrencyHelper;

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
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var ScpConfig
     */
    protected $scpConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param ScpConfig $scpConfig
     * @param Quote $quote
     * @param CurrencyHelper $currencyHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ScpConfig $scpConfig,
        Quote $quote,
        CurrencyHelper $currencyHelper,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->scpConfig = $scpConfig;
        $this->quote = $quote;
        $this->currencyHelper = $currencyHelper;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getConfig()
    {
        $storeId = $this->quote->getStoreId();
        $currency = $this->quote->getQuote()->getQuoteCurrencyCode();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'storeName' => $this->config->getStoreName($storeId),
                    'storeDescription' => $this->config->getStoreDescription($storeId),
                    'publicKey' => $this->scpConfig->getPublicKey($storeId),
                    'decimals' => $this->currencyHelper->getDecimals($currency),
                    'serviceUrl' => $this->getServiceUrl()
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    private function getServiceUrl()
    {
        return $this->urlBuilder->getUrl(
            \Simon\SecurionPay\Model\Ui\ConfigProvider::CODE .
            '/order_create/loadCheckoutRequest'
        );
    }
}
