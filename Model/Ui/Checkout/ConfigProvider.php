<?php

namespace Forpsyte\SecurionPay\Model\Ui\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Gateway\Config\Checkout\Config;
use Forpsyte\SecurionPay\Gateway\Config\Config as ScpConfig;

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
     * @var ScpConfig
     */
    protected $scpConfig;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param ScpConfig $scpConfig
     * @param SessionManagerInterface $sessionManager
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ScpConfig $scpConfig,
        SessionManagerInterface $sessionManager,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->scpConfig = $scpConfig;
        $this->sessionManager = $sessionManager;
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
                    'publicKey' => $this->scpConfig->getPublicKey($storeId),
                    'serviceUrl' => $this->getServiceUrl(),
                    'requireThreeDSecure' => $this->config->isThreeDSecureEnabled()
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    private function getServiceUrl()
    {
        return $this->urlBuilder->getBaseUrl() .
            \Forpsyte\SecurionPay\Model\Ui\ConfigProvider::CODE .
            '/checkout/signature';
    }
}
