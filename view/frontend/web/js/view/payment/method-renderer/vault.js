/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault'
], function ($, VaultComponent) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            template: 'Magento_Vault/payment/form'
        },

        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.maskedCC;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType: function () {
            return this.details.type;
        },

        /**
         * @returns {String}
         */
        getToken: function() {
            return this.token;
        },

        /**
         * @returns {String}
         */
        getPublicHash: function() {
            return this.publicHash;
        },

        /**
         * Get data
         *
         * @returns {Object}
         */
        getData: function() {
            return {
                'method': this.getCode(),
                'additional_data': {
                    'cc_type': this.getCardType(),
                    'cc_number': this.getMaskedCard(),
                    'cc_exp_month': this.getExpirationDate().split("/").shift(),
                    'cc_exp_year': this.getExpirationDate().split("/").pop(),
                    'public_hash': this.getPublicHash()
                },
            };
        },
    });
});
