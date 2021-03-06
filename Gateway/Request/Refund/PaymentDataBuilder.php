<?php

namespace Forpsyte\SecurionPay\Gateway\Request\Refund;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\SubjectReader;
use Forpsyte\SecurionPay\Helper\Currency;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Currency
     */
    protected $currencyHelper;

    /**
     * AddressDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param Currency $currencyHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        Currency $currencyHelper
    ) {
        $this->subjectReader = $subjectReader;
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        return [
            Request::FIELD_CHARGE_ID => $payment->getAdditionalInformation(Request::FIELD_ID),
            Request::FIELD_AMOUNT => $this->currencyHelper->getMinorUnits(
                $this->subjectReader->readAmount($buildSubject)
            )
        ];
    }
}
