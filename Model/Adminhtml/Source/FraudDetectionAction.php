<?php

namespace Simon\SecurionPay\Model\Adminhtml\Source;

class FraudDetectionAction
{
    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'during_checkout',
                'label' => __('During Checkout'),
            ],
            [
                'value' => 'after_checkout',
                'label' => __('After Checkout'),
            ]
        ];
    }
}
