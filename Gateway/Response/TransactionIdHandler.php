<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Data\Response;

class TransactionIdHandler extends AbstractHandler
{

    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        if (!($paymentDO->getPayment() instanceof Payment)) {
            return;
        }

        /** @var Response $transaction */
        $transaction = $this->subjectReader->readTransaction($response);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $this->setTransactionId(
            $payment,
            $transaction
        );

        $payment->setIsTransactionClosed($this->shouldCloseTransaction());
        $closed = $this->shouldCloseParentTransaction($payment);
        $payment->setShouldCloseParentTransaction($closed);
        $this->updatePaymentInformation($transaction, $payment);
    }

    /**
     * @param Payment $payment
     * @param Response $transaction
     * @return void
     */
    protected function setTransactionId(Payment $payment, Response $transaction)
    {
        $payment->setTransactionId($transaction[Response::ID]);
    }

    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return false;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment)
    {
        return false;
    }
}
