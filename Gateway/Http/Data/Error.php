<?php

namespace Simon\SecurionPay\Gateway\Http\Data;

class Error
{
    const TYPE_INVALID_REQUEST = 'invalid_request';
    const TYPE_CARD_ERROR = 'card_error';
    const TYPE_GATEWAY_ERROR = 'gateway_error';
    const TYPE_RATE_LIMIT_ERROR = 'rate_limit_error';
    const CODE_INVALID_NUMBER = 'invalid_number';
    const CODE_INVALID_EXPIRY_MONTH = 'invalid_expiry_month';
    const CODE_INVALID_EXPIRY_YEAR = 'invalid_expiry_year';
    const CODE_INVALID_CVC = 'invalid_cvc';
    const CODE_INCORRECT_CVC = 'incorrect_cvc';
    const CODE_INCORRECT_ZIP = 'incorrect_zip';
    const CODE_EXPIRED_CARD = 'expired_card';
    const CODE_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const CODE_LOST_OR_STOLEN = 'lost_or_stolen';
    const CODE_SUSPECTED_FRAUD = 'suspected_fraud';
    const CODE_LIMIT_EXCEEDED = 'limit_exceeded';
    const CODE_CARD_DECLINED = 'card_declined';
    const CODE_PROCESSING_ERROR = 'processing_error';
    const CODE_BLACKLISTED = 'blacklisted';
    const CODE_EXPIRED_TOKEN = 'expired_token';
    const CODE_AUTHENTICATION_REQUIRED = 'authentication_required';
}
