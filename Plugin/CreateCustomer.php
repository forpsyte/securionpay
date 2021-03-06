<?php
namespace Forpsyte\SecurionPay\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use SecurionPay\Request\CustomerRequestFactory;
use SecurionPay\SecurionPayGateway;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Client\Adapter\AbstractClient;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Observer\DataAssignObserver;

class CreateCustomer
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
     * @var CustomerRequestFactory
     */
    protected $customerRequestFactory;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CustomerRequestFactory $customerRequestFactory
     * @param Config $config
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CustomerRequestFactory $customerRequestFactory,
        Config $config,
        Session $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->customerRequestFactory = $customerRequestFactory;
        $this->session = $session;
        $this->customerRepository = $customerRepository;
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

        if (!isset($spCustomerId)) {
            $request = $this->customerRequestFactory->create();
            $request->card($response[Request::FIELD_ID]);
            $request->email($data[CustomerInterface::EMAIL]);
            $customerResponse = $this->securionPayGateway->createCustomer($request)->toArray();
            $body = $response->getBody();
            $body[Request::FIELD_CUSTOMER_ID] = $customerResponse[Request::FIELD_ID];
            $body[PaymentTokenInterface::GATEWAY_TOKEN] = $customerResponse[Request::FIELD_DEFAULT_CARD_ID];
            $response->setBody($body);
        }
        return $response;
    }
}
