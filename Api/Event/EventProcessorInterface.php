<?php

namespace Forpsyte\SecurionPay\Api\Event;

use Forpsyte\SecurionPay\Api\Data\EventInterface;

interface EventProcessorInterface
{

    /**
     * @param EventInterface $event
     * @return void
     */
    public function process(EventInterface $event);

    /**
     * Determines if the event can be processed.
     *
     * @param EventInterface $event
     * @return bool
     */
    public function canProcess(EventInterface $event);

    /**
     * Get the event type for the processor.
     *
     * @return string|null
     */
    public function getEventType();
}
