define([
    'jquery',
    'loader',
    'securionpay',
    'mage/translate'
],function ($) {
    'use strict';

    $.widget('mage.savePaymentMethod',{
        /**
         * Bind event handlers for adding payment method.
         * @private
         */
        _create: function () {
            let options         = this.options,
                savePaymentMethod      = options.savePaymentMethod;

            if (savePaymentMethod) {
                $(document).on('click', savePaymentMethod, this._savePaymentMethods.bind(this));
            }

            $('.page-main').loader({
                icon: this.options.icon
            })
        },

        /**
         * Save new payment method.
         * @private
         */
        _savePaymentMethods: function () {
            let form = $('#form-validate');
            if (!this.validate()) {
                return;
            }
            $(".page-main").loader("show");
            window.SecurionPay.setPublicKey(this.options.publicKey);
            window.SecurionPay.createCardToken(form, this._createCardTokenCallback.bind(this))
        },

        /**
         * Call back for
         * @param token
         * @private
         */
        _createCardTokenCallback: function (token) {
            let ccType = $(this._getSelector('cc_type')).val(),
                ccExpYear = $(this._getSelector('cc_exp_year')).val(),
                ccExpMonth = $(this._getSelector('cc_exp_month')).val(),
                ccNumber = $(this._getSelector('cc_number')).val().replace(/\s/g,'').replace(/\d(?=\d{4})/g, "X"),
                payload = {
                    gateway_token: token.id,
                    cc_type: ccType,
                    cc_exp_year: ccExpYear,
                    cc_exp_month: ccExpMonth,
                    cc_number: ccNumber
                };

            $.ajax({
                url: this.options.savePaymentMethodLocation,
                method: 'GET',
                dataType: 'json',
                data: payload,
                success: function(response) {
                    window.location = this.options.redirectUrl;
                }.bind(this),
                error: function (xhr, status, error) {
                    console.log(xhr);
                }
            });
        },

        /**
         * Get full selector name
         *
         * @param {String} field
         * @returns {String}
         * @private
         */
        _getSelector: function (field) {
            return '#' + this.getCode() + '_' + field;
        },

        /**
         * Get the payment method code
         *
         * @return {string}
         */
        getCode: function () {
            return 'securionpay';
        },

        validate: function () {
            let form = $('#form-validate');
            return form.validation() && form.validation('isValid');
        }


    });
    return $.mage.savePaymentMethod;
})
