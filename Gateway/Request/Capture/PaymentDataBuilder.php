<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Request\Capture;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * AddressDataBuilder constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        return [
            AdapterInterface::FIELD_CHARGE_ID => $payment->getAdditionalInformation(AdapterInterface::FIELD_ID)
        ];
    }
}
