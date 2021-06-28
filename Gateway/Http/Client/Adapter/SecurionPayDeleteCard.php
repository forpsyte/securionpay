<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Gateway\Http\Client\Adapter;

use SecurionPay\SecurionPayGateway;
use Simon\SecurionPay\Gateway\Config\Config;
use Simon\SecurionPay\Gateway\Http\Data\ResponseFactory;

/**
 * Delete card from the payment provider system
 */
class SecurionPayDeleteCard implements AdapterInterface
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
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     *
     * /**
     * SecurionPayAuthorize constructor.
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
        $this->config = $config;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        $this->securionPayGateway->setPrivateKey($this->config->getSecretKey());
        $result = $this->securionPayGateway->deleteCard(
            $data[AdapterInterface::FIELD_CUSTOMER_ID],
            $data[AdapterInterface::FIELD_CARD_ID]
        )->toArray();
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody($result);
        return $response;
    }
}
