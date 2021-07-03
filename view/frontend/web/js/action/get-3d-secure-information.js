define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',

], function (ko, quote, resourceUrlManager, storage, errorProcessor, fullScreenLoader) {
    'use strict';

    return function (token) {
        let payload = {
            tokenInformation: {
                'id': token.id,
                'created': token.created,
                'fingerprint': token.fingerprint,
                'cc_type': token.brand
            }
        }
        return storage.post(
            resourceUrlManager.getUrlForThreeDSecureInformation(quote),
            JSON.stringify(payload)
        ).fail(
            function (response) {
                errorProcessor.process(response);
                fullScreenLoader.stopLoader();
            }
        );
    }
});
