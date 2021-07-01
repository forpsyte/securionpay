var config = {
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
