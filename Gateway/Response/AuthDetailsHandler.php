<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Data\Response;

class AuthDetailsHandler extends AbstractHandler
{

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $payment->setCcTransId($transaction[Response::ID]);
        $payment->setLastTransId($transaction[Response::ID]);
        $this->updatePaymentInformation($transaction, $payment);
    }
}
