<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Block;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Model\Ui\ConfigProvider;

class Payment extends Template
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var ConfigProviderInterface
     */
    protected $configProvider;
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Payment constructor.
     * @param Template\Context $context
     * @param Config $config
     * @param ConfigProviderInterface $configProvider
     * @param Serializer $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        ConfigProviderInterface $configProvider,
        Serializer $serializer,
        array $data = []
    ) {
        $this->config = $config;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|false|string
     */
    public function getPaymentConfig()
    {
        $payment = $this->configProvider->getConfig()['payment'];
        $config = $payment[$this->getCode()];
        $config['code'] = $this->getCode();
        $config['publicKey'] = $this->config->getPublicKey();
        return $this->serializer->serialize($config);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return ConfigProvider::CODE;
    }
}
