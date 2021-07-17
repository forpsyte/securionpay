<?php

namespace Simon\SecurionPay\Model;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use Magento\Vault\Model\PaymentTokenFactory;
use Simon\SecurionPay\Api\CustomerRepositoryInterface;
use Simon\SecurionPay\Gateway\Http\Data\Request;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;
use Simon\SecurionPay\Model\Ui\ConfigProvider;

class TokenManagement
{
    /**
     * @var PaymentTokenFactory
     */
    protected $paymentTokenFactory;
    /**
     * @var PaymentTokenRepositoryInterface
     */
    protected $paymentTokenRepository;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var SecurionPayAdapterFactory
     */
    protected $securionPayAdapterFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * TokenManagement constructor.
     * @param PaymentTokenFactory $paymentTokenFactory
     * @param PaymentTokenRepositoryInterface $paymentTokenRepository
     * @param EncryptorInterface $encryptor
     * @param Serializer $serializer
     * @param SecurionPayAdapterFactory $securionPayAdapterFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        PaymentTokenFactory $paymentTokenFactory,
        PaymentTokenRepositoryInterface $paymentTokenRepository,
        EncryptorInterface $encryptor,
        Serializer $serializer,
        SecurionPayAdapterFactory $securionPayAdapterFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory
    ) {
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentTokenRepository = $paymentTokenRepository;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->securionPayAdapterFactory = $securionPayAdapterFactory;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param CustomerInterface $customer
     * @param array $tokenDetails
     * @return PaymentTokenInterface
     * @throws Exception
     */
    public function createToken(CustomerInterface $customer, array $tokenDetails)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        $spCustomerId = $extensionAttributes->getSecurionpayCustomerId();

        if ($spCustomerId) {
            $response = $this->securionPayAdapterFactory->create()->createCard([
                Request::FIELD_CUSTOMER_ID => $spCustomerId,
                Request::FIELD_ID => $tokenDetails[PaymentTokenInterface::GATEWAY_TOKEN]
            ])->getBody();
            $token = $response[Request::FIELD_ID];
            $customerId = $response[Request::FIELD_CUSTOMER_ID];
        } else {
            $response = $this->securionPayAdapterFactory->create()->createCustomer([
                Request::FIELD_CARD => $tokenDetails[PaymentTokenInterface::GATEWAY_TOKEN],
                CustomerInterface::EMAIL => $customer->getEmail()
            ])->getBody();
            $token = $response[Request::FIELD_DEFAULT_CARD_ID];
            $customerId = $response[Request::FIELD_ID];
            /** @var Customer $spCustomer */
            $spCustomer = $this->customerFactory->create();
            $spCustomer
                ->setCustomerId($customer->getId())
                ->setSpCustomerId($customerId);
            $this->customerRepository->save($spCustomer);
        }

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($this->getExpirationDate($tokenDetails));
        $paymentToken->setCustomerId($customer->getId());
        $paymentToken->setIsActive(true);
        $paymentToken->setIsVisible(true);
        $paymentToken->setPaymentMethodCode(ConfigProvider::CODE);
        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'type' => $tokenDetails['cc_type'],
            'maskedCC' => $tokenDetails['cc_number'],
            'expirationDate' => $tokenDetails['cc_exp_month'] . '/' . $tokenDetails['cc_exp_year'],
            'customerId' => $customerId,
            'created' => $response[Response::CREATED]
        ]));
        $paymentToken->setPublicHash($this->generatePublicHash($paymentToken));
        return $this->paymentTokenRepository->save($paymentToken);
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
     * @param array $params
     * @return string
     * @throws Exception
     */
    private function getExpirationDate(array $params)
    {
        $expDate = new \DateTime(
            $params['cc_exp_year']
            . '-'
            . $params['cc_exp_month']
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
     * Generate vault payment public hash
     *
     * @param PaymentTokenInterface $paymentToken
     * @return string
     */
    private function generatePublicHash(PaymentTokenInterface $paymentToken)
    {
        $hashKey = $paymentToken->getGatewayToken();
        if ($paymentToken->getCustomerId()) {
            $hashKey .= $paymentToken->getCustomerId();
        }

        $hashKey .= $paymentToken->getPaymentMethodCode()
            . $paymentToken->getType()
            . $paymentToken->getTokenDetails();

        return $this->encryptor->getHash($hashKey);
    }
}
