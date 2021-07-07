<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

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

        if ($this->config->getFraudDetectionAction() == 'after_checkout') {
            if ($fraudDetails[Response::FRAUD_DETAIL_STATUS] == Response::FRAUD_STATUS_IN_PROGRESS) {
                $payment->setIsTransactionPending(true);
            }
        } else {
            while ($fraudDetails[Response::FRAUD_DETAIL_STATUS] == Response::FRAUD_STATUS_IN_PROGRESS) {
                sleep(5);
                $chargeResponse = $this->securionPayAdapterFactory->create()->getCharge([
                    AdapterInterface::FIELD_CHARGE_ID => $transaction[Response::ID]
                ])->getBody();
                $fraudDetails = $chargeResponse[Response::FRAUD_DETAILS];
            }
        }


        if (array_key_exists($fraudDetails[Response::FRAUD_DETAIL_STATUS], $this->config->getRiskFraudResult())) {
            $payment->setIsFraudDetected(true);
        }
    }
}
