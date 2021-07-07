<?php

namespace Simon\SecurionPay\Model\Adminhtml\Source;

class FraudDetectionAction
{
    const OPTION_DURING_CHECKOUT = 'during_checkout';
    const OPTION_AFTER_CHECKOUT = 'after_checkout';
    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::OPTION_DURING_CHECKOUT,
                'label' => __('During Checkout'),
            ],
            [
                'value' => self::OPTION_AFTER_CHECKOUT,
                'label' => __('After Checkout'),
            ]
        ];
    }
}
