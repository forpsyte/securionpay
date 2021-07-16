<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\Exception\SecurionPayException;
use SecurionPay\Request\CustomerRequestFactory;
use SecurionPay\SecurionPayGateway;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayCreateCustomer implements AdapterInterface
{
    /**
     * @var SecurionPayGateway
     */
    protected $securionPayGateway;
    /**
     * @var CustomerRequestFactory
     */
    protected $customerRequestFactory;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var Config
     */
    protected $config;

    /**
     * SecurionPayCreateCustomer constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CustomerRequestFactory $customerRequestFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CustomerRequestFactory $customerRequestFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->customerRequestFactory = $customerRequestFactory;
        $this->responseFactory = $responseFactory;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        $request = $this->customerRequestFactory->create();
        foreach ($data as $field => $value) {
            $request->set($field, $value);
        }
        $result = $this->securionPayGateway->createCustomer($request)->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
