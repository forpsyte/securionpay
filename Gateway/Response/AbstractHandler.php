<?php

namespace Simon\SecurionPay\Gateway\Response;


use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;

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
        AdapterInterface::FIELD_ID,
        AdapterInterface::FIELD_AMOUNT,
        AdapterInterface::FIELD_CURRENCY,
        AdapterInterface::FIELD_CREATED,
        AdapterInterface::FIELD_CAPTURED,
        AdapterInterface::FIELD_REFUNDED,
        AdapterInterface::FIELD_DISPUTED
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
