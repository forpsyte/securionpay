<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\Request\RefundRequest;
use SecurionPay\Request\RefundRequestFactory;
use SecurionPay\SecurionPayGateway;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayRefund implements AdapterInterface
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
     * @var RefundRequestFactory
     */
    protected $refundRequestFactory;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param RefundRequestFactory $refundRequestFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        RefundRequestFactory $refundRequestFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->refundRequestFactory = $refundRequestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        /** @var RefundRequest $request */
        $request = $this->refundRequestFactory->create();
        foreach ($data as $field => $value) {
            $request->set($field, $value);
        }
        $result = $this->securionPayGateway->refundCharge($request)->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
