<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Response;


use Magento\Sales\Model\Order\Payment;

/**
 * Short description...
 *
 * Long description
 * Broken down into several lines
 *
 * License notice...
 */
class SaleDetailsHandler extends TransactionIdHandler
{
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
        return false;
    }
}
