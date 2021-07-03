
/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'jquery',
        'securionpay',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Simon_SecurionPay/js/action/get-3d-secure-information'
    ],
    function (
        _,
        $,
        SecurionPay,
        Component,
        globalMessageList,
        quote,
        fullScreenLoader,
        VaultEnabler,
        GetThreeDSecureInfoAction,
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Simon_SecurionPay/payment/cc-form',
                active: false,
                code: 'securionpay',
                lastBillingAddress: null,
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                selectedCardType: null,
                creditCardToken: null,
                additionalData: {},
                securionPay: null,
                isThreeDSecureActive: null
            },

            /**
             * @returns {exports.initialize}
             */
            initialize: function() {
                this._super();
                this.securionPay = window.SecurionPay;
                this.isThreeDSecureActive = window.checkoutConfig.payment[this.getCode()].threeDSecureActive;
                this.vaultEnabler = new VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());
                return this;
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe([
                        'active',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'selectedCardType',
                        'creditCardToken',
                    ]);

                return this;
            },

            /**
             * Initialize event observers
             *
             * @returns void
             */
            initEventObservers: function() {
                // Format the credit card number field
                let ccNumberSelector = this.getSelector('cc_number');
                $(ccNumberSelector).on('keypress change', function () {
                    $(this).val(function (index, value) {
                        return value.replace(/[^0-9]/g, "").replace(/\W/gi, '').replace(/(.{4})/g, '$1 ');
                    });
                });
            },

            /**
             * Get code
             *
             * @returns {String}
             */
            getCode: function () {
                return this.code;
            },

            /**
             * Returns vault code.
             *
             * @returns {String}
             */
            getVaultCode: function () {
                return window.checkoutConfig.payment[this.getCode()].ccVaultCode;
            },

            /**
             * Get data
             *
             * @returns {Object}
             */
            getData: function() {
                let data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'gateway_token': this.creditCardToken(),
                        'cc_type': this.selectedCardType(),
                        'cc_number_enc': this.creditCardNumber().replace(/\s/g,'').replace(/\d(?=\d{4})/g, "X"),
                        'cvc': this.creditCardVerificationNumber(),
                        'store_card': this.vaultEnabler.isActivePaymentTokenEnabler() && this.isVaultEnabled(),
                        'email': window.checkoutConfig.customerData.email
                    },
                };

                let parentData = this._super();

                data['additional_data'] =  _.extend(parentData['additional_data'], data['additional_data']);
                this.vaultEnabler.visitAdditionalData(data);

                return data;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                let active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },

            /**
             * @returns {Boolean}
             */
            isVaultEnabled: function () {
                return this.vaultEnabler.isVaultEnabled();
            },

            /**
             * Returns state of place order button
             *
             * @returns {Boolean}
             */
            isButtonActive: function () {
                return this.isActive() && this.isPlaceOrderActionAllowed();
            },

            /**
             * Validate cc form
             *
             * @return {*}
             */
            validate: function() {
                let formSelector = this.getSelector('cc_form'),
                    form = $(formSelector);
                return form.validation() && form.validation('isValid');
            },

            /**
             * Place order.
             *
             * @returns void
             */
            beforePlaceOrder: function (data, event) {
                if(this.validate()) {
                    let formSelector = this.getSelector('cc_form'),
                        form = $(formSelector),
                        publicKey = window.checkoutConfig.payment[this.getCode()].publicKey;

                    if (event) {
                        event.preventDefault();
                    }
                    this.securionPay.setPublicKey(publicKey);
                    this.securionPay.createCardToken(form, this.createCardTokenCallback.bind(this));
                }
            },

            /**
             * @param token
             * @returns void
             */
            createCardTokenCallback: function(token) {
                let self = this;

                if (token.error) {
                    this.showError(token.error.message);
                    return;
                }

                if (!this.isThreeDSecureActive) {
                    this.creditCardToken(token.id);
                    this.placeOrder();
                } else {
                    GetThreeDSecureInfoAction(token).done(
                        function (response) {
                            self.securionPay.verifyThreeDSecure({
                                amount: response.amount,
                                currency: response.currency,
                                card: response.token
                            }, self.verifyThreeDSecureCallback.bind(self));
                        }
                    );
                }


            },

            /**
             * @param token
             * @returns void
             */
            verifyThreeDSecureCallback: function(token) {
                if (token.error) {
                    this.showError(token.error.message);
                } else {
                    this.creditCardToken(token.id);
                    this.placeOrder();
                }
            },

            /**
             * Get full selector name
             *
             * @param {String} field
             * @returns {String}
             * @private
             */
            getSelector: function (field) {
                return '#' + this.getCode() + '_' + field;
            },

            /**
             * Show error message
             *
             * @param {String} errorMessage
             * @private
             */
            showError: function (errorMessage) {
                globalMessageList.addErrorMessage({
                    message: errorMessage
                });
            },
        });

    }
);
