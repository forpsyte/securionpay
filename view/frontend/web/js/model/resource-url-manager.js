define([],function(){
    'use strict';

    return function(resourceUrlManager) {
        resourceUrlManager.getUrlForCheckoutRequest = function(quote) {
            let params = this.getCheckoutMethod() == 'guest' ? //eslint-disable-line eqeqeq
                {
                    cartId: quote.getQuoteId()
                } : {},
                urls = {
                    'guest': '/guest-carts/:cartId/checkout-request',
                    'customer': '/carts/mine/checkout-request'
                };

            return this.getUrl(urls, params);
        }

        resourceUrlManager.getUrlForThreeDSecureInformation = function(quote) {
            let params = this.getCheckoutMethod() == 'guest' ? //eslint-disable-line eqeqeq
                {
                    cartId: quote.getQuoteId()
                } : {},
                urls = {
                    'guest': '/guest-carts/:cartId/3d-secure-information',
                    'customer': '/carts/mine/3d-secure-information'
                };

            return this.getUrl(urls, params);
        }

        return resourceUrlManager;
    }
});
