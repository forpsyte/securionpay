<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Block;


use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Renders payment information details in Order block
 * in Admin panel or customer account on storefront.
 */
class Info extends ConfigurableInfo
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $config, $data);
        $this->config = $config;

        if (isset($data['pathPattern'])) {
            $this->config->setPathPattern($data['pathPattern']);
        }

        if (isset($data['methodCode'])) {
            $this->config->setMethodCode($data['methodCode']);
        }
    }

    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getValueView($field, $value)
    {
        if ($field == 'cc_type') {
            $value = __($this->getMappedCardType($value));
        }
        return $value;
    }

    /**
     * @param $ccType
     * @return string
     */
    protected function getMappedCardType($ccType)
    {
        $ccTypesMapper = $this->config->getCcTypesMapper();
        return array_search($ccType, $ccTypesMapper) ?: $ccType;
    }

}
