define([
  'jquery',
  'mage/translate'
], function ($) {
    'use strict';

    $.widget('mage.paymentMethod',{
        /**
         * Bind event handlers for adding payment method.
         * @private
         */
        _create: function () {
            let options         = this.options,
                addPaymentMethod      = options.addPaymentMethod;

            if (addPaymentMethod) {
                $(document).on('click', addPaymentMethod, this._addPaymentMethods.bind(this));
            }
        },

        /**
         * Add a new payment method.
         * @private
         */
        _addPaymentMethods: function () {
            window.location = this.options.addPaymentMethodLocation;
        }
    });
    return $.mage.paymentMethod;
});
