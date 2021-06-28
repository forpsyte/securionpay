<?php

namespace Simon\SecurionPay\Gateway\Request\Checkout\Authorize;

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
     * PaymentDataBuilder constructor.
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
        $chargeId = $payment->getAdditionalInformation(AdapterInterface::FIELD_CHARGE_ID);
        $payment->unsAdditionalInformation(AdapterInterface::FIELD_CHARGE_ID);
        return [
            AdapterInterface::FIELD_CHARGE_ID => $chargeId
        ];
    }
}
