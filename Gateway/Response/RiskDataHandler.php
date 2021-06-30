<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;

/**
 * Risk data response handler.
 *
 * Raises flag for fraud if address verification failed.
 */
class RiskDataHandler extends AbstractHandler
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * RiskDataHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(SubjectReader $subjectReader, Config $config)
    {
        parent::__construct($subjectReader);
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var Response $transaction */
        $transaction = $this->subjectReader->readTransaction($response);

        if (!isset($transaction[Response::FRAUD_DETAILS])) {
            return;
        }

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $fraudDetails = $transaction[Response::FRAUD_DETAILS];

        if (array_key_exists($fraudDetails[Response::FRAUD_DETAIL_STATUS], $this->config->getRiskFraudResult())) {
            $payment->setIsFraudDetected(true);
        }
    }
}
