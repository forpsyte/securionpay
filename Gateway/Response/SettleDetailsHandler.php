<?php

namespace Forpsyte\SecurionPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;

/**
 * Settlement/Capture details handler.
 *
 * Updates the payment information with payment settle response data
 * from the payment provider.
 */
class SettleDetailsHandler extends AbstractHandler
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
        $lastCcTransId = $payment->getCcTransId();
        $payment->setCcTransId($transaction[Response::ID]);
        $payment->setLastTransId($lastCcTransId);
        $this->updatePaymentInformation($transaction, $payment);
    }
}
