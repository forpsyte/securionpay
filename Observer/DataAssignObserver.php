<?php

namespace Simon\SecurionPay\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote\Payment;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Model\Ui\ConfigProvider;

/**
 * Adds additional data to quote payment.
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    const STORE_CARD = 'store_card';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        PaymentTokenInterface::GATEWAY_TOKEN,
        CustomerInterface::EMAIL,
        OrderPaymentInterface::CC_TYPE,
        OrderPaymentInterface::CC_NUMBER_ENC,
        OrderPaymentInterface::CC_LAST_4,
        AdapterInterface::FIELD_CHARGE_ID,
        AdapterInterface::FIELD_CUSTOMER_ID,
        self::STORE_CARD
    ];

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        /** @var Payment $paymentInfo */
        $paymentInfo = $this->readPaymentModelArgument($observer);

        if (!empty($additionalData[OrderPaymentInterface::CC_NUMBER_ENC])) {
            $paymentInfo->setData(
                OrderPaymentInterface::CC_LAST_4,
                substr($additionalData[OrderPaymentInterface::CC_NUMBER_ENC], -4)
            );
            $paymentInfo->setData(
                OrderPaymentInterface::CC_NUMBER_ENC,
                $additionalData[OrderPaymentInterface::CC_NUMBER_ENC]
            );
        }
        if (!empty($additionalData[OrderPaymentInterface::CC_TYPE])) {
            $paymentInfo->setData(
                OrderPaymentInterface::CC_TYPE,
                $additionalData[OrderPaymentInterface::CC_TYPE]
            );
        }
        if (!empty($additionalData[OrderPaymentInterface::CC_EXP_YEAR])) {
            $paymentInfo->setData(
                OrderPaymentInterface::CC_EXP_YEAR,
                $additionalData[OrderPaymentInterface::CC_EXP_YEAR]
            );
        }
        if (!empty($additionalData[OrderPaymentInterface::CC_EXP_MONTH])) {
            $paymentInfo->setData(
                OrderPaymentInterface::CC_EXP_MONTH,
                $additionalData[OrderPaymentInterface::CC_EXP_MONTH]
            );
        }

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
