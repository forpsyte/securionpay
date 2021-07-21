<?php

namespace Forpsyte\SecurionPay\Model\Event\Processor\ChargeUpdated;

use Forpsyte\SecurionPay\Api\Data\EventInterface;
use Forpsyte\SecurionPay\Gateway\Http\Data\Response;
use Forpsyte\SecurionPay\Model\Adminhtml\Source\FraudDetectionAction;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

class AuthorizationFraudDetectionProcessor extends AbstractFraudDetectionProcessor
{
    /**
     * @inheritDoc
     */
    public function process(EventInterface $event)
    {
        $eventData = $this->getEventData($event);
        $payment = $this->getPayment($event);
        $order = $payment->getOrder();

        if ($order->getState() == Order::STATE_PROCESSING) {
            return;
        }

        $fraudDetails = $eventData[Response::FRAUD_DETAILS];
        $status = $fraudDetails[Response::FRAUD_DETAIL_STATUS];

        if (in_array($status, $this->_highRiskStatuses)) {
            $message = "Order is suspended as its authorizing amount %1 is suspected to be fraudulent.";
            $order->setStatus(Order::STATUS_FRAUD);
        }

        if ($status == Response::FRAUD_STATUS_IN_UNKNOWN) {
            $message = "Order will remain suspended as its authorizing amount %1 cannot be verified.";
        }

        if ($status == Response::FRAUD_STATUS_SAFE) {
            $message = "Order is approved as its authorizing amount %1 has been verified.";
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
        $this->addPaymentNotification($event);
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
            $this->getPayment($event) != null &&
            !$this->isExistsCaptureTransaction($this->getPayment($event)) &&
            $this->config->getFraudDetectionAction() == FraudDetectionAction::OPTION_AFTER_CHECKOUT;
    }
}
