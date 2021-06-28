<?php

namespace Simon\SecurionPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Config\Checkout\Config;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;

class ChargeDetailsHandler extends AbstractHandler
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * ChargeDetailsHandler constructor.
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
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);

        if (empty($transaction[AdapterInterface::FIELD_CARD])) {
            return;
        }

        $card = $transaction[AdapterInterface::FIELD_CARD];

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        if (!empty($card[Response::LAST_4])) {
            $payment->setData(OrderPaymentInterface::CC_LAST_4, $card[Response::LAST_4]);
            $payment->setData(OrderPaymentInterface::CC_NUMBER_ENC, 'XXXXXXXXXXXX' . $card[Response::LAST_4]);
            $payment->setAdditionalInformation(
                OrderPaymentInterface::CC_NUMBER_ENC,
                'XXXXXXXXXXXX' . $card[Response::LAST_4]
            );
        }
        if (!empty($card[Response::BRAND])) {
            $map = $this->config->getApiCcTypesMapper();
            $ccType = $map[$card[Response::BRAND]] ?? $card[Response::BRAND];
            $payment->setData(OrderPaymentInterface::CC_TYPE, $ccType);
            $payment->setAdditionalInformation(OrderPaymentInterface::CC_TYPE, $ccType);
        }
        if (!empty($card[Response::EXP_YEAR])) {
            $payment->setData(OrderPaymentInterface::CC_EXP_YEAR, $card[Response::EXP_YEAR]);
        }
        if (!empty($card[Response::EXP_MONTH])) {
            $payment->setData(OrderPaymentInterface::CC_EXP_MONTH, $card[Response::EXP_MONTH]);
        }
    }
}
