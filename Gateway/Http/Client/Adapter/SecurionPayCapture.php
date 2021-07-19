<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\Request\CaptureRequest;
use SecurionPay\Request\CaptureRequestFactory;
use SecurionPay\SecurionPayGateway;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\ResponseFactory;

class SecurionPayCapture implements AdapterInterface
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
     * @var CaptureRequestFactory
     */
    protected $captureRequestFactory;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param CaptureRequestFactory $captureRequestFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        CaptureRequestFactory $captureRequestFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->captureRequestFactory = $captureRequestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        /** @var CaptureRequest $request */
        $request = $this->captureRequestFactory->create();
        foreach ($data as $field => $value) {
            $request->set($field, $value);
        }
        $result = $this->securionPayGateway->captureCharge($request)->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
