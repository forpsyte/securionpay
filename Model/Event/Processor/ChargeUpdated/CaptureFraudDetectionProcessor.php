<?php

namespace Forpsyte\SecurionPay\Model\Event\Processor\ChargeUpdated;

use Forpsyte\SecurionPay\Api\Data\EventInterface;
use Forpsyte\SecurionPay\Model\Adminhtml\Source\FraudDetectionAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

class CaptureFraudDetectionProcessor extends AbstractFraudDetectionProcessor
{
    /**
     * @inheritDoc
     */
    public function process(EventInterface $event)
    {
        /** @var Payment $payment */
        $payment = $this->getPayment($event);
        /** @var Order\Invoice $invoice */
        $invoice = $payment->getOrder()->getInvoiceCollection()->getFirstItem();
        $invoice = $this->invoiceRepository->get($invoice->getEntityId());
        /** @var Order $order */
        $order = $payment->getOrder();

        if ($order->getState() == Order::STATE_PROCESSING) {
            return;
        }
        try {
            $this->invoiceManagement->setCapture($invoice->getEntityId());
            $invoice->getOrder()->setIsInProcess(true);
            $dbTransaction = $this->transactionFactory->create();
            $dbTransaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return;
        }

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
            $this->isExistsCaptureTransaction($this->getPayment($event)) &&
            $this->config->getFraudDetectionAction() == FraudDetectionAction::OPTION_AFTER_CHECKOUT;
    }
}
