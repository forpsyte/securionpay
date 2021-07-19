<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\Exception\SecurionPayException;
use SecurionPay\Request\CardRequestFactory;
use SecurionPay\SecurionPayGateway;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayCreateCard implements AdapterInterface
{
    /**
     * @var SecurionPayGateway
     */
    protected $securionPayGateway;
    /**
     * @var CardRequestFactory
     */
    protected $cardRequestFactory;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var Config
     */
    protected $config;

    /**
     * SecurionPayCreateCard constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CardRequestFactory $cardRequestFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CardRequestFactory $cardRequestFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->cardRequestFactory = $cardRequestFactory;
        $this->responseFactory = $responseFactory;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        $request = $this->cardRequestFactory->create();
        foreach ($data as $field => $value) {
            $request->set($field, $value);
        }
        $result = $this->securionPayGateway->createCard($request)->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
