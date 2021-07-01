define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',

], function (ko, quote, resourceUrlManager, storage, errorProcessor, fullScreenLoader) {
    'use strict';

    return function () {
        return storage.get(
            resourceUrlManager.getUrlForCheckoutRequest(quote)
        ).fail(
            function (response) {
                errorProcessor.process(response);
                fullScreenLoader.stopLoader();
            }
        );
    }
});
