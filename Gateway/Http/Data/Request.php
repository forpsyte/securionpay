<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Data;

class Request
{
    const FIELD_AMOUNT = 'amount';
    const FIELD_CURRENCY = 'currency';
    const FIELD_CUSTOMER_ID = 'customerId';
    const FIELD_CARD = 'card';
    const FIELD_CARDS = 'cards';
    const FIELD_CARD_ID = 'cardId';
    const FIELD_DEFAULT_CARD_ID = 'defaultCardId';
    const FIELD_SHIPPING = 'shipping';
    const FIELD_BILLING = 'billing';
    const FIELD_NAME = 'name';
    const FIELD_ADDRESS = 'address';
    const FIELD_LINE_1 = 'line1';
    const FIELD_LINE_2 = 'line2';
    const FIELD_ZIP = 'zip';
    const FIELD_CITY = 'city';
    const FIELD_STATE = 'state';
    const FIELD_COUNTRY = 'country';
    const FIELD_VAT = 'vat';
    const FIELD_CHARGE_ID = 'chargeId';
    const FIELD_EVENT_ID = 'eventId';
    const FIELD_ID = 'id';
    const FIELD_CREATED = 'created';
    const FIELD_CAPTURED = 'captured';
    const FIELD_REFUNDED = 'refunded';
    const FIELD_DISPUTED = 'disputed';
    const FIELD_3D_SECURE = 'threeDSecure';
    const FIELD_REQUIRE_ATTEMPT = 'requireAttempt';
    const FIELD_REQUIRE_ENROLLED_CARD = 'requireEnrolledCard';
    const FIELD_REQUIRE_LIABILITY_SHIFT = 'requireSuccessfulLiabilityShiftForEnrolledCard';
}
