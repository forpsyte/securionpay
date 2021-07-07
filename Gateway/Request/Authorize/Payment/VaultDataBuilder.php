<?php

namespace Simon\SecurionPay\Gateway\Request\Authorize\Payment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Vault\Model\PaymentToken;
use Simon\SecurionPay\Gateway\Http\Data\Request;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Helper\Currency;

class VaultDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Currency
     */
    protected $currencyHelper;

    /**
     * VaultDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param Json $serializer
     * @param StoreManagerInterface $storeManager
     * @param Currency $currencyHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        Json $serializer,
        StoreManagerInterface $storeManager,
        Currency $currencyHelper
    ) {
        $this->subjectReader = $subjectReader;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        /** @var PaymentToken $token */
        $token = $payment->getExtensionAttributes()->getVaultPaymentToken();
        $tokenDetails = $this->serializer->unserialize($token->getTokenDetails());

        return [
            Request::FIELD_AMOUNT =>  $this->currencyHelper->getMinorUnits(
                $this->subjectReader->readAmount($buildSubject)
            ),
            Request::FIELD_CURRENCY => $this->storeManager->getStore()->getCurrentCurrencyCode(),
            Request::FIELD_CUSTOMER_ID => $tokenDetails[Request::FIELD_CUSTOMER_ID],
            Request::FIELD_CARD => $token->getGatewayToken()
        ];
    }
}
