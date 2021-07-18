<?php

namespace Forpsyte\SecurionPay\Gateway\Request;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\SubjectReader;
use Forpsyte\SecurionPay\Observer\DataAssignObserver;

class VaultDataBuilder implements BuilderInterface
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

        $willStoreCard = $payment->getAdditionalInformation(DataAssignObserver::STORE_CARD);

        if ($willStoreCard) {
            return [
                DataAssignObserver::STORE_CARD => $payment->getAdditionalInformation(DataAssignObserver::STORE_CARD),
                CustomerInterface::EMAIL => $payment->getAdditionalInformation(CustomerInterface::EMAIL),
            ];
        }

        return [];
    }
}
