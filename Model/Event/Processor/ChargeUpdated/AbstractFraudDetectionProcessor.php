<?php

namespace Forpsyte\SecurionPay\Model\Event\Processor\ChargeUpdated;

use Forpsyte\SecurionPay\Api\Data\EventInterface;
use Forpsyte\SecurionPay\Api\EventRepositoryInterface;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Model\Event\Processor\AbstractProcessor;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;

abstract class AbstractFraudDetectionProcessor extends AbstractProcessor
{
    /**
     * @var string
     */
    protected $_eventType = 'CHARGE_UPDATED';

    /**
     * @var array
     */
    protected $_highRiskStatuses = [
        Response::FRAUD_STATUS_FRAUDULENT,
        Response::FRAUD_STATUS_SUSPICIOUS
    ];
    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;
    /**
     * @var EventRepositoryInterface
     */
    protected $eventRepository;
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var InvoiceManagementInterface
     */
    protected $invoiceManagement;
    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;
    /**
     * @var Order\StatusResolver
     */
    protected $statusResolver;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ChargeUpdated constructor.
     * @param OrderPaymentRepositoryInterface $orderPaymentRepository
     * @param EventRepositoryInterface $eventRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param InvoiceManagementInterface $invoiceManagement
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionFactory $transactionFactory
     * @param Order\StatusResolver $statusResolver
     * @param Config $config
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        EventRepositoryInterface $eventRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        InvoiceManagementInterface $invoiceManagement,
        TransactionRepositoryInterface $transactionRepository,
        TransactionFactory $transactionFactory,
        Order\StatusResolver $statusResolver,
        Config $config,
        Serializer $serializer,
        LoggerInterface $logger
    ) {
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->eventRepository = $eventRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->invoiceManagement = $invoiceManagement;
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory = $transactionFactory;
        $this->statusResolver = $statusResolver;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param EventInterface $event
     * @return OrderPaymentInterface|null
     */
    protected function getPayment(EventInterface $event)
    {
        $eventData = $this->getEventData($event);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderPaymentInterface::CC_TRANS_ID, $eventData[Response::ID])
            ->create();
        $results = $this->orderPaymentRepository->getList($searchCriteria);
        if ($results->getTotalCount() == 0) {
            return null;
        }
        /** @var Payment $payment */
        $items = $results->getItems();
        return array_pop($items);
    }

    /**
     * @param EventInterface $event
     * @return array
     */
    protected function getEventData(EventInterface $event)
    {
        $details = $this->serializer->unserialize($event->getDetails());
        return $details[Response::DATA];
    }

    /**
     * Check if capture transaction already exists
     *
     * @param OrderPaymentInterface $payment
     * @return bool
     */
    protected function isExistsCaptureTransaction(OrderPaymentInterface $payment)
    {
        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('payment_id')
                    ->setValue($payment->getId())
                    ->create(),
            ]
        );

        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('txn_type')
                    ->setValue(TransactionInterface::TYPE_CAPTURE)
                    ->create(),
            ]
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();

        $count = $this->transactionRepository->getList($searchCriteria)->getTotalCount();
        return (boolean) $count;
    }
}
