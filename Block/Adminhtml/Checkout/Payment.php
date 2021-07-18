<?php

namespace Forpsyte\SecurionPay\Block\Adminhtml\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Element\Template;
use Forpsyte\SecurionPay\Model\Ui\Adminhtml\Checkout\ConfigProvider;

class Payment extends Template
{
    /**
     * @var ConfigProviderInterface
     */
    protected $configProvider;

    /**
     * Payment constructor.
     * @param Template\Context $context
     * @param ConfigProviderInterface $configProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ConfigProviderInterface $configProvider,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getPaymentConfig()
    {
        $payment = $this->configProvider->getConfig()['payment'];
        $config = $payment[$this->getCode()];
        $config['code'] = $this->getCode();
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return ConfigProvider::CODE;
    }
}
