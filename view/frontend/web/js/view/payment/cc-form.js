/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        let config = window.checkoutConfig.payment,
            securionPayGatewayType = 'securionpay';

        if (config[securionPayGatewayType].isActive) {
            rendererList.push(
                {
                    type: securionPayGatewayType,
                    component: 'Forpsyte_SecurionPay/js/view/payment/method-renderer/cc-form'
                }
            );
        }

        return Component.extend({});
    }
);
