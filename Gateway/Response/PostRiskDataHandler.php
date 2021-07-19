<?php

namespace Forpsyte\SecurionPay\Gateway\Response;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Gateway\SubjectReader;
use Forpsyte\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;
use Forpsyte\SecurionPay\Model\Adminhtml\Source\FraudDetectionAction;

/**
 * Risk data response handler.
 *
 * Raises flag for fraud if address verification failed.
 */
class PostRiskDataHandler extends AbstractHandler
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var SecurionPayAdapterFactory
     */
    protected $securionPayAdapterFactory;

    /**
     * RiskDataHandler constructor.
     * @param SubjectReader $subjectReader
     * @param Config $config
     * @param SecurionPayAdapterFactory $securionPayAdapterFactory
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config $config,
        SecurionPayAdapterFactory $securionPayAdapterFactory
    ) {
        parent::__construct($subjectReader);
        $this->config = $config;
        $this->securionPayAdapterFactory = $securionPayAdapterFactory;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        if ($this->config->getFraudDetectionAction() !== FraudDetectionAction::OPTION_AFTER_CHECKOUT) {
            return;
        }

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

        if ($fraudDetails[Response::FRAUD_DETAIL_STATUS] == Response::FRAUD_STATUS_IN_PROGRESS) {
            $payment->setIsTransactionPending(true);
        }

        if ($fraudDetails[Response::FRAUD_DETAIL_STATUS] == Response::FRAUD_STATUS_FRAUDULENT) {
            $payment->setIsFraudDetected(true);
        }
    }
}
