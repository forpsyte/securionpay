<?php

namespace Simon\SecurionPay\Model\Ui\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Gateway\Config\Checkout\Config;
use Simon\SecurionPay\Gateway\Config\Config as ScpConfig;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\SecurionPayCheckout;
use Simon\SecurionPay\Helper\Currency as CurrencyHelper;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'securionpay_checkout';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var SecurionPayCheckout
     */
    protected $securionPayCheckout;
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
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param ScpConfig $scpConfig
     * @param SessionManagerInterface $sessionManager
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param SecurionPayCheckout $securionPayCheckout
     * @param CurrencyHelper $currencyHelper
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ScpConfig $scpConfig,
        SessionManagerInterface $sessionManager,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        SecurionPayCheckout $securionPayCheckout,
        CurrencyHelper $currencyHelper,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->scpConfig = $scpConfig;
        $this->sessionManager = $sessionManager;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->securionPayCheckout = $securionPayCheckout;
        $this->currencyHelper = $currencyHelper;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $storeId = $this->sessionManager->getStoreId();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'storeName' => $this->config->getStoreName($storeId),
                    'storeDescription' => $this->config->getStoreDescription($storeId),
                    'signature' => $this->getSignature(),
                    'publicKey' => $this->scpConfig->getPublicKey($storeId),
                    'serviceUrl' => $this->getServiceUrl()
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
            $storeId = $this->sessionManager->getStoreId();
            $total = $this->checkoutSession->getQuote()->getBaseGrandTotal();
            $response =  $this->securionPayCheckout->send([
                AdapterInterface::FIELD_CURRENCY => $this->storeManager->getStore($storeId)->getCurrentCurrencyCode(),
                AdapterInterface::FIELD_AMOUNT => $this->currencyHelper->getMinorUnits($total),
                AdapterInterface::FIELD_REQUIRE_ATTEMPT => $this->config->isThreeDSecureEnabled($storeId)
            ]);
            $body = $response->getBody();
            return $body['signature'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return '';
        }
    }

    /**
     * @return string
     */
    private function getServiceUrl()
    {
        return $this->urlBuilder->getBaseUrl() . \Simon\SecurionPay\Model\Ui\ConfigProvider::CODE . '/checkout/signature';
    }
}
