<?php

namespace Simon\SecurionPay\Model\Event\Processor;

use Simon\SecurionPay\Api\Event\EventProcessorInterface;

abstract class AbstractProcessor implements EventProcessorInterface
{
    /**
     * @var string|null
     */
    protected $_eventType = null;

    /**
     * @inheritDoc
     */
    public function getEventType()
    {
        return $this->_eventType;
    }
}
