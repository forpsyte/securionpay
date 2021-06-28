/*browser:true*/
/*global define*/
define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'mage/translate',
    'securionpay'
], function($, Class, alert, domObserver, $t){
    'use strict';

    return Class.extend({
        defaults: {
            $selector: null,
            selector: 'edit_form',
            container: 'payment_form_securionpay',
            active: false,
            securionPay: null,
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * Set list of observable attributes
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            let self = this;
            this.$selector = $('#' + this.selector);
            this._super()
                .observe([
                    'active'
                ]);

            // re-init payment method events
            this.$selector.off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            // listen block changes
            domObserver.get('#' + self.container, function () {
                self.$selector.off('submit');
                self.initSecurionPay();
            });

            return this;
        },

        /**
         * Initializer
         */
        initSecurionPay: function () {
            this.securionPay = window.SecurionPay;
            this.disableEventListeners();
            this.enableEventListeners();
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
         * Triggered when payment changed
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive && !this.active()) {
                this.$selector.off('submitOrder.securionpay');
                return;
            }
            this.disableEventListeners();
            window.order.addExcludedPaymentMethod(this.code);
            if (!this.publicKey) {
                this.showError($.mage.__('This payment is not available'));

                return;
            }
            this.enableEventListeners();
        },

        /**
         * Show alert message
         * @param {String} message
         */
        showError: function (message) {
            alert({
                content: message
            });
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            if (!this.active()) {
                return;
            }
            this.$selector.on('submitOrder.securionpay', this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submit');
            let events = $._data(this.$selector[0], "events");
            if (events.submitOrder && events.submitOrder[0].namespace === "securionpay_checkout") {
                return;
            }
            this.$selector.off('submitOrder');
        },

        /**
         * Store payment details
         * @param {String} token
         * @param {String} ccNumber
         */
        setPaymentDetails: function (token, ccNumber) {
            let $container = $('#' + this.container),
                maskedCcNumber = 'XXXXXXXXXXXX' + ccNumber.substr(-4);
            $container.find('[name="payment[gateway_token]"]').val(token);
            $container.find('[name="payment[cc_number_enc]"]').val(maskedCcNumber);
        },

        /**
         * Trigger order submit
         */
        submitOrder: function () {
            let self = this,
                success = false;

            this.$selector.validate().form();
            this.$selector.trigger('afterValidate.beforeSubmit');

            // validate parent form
            if (this.$selector.validate().errorList.length) {
                $('body').trigger('processStop');
                return false;
            }

            this.securionPay.setPublicKey(this.publicKey);
            this.securionPay.createCardToken(this.$selector, this.createCardTokenCallback.bind(this));
        },

        /**
         * Sets payment details and places the order
         * @param token
         */
        createCardTokenCallback: function(token) {
            if (token.error) {
                this.showError(token.error.message);
                return;
            }
            let fields = this.getFields(),
                ccNumber = $(fields.number.selector).val();
            this.setPaymentDetails(token.id, ccNumber);
            $('#' + this.container).find('[type="submit"]').trigger('click');
        },

        /**
         * Place order
         */
        placeOrder: function () {
            $('#' + this.selector).trigger('realOrder');
        },

        /**
         * Get jQuery selector
         * @param {String} field
         * @returns {String}
         */
        getSelector: function (field) {
            return '#' + this.code + '_' + field;
        },

        /**
         * Get hosted fields configuration
         * @returns {Object}
         */
        getFields: function () {
            return {
                number: {
                    selector: this.getSelector('cc_number')
                },
                expirationMonth: {
                    selector: this.getSelector('cc_exp_month'),
                    placeholder: $t('MM')
                },
                expirationYear: {
                    selector: this.getSelector('cc_exp_year'),
                    placeholder: $t('YY')
                }
            };
        },
    });
});
