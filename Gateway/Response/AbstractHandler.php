<?php

namespace Forpsyte\SecurionPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Gateway\SubjectReader;

/**
 * Parent response handler
 *
 * Updates the payment information with response data
 * from the payment provider.
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        Request::FIELD_ID,
        Request::FIELD_AMOUNT,
        Request::FIELD_CURRENCY,
        Request::FIELD_CREATED,
        Request::FIELD_CAPTURED,
        Request::FIELD_REFUNDED,
        Request::FIELD_DISPUTED
    ];
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * AuthDetailsHandler constructor.
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param Response $transaction
     * @param Payment $payment
     * @throws LocalizedException
     */
    public function updatePaymentInformation(Response $transaction, Payment $payment)
    {
        foreach ($this->additionalInformationMapping as $field) {
            if (!isset($transaction[$field])) {
                continue;
            }
            $payment->setAdditionalInformation($field, $transaction[$field]);
        }
    }
}
