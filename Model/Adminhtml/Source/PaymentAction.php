<?php

namespace Forpsyte\SecurionPay\Model\Adminhtml\Source;

use Magento\Payment\Model\MethodInterface;

class PaymentAction
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
                'value' => MethodInterface::ACTION_AUTHORIZE,
                'label' => __('Authorize'),
            ],
            [
                'value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture'),
            ]
        ];
    }
}
