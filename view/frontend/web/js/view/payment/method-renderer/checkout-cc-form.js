
/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'jquery',
        'securionpay_checkout',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Ui/js/model/messageList',
        'Forpsyte_SecurionPay/js/action/get-checkout-request'
    ],
    function (
        _,
        $,
        SecurionPayCheckout,
        Component,
        globalMessageList,
        getCheckoutRequestAction,
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Forpsyte_SecurionPay/payment/checkout-cc-form',
                active: false,
                code: 'securionpay_checkout',
                lastBillingAddress: null,
                chargeId: null,
                customerId: null,
                serviceUrl: null,
                storeName: null,
                storeDescription: null,
                additionalData: {},
                securionPayCheckout: null
            },

            /**
             * @returns {exports.initialize}
             */
            initialize: function() {
                this._super();
                this.securionPayCheckout = window.SecurionpayCheckout;
                this.serviceUrl = window.checkoutConfig.payment[this.getCode()].serviceUrl;
                this.storeName = window.checkoutConfig.payment[this.getCode()].storeName;
                this.storeDescription = window.checkoutConfig.payment[this.getCode()].storeDescription;
                return this;
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe([
                        'active',
                        'chargeId',
                        'customerId',
                    ]);

                return this;
            },

            /**
             * Initialize event observers
             *
             * @returns void
             */
            initEventObservers: function() {
                this.securionPayCheckout.key = window.checkoutConfig.payment[this.getCode()].publicKey;
                this.securionPayCheckout.error = this.showError.bind(this);
                this.securionPayCheckout.success = this.beforePlaceOrder.bind(this);
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
             * Get data
             *
             * @returns {Object}
             */
            getData: function() {
                let data = {
                    'method': this.getCode(),
                    'additional_data': {
                        'chargeId': this.chargeId(),
                        'customerId': this.customerId(),
                    },
                };

                let parentData = this._super();

                data['additional_data'] =  _.extend(parentData['additional_data'], data['additional_data']);

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
            beforePlaceOrder: function (result) {
                this.setPaymentDetails(result);
                if(this.validate()) {
                    this.placeOrder();
                } else {
                    this.showError('We could not process you order at this time. Pleas try again.');
                }
            },

            setPaymentDetails: function(result) {
                this.chargeId(result.charge.id);
                this.customerId(result.customer.id);
            },

            /**
             * Display SecurionPay checkout form.
             *
             * @returns void
             */
            openCheckout: function(data, event) {
                let self = this;

                if (event) {
                    event.preventDefault();
                }

                getCheckoutRequestAction().done(
                    function (response) {
                        self.securionPayCheckout.open({
                            checkoutRequest: response.signature,
                            name: self.storeName,
                            description: self.storeDescription
                        });
                    }
                );
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
