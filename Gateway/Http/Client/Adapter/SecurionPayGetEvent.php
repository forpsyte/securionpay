<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\SecurionPayGateway;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Data\Request;
use Simon\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayGetEvent implements AdapterInterface
{
    /**
     * @var SecurionPayGateway
     */
    protected $securionPayGateway;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var Config
     */
    private $config;

    /**
     * SecurionPayGetCharge constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->responseFactory = $responseFactory;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        $result = $this->securionPayGateway
            ->retrieveEvent($data[Request::FIELD_EVENT_ID])
            ->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
