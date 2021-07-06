<?php

namespace Simon\SecurionPay\Model;


use Magento\Framework\Model\AbstractModel;
use Simon\SecurionPay\Api\Data\EventInterface;

class Event extends AbstractModel implements EventInterface
{
    /**
     * @inheritDoc
     */
    protected $_cacheTag = 'simon_securionpay_payment_event';
    /**
     * @inheritDoc
     */
    protected $_eventPrefix = 'securionpay_payment_event';
    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getEventId()
    {
        return $this->getData(self::EVENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEventId($eventId)
    {
        return $this->setData(self::EVENT_ID, $eventId);
    }

    /**
     * @inheritDoc
     */
    public function getIsProcessed()
    {
        return $this->getData(self::IS_PROCESSED);
    }

    /**
     * @inheritDoc
     */
    public function setIsProcessed($isProcessed)
    {
        return $this->setData(self::IS_PROCESSED, $isProcessed);
    }

    /**
     * @inheritDoc
     */
    public function getDetails()
    {
        return $this->getData(self::DETAILS);
    }

    /**
     * @inheritDoc
     */
    public function setDetails($details)
    {
        return $this->setData(self::DETAILS, $details);
    }

    /**
     * @inheritDoc
     */
    public function getProcessAttempts()
    {
        return $this->getData(self::PROCESS_ATTEMPTS);
    }

    /**
     * @inheritDoc
     */
    public function setProcessAttempts($attempts)
    {
        return $this->setData(self::PROCESS_ATTEMPTS, $attempts);
    }

    /**
     * @inheritDoc
     */
    public function getSource()
    {
        return $this->getData(self::SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        return $this->setData(self::SOURCE, $source);
    }
}
