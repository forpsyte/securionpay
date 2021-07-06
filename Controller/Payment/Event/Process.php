<?php

namespace Simon\SecurionPay\Controller\Payment\Event;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;

class Process extends Action implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var SecurionPayAdapterFactory
     */
    protected $adapterFactory;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Process constructor.
     * @param Context $context
     * @param SecurionPayAdapterFactory $adapterFactory
     * @param JsonFactory $jsonFactory
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SecurionPayAdapterFactory $adapterFactory,
        JsonFactory $jsonFactory,
        Serializer $serializer,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->jsonFactory = $jsonFactory;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $body = $this->serializer->unserialize($request->getContent());
        $this->logger->debug('Incoming event: ', $body);
        return $this->createResponse([
            'processed' => true,
            'message' => 'Event successfully processed.'
        ])->setHttpResponseCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return new InvalidRequestException(
            $this->createResponse(['error' => true]),
            [new Phrase('Invalid event processing request.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        if (!$request->isSecure() || !$request->isPost()) {
            return false;
        }

        return true;
    }

    /**
     * @param array|string $data
     * @return Json
     */
    private function createResponse($data)
    {
        $result = $this->jsonFactory->create();
        $result->setData($data);
        return $result;
    }
}
