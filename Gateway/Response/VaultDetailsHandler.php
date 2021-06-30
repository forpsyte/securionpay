<?php

namespace Simon\SecurionPay\Gateway\Response;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\SubjectReader;
use Simon\SecurionPay\Observer\DataAssignObserver;

/**
 * Adds customer payment information to the Magento Vault.
 */
class VaultDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var PaymentTokenFactoryInterface
     */
    protected $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    protected $paymentExtensionFactory;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var PaymentTokenRepositoryInterface
     */
    protected $paymentTokenRepository;

    /**
     * VaultDetailsHandler constructor.
     * @param SubjectReader $subjectReader
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     * @param PaymentTokenFactoryInterface $paymentTokenFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PaymentTokenRepositoryInterface $paymentTokenRepository
     * @param Json $serializer
     */
    public function __construct(
        SubjectReader $subjectReader,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        PaymentTokenFactoryInterface $paymentTokenFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PaymentTokenRepositoryInterface $paymentTokenRepository,
        Json $serializer = null
    ) {
        $this->subjectReader = $subjectReader;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->serializer  = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->paymentTokenRepository = $paymentTokenRepository;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        // add vault payment token entity to extension attributes
        $paymentToken = $this->getVaultPaymentToken($payment, $transaction);
        if (null !== $paymentToken) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    /**
     * Get vault payment token entity
     *
     * @param Payment $payment
     * @param Response $transaction
     * @return PaymentTokenInterface|null
     * @throws Exception
     */
    protected function getVaultPaymentToken(Payment $payment, Response $transaction)
    {
        // Check token existing in gateway response
        $token = isset($transaction[PaymentTokenInterface::GATEWAY_TOKEN]) ?
            $transaction[PaymentTokenInterface::GATEWAY_TOKEN] : null;
        if (empty($token)) {
            return null;
        }

        // Check for duplicate token
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(PaymentTokenInterface::GATEWAY_TOKEN, $token)
            ->addFilter(PaymentTokenInterface::PAYMENT_METHOD_CODE, $payment->getMethod())
            ->addFilter(PaymentTokenInterface::CUSTOMER_ID, $payment->getOrder()->getCustomerId())
            ->create();
        $result = $this->paymentTokenRepository->getList($searchCriteria)->getTotalCount();
        if ($result > 0) {
            return null;
        }

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($this->getExpirationDate($payment));

        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'type' => $payment->getAdditionalInformation(OrderPaymentInterface::CC_TYPE),
            'maskedCC' => $payment->getAdditionalInformation(OrderPaymentInterface::CC_NUMBER_ENC),
            'expirationDate' => $payment->getCcExpMonth() .
                '/' . $payment->getCcExpYear(),
            'customerId' => $transaction[AdapterInterface::FIELD_CUSTOMER_ID]
        ]));

        return $paymentToken;
    }

    /**
     * @param Payment $payment
     * @return string
     * @throws Exception
     */
    private function getExpirationDate(Payment $payment)
    {
        $expDate = new \DateTime(
            $payment->getCcExpYear()
            . '-'
            . $payment->getCcExpMonth()
            . '-'
            . '01'
            . ' '
            . '00:00:00',
            new \DateTimeZone('UTC')
        );
        $expDate->add(new \DateInterval('P1M'));
        return $expDate->format('Y-m-d 00:00:00');
    }

    /**
     * Convert payment token details to JSON
     * @param array $details
     * @return string
     */
    private function convertDetailsToJSON($details)
    {
        $json = $this->serializer->serialize($details);
        return $json ? $json : '{}';
    }

    /**
     * Get payment extension attributes
     *
     * @param Payment $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(Payment $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
