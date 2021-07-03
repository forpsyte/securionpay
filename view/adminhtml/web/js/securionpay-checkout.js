
/*browser:true*/
/*global define*/
define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'mage/translate',
    'securionpay_checkout',
], function ($, Class, alert, domObserver, $t, SecurionPayCheckout) {
    'use strict';

    return Class.extend({
        defaults: {
            $selector: null,
            selector: 'edit_form',
            container: 'payment_form_securionpay_checkout',
            formSelector: 'securionpay_checkout_cc_form',
            active: false,
            code: 'securionpay_checkout',
            publicKey: null,
            securionPayCheckout: null,
            storeName: null,
            storeDescription: null,
            serviceUrl: null,
            decimals: null,
            chargeAmount: null,
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * @returns {exports.initialize}
         */
        initialize: function(){
            console.log(SecurionPayCheckout);
            this._super();
            this.securionPayCheckout = window.SecurionpayCheckout;
            this.securionPayCheckout.key = this.publicKey;
            this.securionPayCheckout.success = this.submitOrder.bind(this);
            this.securionPayCheckout.error = this.stopProcess.bind(this)
            this.securionPayCheckout.close = this.stopProcess.bind(this);
            return this;
        },

        /**
         * Set list of observable attributes
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            let self = this;
            self.$selector = $('#' + self.selector);
            this._super()
                .observe([
                    'active',
                ]);

            // re-init payment method events
            self.$selector.off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            // listen block changes
            domObserver.get('#' + self.container, function () {
                self.$selector.off('submit');
                self.initSecurionPayCheckout();
            });

            return this;
        },

        initSecurionPayCheckout: function () {
            let self = this;
            self.disableEventListeners();
            self.enableEventListeners();
        },

        /**
         * Triggered when payment changed
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive && !this.active()) {
                this.$selector.off('submitOrder.securionpay_checkout');

                return;
            }
            this.disableEventListeners();
            window.order.addExcludedPaymentMethod(this.code);

            if (!this.serviceUrl) {
                alert($.mage.__('This payment is not available'));
                return;
            }

            this.enableEventListeners();

        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            if (!this.active()) {
                return;
            }
            this.$selector.on('submitOrder.securionpay_checkout', this.openCheckout.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submit');
            let events = $._data(this.$selector[0], "events");
            if (events.submitOrder && events.submitOrder[0].namespace === "securionpay") {
                return;
            }
            this.$selector.off('submitOrder');
        },

        /**
         * Enable/disable current payment method
         * @param {Object} event
         * @param {String} method
         * @returns {exports.changePaymentMethod}
         */
        changePaymentMethod: function (event, method) {
            this.active(method === this.code);
            return this;
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
         * Trigger order submit
         */
        submitOrder: function (result) {
            this.setPaymentDetails(result);
            this.$selector.validate().form();
            this.$selector.trigger('afterValidate.beforeSubmit');
            this.placeOrder();
        },

        /**
         * Open SecurionPay checkout form
         */
        openCheckout: function() {
            $.ajax({
                url: this.serviceUrl,
                method: 'GET',
                datatype: 'json',
                success: function (response) {
                    this.securionPayCheckout.open({
                        checkoutRequest: response.signature,
                        name: this.storeName,
                        description: this.storeDescription
                    });
                }.bind(this),
                error: function (xhr, status, error) {
                    this.stopProcess();
                }.bind(this)
            });
        },

        setPaymentDetails: function(result) {
            let $container = $('#' + this.container);
            $container.find('[name="payment[chargeId]"]').val(result.charge.id);
            $container.find('[name="payment[customerId]"]').val(result.customer.id);
        },

        /**
         * Place order
         */
        placeOrder: function () {
            let body = $('body');
            body.trigger('processStart');
            $('#' + this.selector).trigger('realOrder');
            body.trigger('processStop');
        },

        /**
         * Place order
         */
        stopProcess: function (result) {
            let body = $('body');
            body.trigger('processStop');
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
    });
});
