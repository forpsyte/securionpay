<?php

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use Magento\Customer\Api\Data\CustomerInterface;
use SecurionPay\Request\ChargeRequest;
use SecurionPay\Request\ChargeRequestFactory;
use SecurionPay\SecurionPayGateway;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Data\ResponseFactory;
use Simon\SecurionPay\Observer\DataAssignObserver;

abstract class AbstractClient implements AdapterInterface
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
     * @var ChargeRequestFactory
     */
    protected $chargeRequestFactory;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * SecurionPayAuthorize constructor.
     * @param SecurionPayGateway $securionPayGateway
     * @param ChargeRequestFactory $chargeRequestFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        SecurionPayGateway $securionPayGateway,
        ChargeRequestFactory $chargeRequestFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        $this->securionPayGateway = $securionPayGateway;
        $this->config = $config;
        $this->chargeRequestFactory = $chargeRequestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        $willStoreCard = array_key_exists(DataAssignObserver::STORE_CARD, $data) ?
            $data[DataAssignObserver::STORE_CARD] : null;

        if ($willStoreCard) {
            unset($data[CustomerInterface::EMAIL]);
            unset($data[DataAssignObserver::STORE_CARD]);
        }

        /** @var ChargeRequest $request */
        $request = $this->chargeRequestFactory->create();
        if ($this->willCapture()) {
            $request->captured(true);
        } else {
            $request->captured(false);
        }

        foreach ($data as $field => $value) {
            $request->set($field, $value);
        }

        $result = $this->securionPayGateway->createCharge($request)->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }

    /**
     * @return bool
     */
    abstract public function willCapture();
}
