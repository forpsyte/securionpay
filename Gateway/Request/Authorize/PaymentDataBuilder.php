<?php

namespace Simon\SecurionPay\Gateway\Request\Authorize;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Helper\Currency;
use Simon\SecurionPay\Observer\DataAssignObserver;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Currency
     */
    protected $currencyHelper;

    /**
     * PaymentDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param StoreManagerInterface $storeManager
     * @param Currency $currencyHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        StoreManagerInterface $storeManager,
        Currency $currencyHelper
    ) {
        $this->subjectReader = $subjectReader;
        $this->storeManager = $storeManager;
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        return [
            AdapterInterface::FIELD_AMOUNT =>  $this->currencyHelper->getMinorUnits(
                $this->subjectReader->readAmount($buildSubject)
            ),
            AdapterInterface::FIELD_CURRENCY => $this->storeManager->getStore()->getCurrentCurrencyCode(),
            AdapterInterface::FIELD_CARD => $payment->getAdditionalInformation(PaymentTokenInterface::GATEWAY_TOKEN),
        ];
    }
}
