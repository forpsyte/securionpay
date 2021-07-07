<?php
namespace Simon\SecurionPay\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use SecurionPay\Request\CardRequestFactory;
use SecurionPay\SecurionPayGateway;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AbstractClient;
use Simon\SecurionPay\Gateway\Http\Data\Request;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Observer\DataAssignObserver;

class CreateCard
{
    /**
     * @var SecurionPayGateway
     */
    protected $securionPayGateway;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var CardRequestFactory
     */
    protected $cardRequestFactory;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CardRequestFactory $cardRequestFactory
     * @param Config $config
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CardRequestFactory $cardRequestFactory,
        Config $config,
        Session $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->cardRequestFactory = $cardRequestFactory;
    }

    public function afterSend(
        AbstractClient $subject,
        Response $response,
        array $data
    ) {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());

        if (empty($data[DataAssignObserver::STORE_CARD])) {
            return $response;
        }

        if ($this->session->isLoggedIn()) {
            $customer = $this->customerRepository->getById($this->session->getCustomerId());
            $extensionAttributes = $customer->getExtensionAttributes();
            $spCustomerId = $extensionAttributes->getSecurionpayCustomerId();
        }

        if (isset($spCustomerId)) {
            $request = $this->cardRequestFactory->create();
            $request->customerId($spCustomerId);
            $request->id($response[Request::FIELD_ID]);
            $cardResponse = $this->securionPayGateway->createCard($request)->toArray();
            $body = $response->getBody();
            $body[Request::FIELD_CUSTOMER_ID] = $cardResponse[Request::FIELD_CUSTOMER_ID];
            $body[PaymentTokenInterface::GATEWAY_TOKEN] = $cardResponse[Request::FIELD_ID];
            $response->setBody($body);
        }
        return $response;
    }
}
