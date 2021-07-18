<?php

namespace Forpsyte\SecurionPay\Gateway\Http\Client;

use Exception;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

/**
 * Gateway client parent class
 *
 * Takes the provider-specific arguments and performs a low-level
 * call to the provider.
 */
abstract class AbstractTransaction implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * @var SecurionPayAdapterFactory
     */
    protected $adapterFactory;

    /**
     * AbstractTransaction constructor.
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param SecurionPayAdapterFactory $adapterFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        SecurionPayAdapterFactory $adapterFactory
    ) {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $log = [
            'request' => $data,
            'client' => static::class
        ];
        $response['object'] = [];

        try {
            $response['object'] = $this->process($data);
        } catch (Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = (array) $response['object'];
            $this->customLogger->debug($log);
        }

        return $response;
    }

    /**
     * @param array $data
     * @return Response
     * @throws Exception
     */
    abstract protected function process(array $data);
}
