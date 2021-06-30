<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Data\Response;

/**
 * Void payment response handler
 *
 * Closes current and respective parent transaction.
 */
class VoidHandler extends TransactionIdHandler
{

    /**
     * @param Payment $payment
     * @param Response $transaction
     * @return void
     */
    protected function setTransactionId(Payment $payment, Response $transaction)
    {
        return;
    }

    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return true;
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
        return true;
    }
}
