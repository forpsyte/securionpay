define([
    'jquery',
    'uiComponent'
], function ($, Class) {
    'use strict';

    return Class.extend({
        defaults: {
            $selector: null,
            selector: 'edit_form',
            $body: null,
            $ccNumberSelector: null,
            $ccTypeSelector: null,
            $ccExpMonthSelector: null,
            $ccExpYearSelector: null
        },

        /**
         * Set list of observable attributes
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            var self = this;
            self.$body = $('body');
            self.$ccNumberSelector = $(this.getSelector('cc_number'));
            self.$ccTypeSelector = $(this.getSelector('cc_type'));
            self.$ccExpMonthSelector = $(this.getSelector('cc_exp_month'));
            self.$ccExpYearSelector = $(this.getSelector('cc_exp_year'));
            self.$selector = $('#' + self.selector);
            self.$selector.on(
                'setVaultNotActive.' + self.getCode(),
                function () {
                    self.$selector.off('submitOrder.' + self.getCode());
                }
            );
            this._super();

            this.initEventHandlers();

            return this;
        },

        /**
         * Get payment code
         * @returns {String}
         */
        getCode: function () {
            return this.code;
        },

        /**
         * Init event handlers
         */
        initEventHandlers: function () {
            $('#' + this.container).find('[name="payment[token_switcher]"]')
                .on('click', this.selectPaymentMethod.bind(this));
        },

        /**
         * Store payment details
         */
        setPaymentDetails: function () {
            this.$selector.find('[name="payment[public_hash]"]').val(this.publicHash);
            this.$ccNumberSelector.val(this.ccNumber);
            this.$ccTypeSelector.val(this.ccType);
            this.$ccExpMonthSelector.val(this.ccExpMonth);
            this.$ccExpYearSelector.val(this.ccExpYear);
        },

        /**
         * Select current payment token
         */
        selectPaymentMethod: function () {
            this.disableEventListeners();
            this.enableEventListeners();
            this.setPaymentDetails();
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            this.$selector.on('submitOrder.' + this.getCode(), this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submitOrder');
        },

        /**
         * Pre submit for order
         * @returns {Boolean}
         */
        submitOrder: function () {
            this.$selector.validate().form();
            this.$selector.trigger('afterValidate.beforeSubmit');
            this.$body.trigger('processStop');

            // validate parent form
            if (this.$selector.validate().errorList.length) {
                return false;
            }
            this.placeOrder();
        },

        /**
         * Place order
         */
        placeOrder: function () {
            this.$body.trigger('processStart');
            this.$selector.trigger('realOrder');
            this.$body.trigger('processStop');
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

        getSelector: function (field) {
            return '#' + this.code + '_' + field;
        },
    });
});
