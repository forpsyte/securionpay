<?php

namespace Simon\SecurionPay\Controller\Payment\Event;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Psr\Log\LoggerInterface;
use Simon\SecurionPay\Api\Event\EventProcessorInterface;
use Simon\SecurionPay\Gateway\Http\Client\Adapter\AdapterInterface;
use Simon\SecurionPay\Gateway\Http\Data\Response;
use Simon\SecurionPay\Model\Adapter\SecurionPayAdapterFactory;
use Simon\SecurionPay\Model\Event;
use Simon\SecurionPay\Model\EventFactory;

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
     * @var EventProcessorInterface
     */
    protected $eventProcessor;
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var EventFactory
     */
    protected $eventFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Process constructor.
     * @param Context $context
     * @param SecurionPayAdapterFactory $adapterFactory
     * @param JsonFactory $jsonFactory
     * @param Serializer $serializer
     * @param EventProcessorInterface $eventProcessor
     * @param EventFactory $eventFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SecurionPayAdapterFactory $adapterFactory,
        JsonFactory $jsonFactory,
        Serializer $serializer,
        EventProcessorInterface $eventProcessor,
        EventFactory $eventFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->jsonFactory = $jsonFactory;
        $this->serializer = $serializer;
        $this->eventProcessor = $eventProcessor;
        $this->logger = $logger;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {

        try {
            /** @var HttpRequest $request */
            $request = $this->getRequest();
            $requestBody = $this->serializer->unserialize($request->getContent());
            $response = $this->adapterFactory->create()->getEvent([
                AdapterInterface::FIELD_EVENT_ID => $requestBody[AdapterInterface::FIELD_ID]
            ]);
            $eventDetails = $response->getBody();
            /** @var Event $eventModel */
            $eventModel = $this->eventFactory->create();
            $eventModel
                ->setEventId($eventDetails[Response::ID])
                ->setType($eventDetails[Response::CHARGE_TYPE])
                ->setSource($request->getClientIp())
                ->setDetails($response->getBodyAsString());
            $this->eventProcessor->process($eventModel);
            return $this->createResponse([
                'processed' => true,
                'message' => 'Event successfully processed.'
            ])->setHttpResponseCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->createResponse([
                'processed' => false,
                'message' => __('Error processing event: %1', $e->getMessage())
            ])->setHttpResponseCode(\Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }


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
