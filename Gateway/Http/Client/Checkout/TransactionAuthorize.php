<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client\Checkout;

use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Gateway\Http\Client\AbstractTransaction;
use Forpsyte\SecurionPay\Gateway\Http\Data\Request;
use Forpsyte\SecurionPay\Gateway\Http\Data\ResponseFactory;
use Forpsyte\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

class TransactionAuthorize extends AbstractTransaction
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * TransactionAuthorize constructor.
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param SecurionPayAdapterFactory $adapterFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        SecurionPayAdapterFactory $adapterFactory,
        ResponseFactory $responseFactory
    ) {
        parent::__construct($logger, $customLogger, $adapterFactory);
        $this->responseFactory = $responseFactory;
    }
    /**
     * @inheritDoc
     */
    protected function process(array $data)
    {
        $response = $this->responseFactory->create();
        $response->setStatus(200);
        $response->setBody([
            Request::FIELD_ID => $data[Request::FIELD_CHARGE_ID],
            Request::FIELD_CUSTOMER_ID => $data[Request::FIELD_CUSTOMER_ID]
        ]);
        return $response;
    }
}
