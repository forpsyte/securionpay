<?php

namespace Simon\SecurionPay\Block\Customer;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

class PaymentMethod extends Template
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * PaymentMethod constructor.
     * @param Template\Context $context
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get the add payment method url.
     *
     * @return string
     */
    public function getAddPaymentMethodUrl()
    {
        return $this->urlBuilder->getUrl('vault/cards/new');
    }
}

