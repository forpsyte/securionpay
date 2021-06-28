<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simon\SecurionPay\Model\Adapter;

use Psr\Log\LoggerInterface;
use SecurionPay\Exception\SecurionPayException;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\CommandStrategy;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Gateway\Http\Data\ResponseFactory;

/**
 * Short description...
 *
 * Long description
 * Broken down into several lines
 *
 * License notice...
 */
class SecurionPayAdapter
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var CommandStrategy
     */
    protected $commandStrategy;

    /**
     * SecurionPayAdapter constructor.
     * @param LoggerInterface $logger
     * @param CommandStrategy $commandStrategy
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        LoggerInterface $logger,
        CommandStrategy $commandStrategy,
        ResponseFactory $responseFactory
    ) {
        $this->logger = $logger;
        $this->responseFactory = $responseFactory;
        $this->commandStrategy = $commandStrategy;
    }

    /**
     * @param array $data
     * @return Response
     */
    public function authorize(array $data)
    {
        return $this->placeRequest($data, 'authorize');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function capture(array $data)
    {
        return $this->placeRequest($data, 'capture');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function sale(array $data)
    {
        return $this->placeRequest($data, 'sale');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function refund(array $data)
    {
        return $this->placeRequest($data, 'refund');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function deleteCard(array $data)
    {
        return $this->placeRequest($data, 'delete_card');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function getCharge(array $data)
    {
        return $this->placeRequest($data, 'get_charge');
    }

    /**
     * @param array $data
     * @return Response
     */
    public function getCheckout(array $data)
    {
        return $this->placeRequest($data, 'get_checkout');
    }

    /**
     * @param array $data
     * @param string $command
     * @return Response
     */
    private function placeRequest(array $data, $command)
    {
        try {
            $data[CommandStrategy::STRATEGY] = $command;
            return $this->commandStrategy->send($data);
        } catch (SecurionPayException $e) {
            return $this->handle($e);
        }
    }

    /**
     * @param SecurionPayException $exception
     * @return Response
     */
    private function handle($exception)
    {
        $this->logger->critical($exception->getMessage());
        $response = $this->responseFactory->create();
        $response->setStatus(402);
        $response->setBody([
            'code' => $exception->getCode(),
            'type' => $exception->getType(),
            'message' => $exception->getMessage()
        ]);
        return $response;
    }
}
