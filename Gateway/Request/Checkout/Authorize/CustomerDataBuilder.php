<?php

namespace Forpsyte\SecurionPay\Gateway\Request\Checkout\Authorize;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;

class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * PaymentDataBuilder constructor.
     *
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
        $customerId = $payment->getAdditionalInformation(Request::FIELD_CUSTOMER_ID);
        $payment->unsAdditionalInformation(Request::FIELD_CUSTOMER_ID);
        return [
            Request::FIELD_CUSTOMER_ID => $customerId
        ];
    }
}
