var config = {
    map: {
        '*': {
            'payment-method': 'Simon_SecurionPay/js/view/customer/payment-method',
            'save-payment-method': 'Simon_SecurionPay/js/view/customer/save-payment-method'
        }
    },
    paths: {
        'securionpay': 'https://securionpay.com/js/securionpay',
        'securionpay_checkout': 'https://securionpay.com/checkout'
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/resource-url-manager': {
                'Simon_SecurionPay/js/model/resource-url-manager': true
            }
        }
    }
}
