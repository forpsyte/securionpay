<?php

namespace Forpsyte\SecurionPay\Model\Adapter;

use Magento\Framework\ObjectManagerInterface;
use Forpsyte\SecurionPay\Gateway\Config\Config;

class SecurionPayAdapterFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SecurionPayAdapterFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Creates instance of Braintree Adapter.
     *
     * @return SecurionPayAdapter
     */
    public function create()
    {
        return $this->objectManager->create(SecurionPayAdapter::class);
    }
}
