<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Block\Adminhtml\Form;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config;

class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * Cc constructor.
     * @param Context $context
     * @param Config $paymentConfig
     * @param Quote $quote
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        Quote $quote,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->quote = $quote;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->quote->getQuote();
    }
}
