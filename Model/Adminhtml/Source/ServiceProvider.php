<?php

namespace Forpsyte\SecurionPay\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * 3D Secure service provider options
 */
class ServiceProvider implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'cardinal', 'label' => __('CardinalCommerce')],
            ['value' => 'securionpay', 'label' => __('SecurionPay')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'securionpay' => __('SecurionPay'),
            'cardinal' => __('CardinalCommerce')
        ];
    }
}
