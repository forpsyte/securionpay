<?php

namespace Forpsyte\SecurionPay\Model\Event\Processor\ChargeUpdated;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Payment\Model\MethodInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Forpsyte\SecurionPay\Api\Data\EventInterface;
use Forpsyte\SecurionPay\Api\EventRepositoryInterface;
use Forpsyte\SecurionPay\Gateway\Config\Config;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Model\Adminhtml\Source\FraudDetectionAction;
use Forpsyte\SecurionPay\Model\Event\Processor\AbstractProcessor;

class FraudDetectionProcessor extends AbstractProcessor
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
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
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
     * ChargeUpdated constructor.
     * @param OrderPaymentRepositoryInterface $orderPaymentRepository
     * @param EventRepositoryInterface $eventRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Order\StatusResolver $statusResolver
     * @param Config $config
     * @param Serializer $serializer
     */
    public function __construct(
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        EventRepositoryInterface $eventRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Order\StatusResolver $statusResolver,
        Config $config,
        Serializer $serializer
    ) {
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->eventRepository = $eventRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->statusResolver = $statusResolver;
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function process(EventInterface $event)
    {
        $details = $this->serializer->unserialize($event->getDetails());
        $eventData = $details[Response::DATA];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderPaymentInterface::CC_TRANS_ID, $eventData[Response::ID])
            ->create();
        $results = $this->orderPaymentRepository->getList($searchCriteria);
        if ($results->getTotalCount() == 0) {
            return;
        }
        /** @var Payment $payment */
        $items = $results->getItems();
        $payment = array_pop($items);
        $order = $payment->getOrder();

        if ($order->getState() == Order::STATE_PROCESSING) {
            return;
        }

        $fraudDetails = $eventData[Response::FRAUD_DETAILS];
        $status = $fraudDetails[Response::FRAUD_DETAIL_STATUS];
        $paymentAction = $this->getActionText();

        if (in_array($status, $this->_highRiskStatuses)) {
            $message = "Order is suspended as its {$paymentAction} amount %1 is suspected to be fraudulent.";
            $order->setStatus(Order::STATUS_FRAUD);
        }

        if ($status == Response::FRAUD_STATUS_IN_UNKNOWN) {
            $message = "Order will remain suspended as its {$paymentAction} amount %1 cannot be verified.";
        }

        if ($status == Response::FRAUD_STATUS_SAFE) {
            $message = "Order is approved as its {$paymentAction} amount %1 has been verified.";
            $order->setState(Order::STATE_PROCESSING);
            $orderStatus = $this->statusResolver->getOrderStatusByState($order, Order::STATE_PROCESSING);
            $order->setStatus($orderStatus);
        }

        if (!isset($message)) {
            return;
        }

        $amount = $order->getGrandTotal();
        $message = __($message, $order->getBaseCurrency()->formatTxt($amount));
        $order->addCommentToStatusHistory($message, false, false);
        $this->orderRepository->save($order);
        $event->setIsProcessed(true);
        return;
    }

    /**
     * @inheritDoc
     */
    public function canProcess(EventInterface $event)
    {
        return !$this->eventRepository->exists($event) &&
            $event->getType() == $this->getEventType() &&
            $this->config->getFraudDetectionAction() == FraudDetectionAction::OPTION_AFTER_CHECKOUT;
    }

    /**
     * @return string
     */
    private function getActionText()
    {
        if ($this->config->getPaymentAction() == MethodInterface::ACTION_AUTHORIZE) {
            return 'authorizing';
        } else {
            return 'capturing';
        }
    }
}
